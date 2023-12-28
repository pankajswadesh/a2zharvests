<?php

namespace App\Http\Controllers\Frontend;

use App\Mail\BecomeSellerMail;
use App\Mail\ContactUsMail;
use App\Model\AboutUsModel;
use App\Model\BecomeSellerModel;
use App\Model\BrandModel;
use App\Model\CartModel;
use App\Model\CategoryModel;
use App\Model\ContactUsMessagesModel;
use App\Model\ContactUsModel;
use App\Model\DeliverySettingModel;
use App\Model\DeliverySlotsModel;
use App\Model\DepartmentModel;
use App\Model\DiscountModel;
use App\Model\FaqModel;
use App\Model\ImageBannerModel;
use App\Model\OrderModel;
use App\Model\ProductImageModel;
use App\Model\ProductModel;
use App\Model\RecentSearchesModel;
use App\Model\SettingModel;
use App\Model\ShippingModel;
use App\Model\ShopDetailsModel;
use App\Model\SliderModel;
use App\Model\SubscriberModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\TaxValueModel;
use App\Model\TermsConditionModel;
use App\Model\TextBannerModel;
use App\Model\UnitModel;
use App\repo\datavalue;
use App\repo\Response;
use App\User;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Mail;

class PageController extends Controller
{
    public function locationSet(Request $request)
    {
       
        $msg = [
            'latitude.required' => 'Please use your current location or select address from google suggestion.',
        ];
        $this->validate($request, [
            'latitude' => 'required',
        ], $msg);
        \Session::put('location', $request->all());
        if (Auth::check()) {
            User::where("id", Auth::user()->id)->update([
                'latitude' => $request->get('latitude'),
                'longitude' => $request->get('longitude'),
                'location' => $request->get('address'),
            ]);
        }
        return redirect()->route('home');
    }
    public function updateLocation()
    {
        return view('frontend.pages.location-set');
    }
    public function index()
    {
     
        $sliders = SliderModel::where('status', 'Active')->get();
        $text_banner = TextBannerModel::where('status', 'Active')->get();
        $image_banner = ImageBannerModel::where('status', 'Active')->where('type', 'Banner')->get();
        $add_images = ImageBannerModel::where('status', 'Active')->where('type', 'Advertisement')->get();
        $lat_long = datavalue::getLatLong();
        $locationCheck = $lat_long["status"];
        $distance = SettingModel::where('id', 2)->value('value');
        //dd($locationCheck);
        //try {
            $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
            $product_id = DB::table('order_details')
                ->groupBy('product_id')
                ->select(DB::raw('count(product_id) as count'), 'order_details.product_id')
                ->orderBy('count', 'desc')
                ->pluck('product_id')
                ->toArray();
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')
                ->whereIn('products.id', $product_id)
                ->groupBy('products.id')
                ->select(
                    'supplier_products.*',
                    'products.id as product_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.brand_id',
                    'products.category_id',
                    'products.product_name',
                    'products.url',
                    'products.print_name',
                    'products.product_image',
                    'products.product_description',
                    'products.product_company',
                    'products.unit_id',
                    'products.department_id',
                    'products.tax_id',
                    'discounts.discount_name',
                    'units.unit_name'
                );
            $products = $products->orderBy('supplier_products.status')->paginate(10);
            $categories_list = CategoryModel::where('parent_id', 0)->where('in_home', 'Yes')->where('status', 'Active')->orderBy('priority', 'asc')->get();
            $recent_searches = RecentSearchesModel::latest()->take(10)->get();
            $pro_ids = ProductModel::where('status', 'Active')->where(function ($query) use ($recent_searches) {
                foreach ($recent_searches as $row) {
                    $query->orWhere('product_name', 'LIKE', '%' . $row->search_query . '%');
                }
            })->pluck('id')->toArray();
            $recent_products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')
                ->whereIn('products.id', $pro_ids)
                ->groupBy('products.id')
                ->select(
                    'supplier_products.*',
                    'products.id as product_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.brand_id',
                    'products.category_id',
                    'products.product_name',
                    'products.url',
                    'products.print_name',
                    'products.product_image',
                    'products.product_description',
                    'products.product_company',
                    'products.unit_id',
                    'products.department_id',
                    'products.tax_id',
                    'discounts.discount_name',
                    'units.unit_name'
                )->take(10)->get();
            return view('frontend.pages.index', compact('sliders', 'text_banner', 'image_banner', 'add_images', 'products', 'categories_list', 'recent_products', 'locationCheck'));
        // } catch (Exception $e) {
        //     dd('ok');
        //     return view('frontend.errors.500');
        // }
    }

    public static function getProductByCategory($category_id, $lat = null, $lng = null, $token = null)
    {
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        try {
            if ($lat == null) {
                $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
            } else {
                $user_ids = datavalue::getNearbySupplier($lat, $lng);
            }
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')
                ->where('products.category_id', $category_id)
                ->groupBy('products.id')
                ->select(
                    'supplier_products.*',
                    'products.id as product_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.brand_id',
                    'products.category_id',
                    'products.product_name',
                    'products.url',
                    'products.print_name',
                    'products.product_image',
                    'products.product_description',
                    'products.product_company',
                    'products.unit_id',
                    'products.department_id',
                    'products.tax_id',
                    'discounts.discount_name',
                    'units.unit_name'
                )->inRandomOrder()->take(10)->get();
            return $products;
        } catch (Exception $e) {
            return view('frontend.errors.500');
        }
    }

    public function about_us()
    {
        $about_page = AboutUsModel::first();
        return view('frontend.pages.about-us', compact('about_page'));
    }

    public function contact_us()
    {
        $contact_page = ContactUsModel::first();
        return view('frontend.pages.contact-us', compact('contact_page'));
    }
    public function contactSubmit(Request $request)
    {
        $msg = [
            'name.required' => 'Enter your name.',
            'email.required' => 'Enter your email.',
            'email.email' => 'Enter valid email.',
            'phone.required' => 'Enter phone number.',
            'phone.digits' => 'Enter valid phone number.',
            'message.required' => 'Enter your message.',
        ];
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'message' => 'required'
        ], $msg);
        try {
            $data = $request->except('_token');
            $contact = ContactUsMessagesModel::create($data);
            $admin_email = 'info@a2zharvests.com';
            Mail::to($admin_email)->send(new ContactUsMail($contact->id));
            return redirect()->back()->with('success', 'Contact message submitted successfully!!!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function becomeSeller()
    {
        return view('frontend.pages.become-a-seller');
    }

    public function becomeSellerSubmit(Request $request)
    {
        $msg = [
            'name.required' => 'Enter your name.',
            'email.required' => 'Enter your email.',
            'email.email' => 'Enter valid email.',
            'phone.required' => 'Enter phone number.',
            'phone.digits' => 'Enter valid phone number.',
            'business_name.required' => 'Enter your business name.',
            'business_description.required' => 'Enter your business description.',
        ];
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'business_name' => 'required',
            'business_description' => 'required'
        ], $msg);
        try {
            $data = $request->except('_token');
            $contact = BecomeSellerModel::create($data);
            $admin_email = 'rohit@f3hree.com';
            Mail::to($admin_email)->send(new BecomeSellerMail($contact->id));
            return redirect()->back()->with('success', 'Your request submitted successfully,admin will contact you soon.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function faq()
    {
        $data = FaqModel::where("status", "Active")->latest()->get();
        return view('frontend.pages.faq', compact('data'));
    }

    public function terms_and_condition()
    {
        $data = TermsConditionModel::first();
        return view('frontend.pages.terms-condition', compact('data'));
    }

    public function change_password()
    {
        return view('frontend.pages.change-password');
    }

    public function manage_address()
    {
        return view('frontend.pages.manage-address');
    }

    public function products(Request $request, $category_url = null, $subcategory_url = null)
    {
        if ($category_url == "search") {
            $search_query = explode('(', $request->get("search_query"));
            $search_query = $search_query[0];
            $category_id = "";
            $category_name = "Search";
            $subcategory_id = "";
            $subcategory_name = "";
            $cat_image = asset('/') . "frontendtheme/images/about-pic2.jpg";
            RecentSearchesModel::create([
                'search_query' => $search_query
            ]);
            RecentSearchesModel::where('created_at', '<=', Carbon::now()->subDay())->delete();
        } else {
            $category_id = CategoryModel::where('url', $category_url)->value('id');
            $category_name = CategoryModel::where('url', $category_url)->value('category_name');
            $subcategory_id = CategoryModel::where('url', $subcategory_url)->where("parent_id", $category_id)->value('id');
            $subcategory_name = CategoryModel::where('url', $subcategory_url)->value('category_name');
            $cat_image = CategoryModel::where('url', $subcategory_url)->value('category_image');
            $search_query = '';
        }
        return view('frontend.pages.product', compact('category_name', 'cat_image', 'subcategory_name', 'category_id', 'subcategory_id', 'search_query'));
    }

    public function product_filter(Request $request)
    {
        $action = $request->get('action');
        $subcategory_id = $request->get('subcategory_id');
        $search_query = $request->get('search_query');
        $discount = $request->get('discount');
        $minimum_price = $request->get('minimum_price');
        $maximum_price = $request->get('maximum_price');
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        try {
            $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id');
            if (isset($action) && $action == 'fetch_data') {
                if (isset($subcategory_id) && $subcategory_id != '') {
                    $products->where(function ($query) use ($subcategory_id) {
                        $query->where('products.sub_category_id', $subcategory_id);
                    });
                }
                if (isset($discount) && $discount != "undefined") {
                    $disc_val = explode('-', $discount);
                    $products = $products->where('discounts.discount_name', '=', '%');
                    $products->where(function ($query) use ($disc_val) {
                        $query->where('supplier_products.discount_value', '>=', $disc_val[0]);
                        $query->where('supplier_products.discount_value', '<=', $disc_val[1]);
                    });
                }
                if (isset($minimum_price) && isset($maximum_price)) {
                    $products->where(function ($query) use ($minimum_price, $maximum_price) {
                        $query->where('supplier_products.price', '>=', $minimum_price);
                        $query->where('supplier_products.price', '<=', $maximum_price);
                    });
                }
                if (isset($search_query) && $search_query != '') {
                    $products_ids = DB::table('products')->where(function ($query) use ($search_query) {
                        $query->where('product_name', $search_query);
                        $query->orwhere('product_name', 'like', '%' . $search_query . '%');
                    })->pluck('id')->toArray();
                    $products = $products->select(
                        'supplier_products.*',
                        'products.id as product_id',
                        'products.category_id',
                        'products.sub_category_id',
                        'products.brand_id',
                        'products.category_id',
                        'products.product_name',
                        'products.url',
                        'products.product_image',
                        'products.product_description',
                        'products.product_company',
                        'products.unit_id',
                        'products.department_id',
                        'products.tax_id',
                        'discounts.discount_name',
                        'units.unit_name'
                    )->whereIn("products.id", $products_ids);
                } else {
                    $products = $products->select(
                        'supplier_products.*',
                        'products.id as product_id',
                        'products.category_id',
                        'products.sub_category_id',
                        'products.brand_id',
                        'products.category_id',
                        'products.product_name',
                        'products.url',
                        'products.product_image',
                        'products.product_description',
                        'products.product_company',
                        'products.unit_id',
                        'products.department_id',
                        'products.tax_id',
                        'discounts.discount_name',
                        'units.unit_name'
                    );
                }
                $products = $products->whereIn('supplier_products.user_id', $user_ids)
                    ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')->orderBy('supplier_products.status')->paginate(12);
                $html = '';
                if (count($products) > 0) {
                    foreach ($products as $row) {
                        $datavalue = new datavalue();
                        $sale_price = $datavalue->get_sale_price($row->price, $row->discount_name, $row->discount_value);
                        $opacity = '';
                        if ($row->status == "Active") {
                            if (Auth::check()) {
                                $cart_html = '<a href="javascript:add_cart(' . $row->user_id . ',' . $row->product_id . ');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>';
                            } else {
                                $cart_html = '<a style="cursor: pointer;" data-toggle="modal" data-target="#myModal1" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>';
                            }
                        } else {
                            $opacity = 'opacity';
                            $cart_html = '<a href="javascript:void(0);" class="add-area orange">Out Of Stock</a>';
                        }
                        $discount = '';
                        if ($sale_price < $row->price) {
                            if ($row->discount_name == 'rs') {
                                $discount .= '<span class="discount">Rs.' . $row->discount_value . ' <br> off</span>';
                            } else {
                                $discount .= '<span class="discount">' . $row->discount_value . '% <br> off</span>';
                            }
                        }
                        $html .= '<div class="col-md-4 col-lg-3">
                        <div class="product-details">
                            <div class="product-details-inner ' . $opacity . '">
                               <a href="' . route('product_details', [$row->user_id, $row->url]) . '">
                                <div class="product-image">
                                    ' . $discount . '
                                    <img src="' . $row->product_image . '" alt="Product Image">
                                </div>
                                </a>
                            </div>
                            <div class="text-content">
                                <h5><a href="' . route('product_details', [$row->user_id, $row->url]) . '">' . $row->product_name . '</a></h5>
                                <p>' . $row->quantity . ' ' . $row->unit_name . '</p>';
                        if ($sale_price < $row->price) {
                            $html .= '<h6 class="old-price"><span>₹</span>' . $row->price . '</h6>';
                        }
                        $html .= '<div class="add-cart">
                                    <h6><span class="rupee">&#x20B9</span>' . $sale_price . '</h6>
                                    ' . $cart_html . '
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
                } else {
                    $html .= '<div class="row" style="color: red; padding-left: 35%; font-size: 35px; margin-top: 200px;"> No Products Found...</div>';
                }
                return json_encode(array('html' => $html, 'pagination' => (string)$products->links('frontend.layouts.ajax_pagination')));
            }
        } catch (Exception $e) {
            return view('frontend.errors.500');
        }
    }

    public function product_details($supplier_id, $slug)
    {
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
        $product_details = ProductModel::join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
            ->where("products.url", $slug)->where("supplier_products.user_id", $supplier_id)->select("supplier_products.product_id as id", "supplier_products.*", "products.*", "discounts.*")->first();
        $related_product =  DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
            ->join('units', 'units.id', '=', 'products.unit_id')->where('products.sub_category_id', $product_details["sub_category_id"])
            ->whereIn('supplier_products.user_id', $user_ids)
            ->where('products.status', 'Active')->take(10)->get();
        $shop_details = ShopDetailsModel::where('user_id', $supplier_id)->first();
        $showSlot = datavalue::checkAvailability($lat_long["latitude"], $lat_long["longitude"]);
        return view('frontend.pages.product-details', compact('product_details', 'shop_details', 'supplier_id', 'related_product', 'showSlot'));
    }

    public function vendor_form()
    {
        return view('frontend.pages.vendor-form');
    }

    public function cart()
    {
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
        $product_id = DB::table('order_details')
            ->groupBy('product_id')
            ->select(DB::raw('count(product_id) as count'), 'order_details.product_id')
            ->orderBy('count', 'desc')
            ->pluck('product_id')
            ->toArray();
        $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
            ->join('units', 'units.id', '=', 'products.unit_id')
            ->whereIn('supplier_products.user_id', $user_ids)
            ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')
            ->whereIn('products.id', $product_id)
            ->groupBy('products.id')
            ->select(
                'supplier_products.*',
                'products.id as product_id',
                'products.category_id',
                'products.sub_category_id',
                'products.brand_id',
                'products.category_id',
                'products.product_name',
                'products.url',
                'products.print_name',
                'products.product_image',
                'products.product_description',
                'products.product_company',
                'products.unit_id',
                'products.department_id',
                'products.tax_id',
                'discounts.discount_name',
                'units.unit_name'
            );
        $products = $products->orderBy('supplier_products.status')->paginate(10);
        if (Auth::check()) {
            $cart_data = CartModel::join('products', 'products.id', 'carts.product_id')->where('carts.user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')
                ->select('carts.*', 'products.product_name', 'products.product_name', 'products.product_image')->get();
        } else {
            $cart_data = [];
        }
        return view('frontend.pages.cart', compact('cart_data', 'products'));
    }
    public function manageAddress()
    {
        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
        return view('frontend.pages.manage-address', compact('shipping_details'));
    }
    public function saveShippingAddress(Request $request)
    {
        $msg = [
            'name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your Email.',
            'phone_no.required' => 'Enter Your Phone No',
            'address.required' => 'Enter Your Address.',
            'pincode.required' => 'Enter Pincode.'
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone_no' => 'required',
            'address' => 'required',
            'pincode' => 'required'
        ], $msg);
        if ($validator->passes()) {
            try {
                $latLng = datavalue::get_lat_long($request->get('pincode'));
                $latitude = $latLng['lat'];
                $longitude = $latLng['lng'];
                if ($latitude == "") {
                    $data = $latLng;
                    $msg = 'Pin code is not valid.';
                    return array('status' => 'error', 'data' => $data, 'msg' => $msg);
                } else {
                    $user_ids = datavalue::getNearbySupplier($latitude, $longitude);
                    $cart_suppliers = CartModel::where("user_id", Auth::user()->id)->pluck("supplier_id")->toArray();
                    $count = count(array_diff($cart_suppliers, $user_ids));
                    if ($count > 0) {
                        $data = [];
                        $msg = 'Cart products is not available for this pin code.';
                        return array('status' => 'error', 'data' => $data, 'msg' => $msg);
                    }
                }
                $shipping = ShippingModel::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'phone_no' => $request->get('phone_no'),
                    'address' => $request->get('address'),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'pincode' => $request->get('pincode'),
                    'landmark' => $request->get('landmark'),
                    'city' => $request->get('city'),
                    'state' => $request->get('state'),
                ]);
                $data = $shipping;
                $msg = 'Shipping Details Saved.';
                return array('status' => 'success', 'data' => $data, 'msg' => $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Shipping Details Not Saved.';
                return array('status' => 'error', 'data' => $data, 'msg' => $msg);
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return array('status' => 'error', 'data' => $data, 'msg' => $msg);
        }
    }
    public function checkout()
    {

        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();

        $user_ids = datavalue::getNearbySupplier($shipping_details->latitude, $shipping_details->longitude);
        $user_ids = datavalue::getNearbySupplier('32.7266016', '74.8570259');
        $cart_suppliers = CartModel::where("user_id", Auth::user()->id)->pluck("supplier_id")->toArray();
        $count = count(array_diff($cart_suppliers, $user_ids));
        if ($count > 0) {
            return redirect()->back()->with('error', 'Cart products is not available for selected pin code.');
        }
        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->get();
        $cart_data = [];
        $total_amount = 0;
        $gross_amount = 0;
        $total_discount = 0;
        foreach ($cart_details as $details) {
            $tax_value = TaxValueModel::where('tax_id', $details->product->tax_id)->get();
            $supplier_product = SupplierProductModel::where('product_id', $details->product_id)->where('user_id', $details->supplier_id)->where('status', 'Active')->first();
            $total_amount = $total_amount + ($details->quantity * $supplier_product->price);
            $product_total_price = $details->quantity * $supplier_product->price;
            $discount_details = DiscountModel::find($supplier_product->discount_id);
            $tax_details = TaxModel::find($details->product->tax_id);
            $product_discount = 0;
            if ($discount_details['discount_name'] == '%') {
                $product_discount = (($product_total_price * $supplier_product->discount_value) / 100);
                $product_discount_price = $product_total_price - $product_discount;
            } else if ($discount_details['discount_name'] == 'rs') {
                $product_discount = ($details->quantity * $supplier_product->discount_value);
                $product_discount_price = $product_total_price - $product_discount;
            } else {
                $product_discount_price = $product_total_price;
            }
            $total_discount = $total_discount + $product_discount;
            if ($tax_details->is_inclusive == 'No') {
                $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                $gross_amount = $gross_amount + $product_with_tax_price;
            } else {
                $product_with_tax_price = $product_discount_price;
                $gross_amount = $gross_amount + $product_with_tax_price;
            }
            array_push($cart_data, ['cart_details' => [
                'id' => $details->id,
                'user_id' => $details->user_id,
                'supplier_id' => $details->supplier_id,
                'product_id' => $details->product_id,
                'product_name' => $details->product->product_name,
                'product_image' => $details->product->product_image,
                'product_other_image' => ProductImageModel::where('product_id', $details->product_id)->pluck('image')->toArray(),
                'quantity' => $details->quantity,
                'weight' => SupplierProductModel::where('product_id', $details->product_id)->where('user_id', $details->supplier_id)->where('status', 'Active')->value('quantity'),
                'price' => SupplierProductModel::where('product_id', $details->product_id)->where('user_id', $details->supplier_id)->where('status', 'Active')->value('price'),
                'unit' => $details->unit,
                'discount_type' => $discount_details['discount_name'],
                'discount_value' => SupplierProductModel::where('product_id', $details->product_id)->where('user_id', $details->supplier_id)->where('status', 'Active')->value('discount_value'),
                'tax_name' => $tax_details['tax_name'],
                'is_inclusive' => $tax_details['is_inclusive'],
                'tax_value' => $tax_value,
                'total_tax_value' => $tax_details['tax_value'],
            ]]);
        }
        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
        if (empty($shipping_details)) {
            $shipping_details = ShippingModel::create([
                'user_id' => Auth::user()->id,
                'name' => Auth::user()->user_name,
                'email' => Auth::user()->email,
                'phone_no' => Auth::user()->phone,
                'address' => Auth::user()->location,
                'latitude' => Auth::user()->latitude,
                'longitude' => Auth::user()->longitude
            ]);
        }
        $delivery_text = SettingModel::where('key', "Delivery Text")->value("value");
        $delivery_charge_details = DeliverySettingModel::first();
        if ($delivery_charge_details["max_amount"] > $gross_amount) {
            $delivery_charge = $delivery_charge_details["delivery_charge"];
        } else {
            $delivery_charge = 0;
        }
        return view('frontend.pages.checkout', compact('cart_data', 'shipping_details', 'delivery_text', 'delivery_charge'));
    }
    public function timeSlot()
    {
        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
        if (empty($shipping_details)) {
            $shipping_details = ShippingModel::create([
                'user_id' => Auth::user()->id,
                'name' => Auth::user()->user_name,
                'email' => Auth::user()->email,
                'phone_no' => Auth::user()->email,
                'address' => Auth::user()->location,
                'latitude' => Auth::user()->latitude,
                'longitude' => Auth::user()->longitude
            ]);
        }
        $today = Carbon::today();
        $data["data"][0]["date"] = $today->format('d M Y');
        $data["data"][0]["day"] = $today->format('D');
        for ($i = 1; $i < 7; $i++) {
            $now = $today->addDay();
            $data["data"][$i]["date"] = $now->format('d M Y');
            $data["data"][$i]["day"] = $now->format('D');
        }
        $data["showSlot"] = datavalue::checkAvailability(Auth::user()->latitude, Auth::user()->longitude);
        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
        $total_amount = 0;
        $gross_amount = 0;
        $total_discount = 0;
        $total_tax = 0;
        foreach ($cart_details as $c_details) {
            $supplier_product = SupplierProductModel::where('product_id', $c_details->product_id)->where('user_id', $c_details->supplier_id)->where('status', 'Active')->first();
            $total_amount = $total_amount + ($c_details->quantity * $supplier_product->price);
            $product_total_price = $c_details->quantity * $supplier_product->price;
            $discount_details = DiscountModel::find($supplier_product->discount_id);
            $discount['id'] = $discount_details->id;
            $discount['discount_name'] = $discount_details->discount_name;
            $discount['discount_value'] = $supplier_product->discount_value;
            $tax_details = TaxModel::find($c_details->product->tax_id);
            $tax['tax_id'] = $tax_details->id;
            $tax['tax_name'] = $tax_details->tax_name;
            $tax['tax_total_value'] = $tax_details->tax_value;
            $tax['is_inclusive'] = $tax_details->is_inclusive;
            $tax['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
            $product_discount = 0;
            if ($discount_details['discount_name'] == '%') {
                $product_discount = (($product_total_price * $supplier_product->discount_value) / 100);
                $product_discount_price = $product_total_price - $product_discount;
            } else if ($discount_details['discount_name'] == 'rs') {
                $product_discount = ($c_details->quantity * $supplier_product->discount_value);
                $product_discount_price = $product_total_price - $product_discount;
            } else {
                $product_discount_price = $product_total_price;
            }
            $total_discount = $total_discount + $product_discount;
            if ($tax_details->is_inclusive == 'No') {
                $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                $gross_amount = $gross_amount + $product_with_tax_price;
                $total_tax = $total_tax + (($product_discount_price * $tax_details['tax_value']) / 100);
            } else {
                $product_with_tax_price = $product_discount_price;
                $gross_amount = $gross_amount + $product_with_tax_price;
            }
        }
        $delivery_charge_details = DeliverySettingModel::first();
        if ($delivery_charge_details["max_amount"] > $gross_amount) {
            $delivery_charge = $delivery_charge_details["delivery_charge"];
        } else {
            $delivery_charge = 0;
        }
        $pay_amount = $gross_amount + $delivery_charge;
        return view('frontend.pages.time-slot', compact('shipping_details', 'data', 'pay_amount'));
    }

    public function top_seller_products()
    {
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        try {
            $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
            $product_id = DB::table('order_details')
                ->groupBy('product_id')
                ->select(DB::raw('count(product_id) as count'), 'order_details.product_id')
                ->orderBy('count', 'desc')
                ->pluck('product_id')
                ->toArray();
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')
                ->whereIn('products.id', $product_id)
                ->groupBy('products.id')
                ->select(
                    'supplier_products.*',
                    'products.id as product_id',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.brand_id',
                    'products.category_id',
                    'products.product_name',
                    'products.url',
                    'products.print_name',
                    'products.product_image',
                    'products.product_description',
                    'products.product_company',
                    'products.unit_id',
                    'products.department_id',
                    'products.tax_id',
                    'discounts.discount_name',
                    'units.unit_name'
                );
            $products = $products->orderBy('supplier_products.status')->paginate(10);
            $search_query = '';
            return view('frontend.pages.top-seller-products', compact('products', 'search_query'));
        } catch (Exception $e) {
            return view('frontend.errors.500');
        }
    }
    public function top_product_filter(Request $request)
    {
        $action = $request->get('action');
        $search_query = $request->get('search_query');
        $discount = $request->get('discount');
        $minimum_price = $request->get('minimum_price');
        $maximum_price = $request->get('maximum_price');
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        try {
            $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
            $product_id = DB::table('order_details')
                ->groupBy('product_id')
                ->select(DB::raw('count(product_id) as count'), 'order_details.product_id')
                ->orderBy('count', 'desc')
                ->pluck('product_id')
                ->toArray();
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('products.id', $product_id);
            if (isset($action) && $action == 'fetch_data') {
                if (isset($discount) && $discount != "undefined") {
                    $disc_val = explode('-', $discount);
                    $products = $products->where('discounts.discount_name', '=', '%');
                    $products->where(function ($query) use ($disc_val) {
                        $query->where('supplier_products.discount_value', '>=', $disc_val[0]);
                        $query->where('supplier_products.discount_value', '<=', $disc_val[1]);
                    });
                }
                if (isset($minimum_price) && isset($maximum_price)) {
                    $products->where(function ($query) use ($minimum_price, $maximum_price) {
                        $query->where('supplier_products.price', '>=', $minimum_price);
                        $query->where('supplier_products.price', '<=', $maximum_price);
                    });
                }
                $products_ids = [];
                if (isset($search_query) && $search_query != '') {
                    $products_ids = DB::table('products')->where(function ($query) use ($search_query) {
                        $query->where('product_name', $search_query);
                        $query->orwhere('product_name', 'like', $search_query . '%');
                        $query->orwhere('product_description', 'like', '%' . $search_query . '%');
                    })->pluck('id')->toArray();
                    $products = $products->whereIn("products.id", $products_ids)->select(
                        'supplier_products.*',
                        'products.id as product_id',
                        'products.category_id',
                        'products.sub_category_id',
                        'products.brand_id',
                        'products.category_id',
                        'products.product_name',
                        'products.url',
                        'products.product_image',
                        'products.product_description',
                        'products.product_company',
                        'products.unit_id',
                        'products.department_id',
                        'products.tax_id',
                        'discounts.discount_name',
                        'units.unit_name'
                    );
                } else {
                    $products = $products->select(
                        'supplier_products.*',
                        'products.id as product_id',
                        'products.category_id',
                        'products.sub_category_id',
                        'products.brand_id',
                        'products.category_id',
                        'products.product_name',
                        'products.url',
                        'products.product_image',
                        'products.product_description',
                        'products.product_company',
                        'products.unit_id',
                        'products.department_id',
                        'products.tax_id',
                        'discounts.discount_name',
                        'units.unit_name'
                    );
                }
                $products = $products->whereIn('supplier_products.user_id', $user_ids)
                    ->where('products.status', 'Active')->where('supplier_products.status', '<>', 'Deleted')->orderBy('supplier_products.status')->paginate(12);
                $html = '';
                if (count($products) > 0) {
                    foreach ($products as $row) {
                        $datavalue = new datavalue();
                        $sale_price = $datavalue->get_sale_price($row->price, $row->discount_name, $row->discount_value);
                        if ($row->status == "Active") {
                            if (Auth::check()) {
                                $cart_html = '<a href="javascript:add_cart(' . $row->user_id . ',' . $row->product_id . ');" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>';
                            } else {
                                $cart_html = '<a data-toggle="modal" data-target="#exampleModal" class="add-area"><span><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>ADD</a>';
                            }
                        } else {
                            $cart_html = '<a href="javascript:void(0);" class="add-area">Out Of Stock</a>';
                        }
                        $html .= '<div class="col-md-4 col-lg-3">
                        <div class="product-details">
                            <div class="product-details-inner">
                               <a href="' . route('product_details', [$row->user_id, $row->url]) . '">
                                <div class="product-image">
                                    <img src="' . $row->product_image . '" alt="Product Image">
                                </div>
                                </a>
                            </div>
                            <div class="text-content">
                                <h5><a href="' . route('product_details', [$row->user_id, $row->url]) . '">' . $row->product_name . '</a></h5>
                                <p>' . $row->quantity . ' ' . $row->unit_name . '</p><h6 class="old-price"><span>₹</span>' . $row->price . '</h6>                           
                                <div class="add-cart">
                                    <h6><span class="rupee">&#x20B9</span>' . $sale_price . '</h6>
                                    ' . $cart_html . '
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
                } else {
                    $html .= '<div class="row" style="color: red; padding-left: 35%; font-size: 35px; margin-top: 200px;"> No Products Found...</div>';
                }
                return json_encode(array('html' => $html, 'pagination' => (string)$products->links('frontend.layouts.ajax_pagination')));
            }
        } catch (Exception $e) {
            return view('frontend.errors.500');
        }
    }
    public function categories()
    {
        $category_list = CategoryModel::where('parent_id', 0)->where('status', 'Active')->orderBy('category_name', 'asc')->get();
        $parent_details = [];
        return view("frontend.pages.categories", compact("category_list", "parent_details"));
    }
    public function sub_categories($url)
    {
        $parent_details = CategoryModel::where("url", $url)->first();
        $category_list = CategoryModel::where('parent_id', $parent_details["id"])->where('status', 'Active')->orderBy('category_name', 'asc')->get();
        return view("frontend.pages.categories", compact("category_list", "parent_details"));
    }
    public function newsletter_subscribe(Request $request)
    {
        $msg = [
            'email.required' => 'Enter your  email.',
            'email.unique' => 'You have already subscribed.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:newsletter_subscriber',
        ], $msg);
        if ($validator->passes()) {
            SubscriberModel::create([
                'email' => $request->get('email')
            ]);
            return array('status' => 'success', 'msg' => 'Successfully Subscribed');
        } else {
            $msg =  $validator->errors()->first();
            return array('status' => 'error', 'msg' => $msg);
        }
    }
    public function get_suggestion(Request $request)
    {
        $search_query = $request->search_query;
        $lat_long = datavalue::getLatLong();
        $distance = SettingModel::where('id', 2)->value('value');
        $user_ids = datavalue::getNearbySupplier($lat_long["latitude"], $lat_long["longitude"]);
        $pro_ids = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->whereIn('supplier_products.user_id', $user_ids)
            ->where('products.product_name', 'like', $search_query . '%')->groupBy('products.id')->pluck('products.id')->toArray();
        $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->where('products.product_name', 'like', $search_query . '%')->where("products.status", "Active")->where('supplier_products.status', '<>', 'Deleted')->groupBy('products.id')->get();
        $products1 = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->whereNotIn('products.id', $pro_ids)->where('products.product_name', 'like', '%' . $search_query . '%')->where("products.status", "Active")->where('supplier_products.status', '<>', 'Deleted')->groupBy('products.id')->get();
        $html = '<ul class="pro-search-list">';
        if (count($products) > 0) {
            foreach ($products as $row) {
                $unit_name = UnitModel::find($row->unit_id)->unit_name;
                $qty = $row->quantity . ' ' . $unit_name . '- Rs.' . $row->price;
                $html .= '<li>
                                                <img src="' . $row->product_image . '" alt="">
                                                <h5 class="product_text">' . $row->product_name . '(' . $qty . ')</h5>
                                            </li>';
            }
            foreach ($products1 as $row) {
                $unit_name = UnitModel::find($row->unit_id)->unit_name;
                $qty = $row->quantity . ' ' . $unit_name . '- Rs.' . $row->price;
                $html .= '<li>
                                                <img src="' . $row->product_image . '" alt="">
                                                <h5 class="product_text">' . $row->product_name . '(' . $qty . ')</h5>
                                            </li>';
            }
            $html .= '</ul>';
            return array('status' => 'success', 'html' => $html);
        } else {
            return array('status' => 'fail', 'html' => '');
        }
    }
    public function checkSlot(Request $request)
    {
        $user_delivery_date = $request->get('deliveryDate');
        $user_delivery_time = $request->get('deliverySlot');
        $availability = SettingModel::where('key', 'Booking Available Per Slot')->value('value');
        $bookedCount = OrderModel::where("user_delivery_date", $user_delivery_date)->where("user_delivery_time", $user_delivery_time)->whereYear('datetime', '=', date('Y'))->count();
        $status = DeliverySlotsModel::where("slot_name", $user_delivery_time)->value("status");
        if ((($availability <= $bookedCount) && $user_delivery_date != "") || $status == "Inactive") {
            return array('status' => 'error', 'msg' => 'Selected Slot Not Available.');
        } else {
            return array('status' => 'success', 'msg' => 'Selected Slot Available.');
        }
    }
}
