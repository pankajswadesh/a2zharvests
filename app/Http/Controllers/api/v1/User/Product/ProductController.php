<?php

namespace App\Http\Controllers\api\v1\User\Product;

use App\Http\Controllers\Frontend\PageController;
use App\Model\BrandModel;
use App\Model\CartModel;
use App\Model\CategoryModel;
use App\Model\DepartmentModel;
use App\Model\DiscountModel;
use App\Model\OrderDetailsModel;
use App\Model\ProductImageModel;
use App\Model\ProductModel;
use App\Model\RecentSearchesModel;
use App\Model\SettingModel;
use App\Model\ShopDetailsModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\UnitModel;
use App\repo\datavalue;
use App\repo\Response;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function get_products(Request $request)
    {
        $sub_category_id = $request->get('sub_category_id');
        $brand_id = $request->get('brand_id');
        $search_query = $request->get('search_query');
        $sort = $request->get('sort');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $distance=SettingModel::where('id',2)->value('value');
        try {
            $user_ids = datavalue::getNearbySupplier($latitude,$longitude);
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id');
            if(Auth::user()->wholesaler_id=='') {
                $products->whereIn('supplier_products.user_id', $user_ids);
            }else{
                $products->join('users', 'users.id', '=', 'supplier_products.user_id')->where('supplier_products.user_id', Auth::user()->wholesaler_id)->where("users.status","Active");
            }
            $products->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                'products.brand_id', 'products.category_id', 'products.product_name', 'products.print_name', 'products.product_image', 'products.product_description',
                'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id'
            );
            if (isset($sort) && $sort=='new') {
                $products->orderBy('products.id', 'desc');

            }

            if (isset($sort) && $sort=='price_asc') {
                $products->orderBy('supplier_products.price', 'asc');

            }
            if (isset($sort) && $sort=='price_desc') {
                $products->orderBy('supplier_products.price', 'desc');

            }
            if (isset($sort) && $sort=='name_asc') {
                $products->orderBy('products.product_name', 'asc');

            }
            if (isset($sort) && $sort=='name_desc') {
                $products->orderBy('products.product_name', 'desc');

            }

            if (isset($sub_category_id) && $sub_category_id != '') {
                $products->where('products.sub_category_id', $sub_category_id);
            }
            if (isset($brand_id) && $brand_id != '') {
                $products->where('products.brand_id', $brand_id);
            }
            if (isset($search_query) && $search_query != '') {
                $products->where('products.product_name', 'like', '%' . $search_query . '%');
            }
            $products = $products->where('products.status', 'Active')->where('supplier_products.status','<>','Deleted')->orderBy("products.product_name")->orderBy('supplier_products.status')->get();
            $data = [];
            foreach ($products as $product) {
                if($request->bearerToken()!=null){
                    $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                    $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                }else{
                    $is_added=0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added'=>$is_added,
                    'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id',$product->product_id)->first(),
                    // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                    'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    //  'discount' => DiscountModel::where('status', 'Active')->get(),
                    'status' => $product->status
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Products Not Found.';
            return Response::Error($data, $msg);
        }
    }
    public function get_products_v2(Request $request)
    {
        $sub_category_id = $request->get('sub_category_id');
        $brand_id = $request->get('brand_id');
        $search_query = $request->get('search_query');
        $sort = $request->get('sort');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $distance=SettingModel::where('id',2)->value('value');
        try {
            $user_ids = datavalue::getNearbySupplier($latitude,$longitude);
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status','<>','Deleted')
                ->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                    'products.brand_id', 'products.category_id', 'products.product_name', 'products.print_name', 'products.product_image', 'products.product_description',
                    'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id'
                );
            if (isset($sort) && $sort=='new') {
                $products->orderBy('products.id', 'desc');
            }
            if (isset($sort) && $sort=='price_asc') {
                $products->orderBy('supplier_products.price', 'asc');

            }
            if (isset($sort) && $sort=='price_desc') {
                $products->orderBy('supplier_products.price', 'desc');

            }
            if (isset($sort) && $sort=='name_asc') {
                $products->orderBy('products.product_name', 'asc');

            }
            if (isset($sort) && $sort=='name_desc') {
                $products->orderBy('products.product_name', 'desc');

            }

            if (isset($sub_category_id) && $sub_category_id != '') {
                $products->where('products.sub_category_id', $sub_category_id);
            }
            if (isset($brand_id) && $brand_id != '') {
                $products->where('products.brand_id', $brand_id);
            }
            if (isset($search_query) && $search_query != '') {
                $products->where('products.product_name', 'like','%'.$search_query . '%');
            }
            $products = $products->orderBy('supplier_products.status')->paginate(10);
            $data = ['count'=>count($products),
                'pagination'=>[
                    'current_page'=>$products->toArray()['current_page'],
                    'first_page_url'=>$products->toArray()['first_page_url'],
                    'from'=>$products->toArray()['from'],
                    'last_page'=>$products->toArray()['last_page'],
                    'last_page_url'=>$products->toArray()['last_page_url'],
                    'next_page_url'=>$products->toArray()['next_page_url'],
                    'path'=>$products->toArray()['path'],
                    'per_page'=>$products->toArray()['per_page'],
                    'prev_page_url'=>$products->toArray()['prev_page_url'],
                    'to'=>$products->toArray()['to'],
                    'total'=>$products->toArray()['total'],
                ],];

            foreach ($products as $product) {
                if($request->bearerToken()!=null){
                    $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                    $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                }else{
                    $is_added=0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added'=>$is_added,
                    'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id',$product->product_id)->first(),
                    // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                    'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    //  'discount' => DiscountModel::where('status', 'Active')->get(),
                    'status' => $product->status
                ]);

            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = $e->getMessage();
            return Response::Error($data, $msg);
        }
    }
    public function get_simmilar_product(Request $request)
    {
        $product_id = $request->get('product_id');
        $supplier_id = $request->get('supplier_id');
        $user_ids = datavalue::getNearbySupplier(Auth::user()->latitude,Auth::user()->longitude,$supplier_id);
        try {
            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->where('products.id', $product_id)
                ->where('products.status', 'Active')->where('supplier_products.status','<>','Deleted')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                    'products.brand_id', 'products.category_id', 'products.product_name', 'products.print_name', 'products.product_image', 'products.product_description',
                    'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id'
                )->orderBy('supplier_products.price','asc')->take(10)->inRandomOrder()->get();
            $data = [];
            foreach ($products as $product) {
                if($request->bearerToken()!=null){
                    $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                    $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                }else{
                    $is_added=0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added'=>$is_added,
                    'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id',$product->product_id)->first(),
                    // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                    'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    //  'discount' => DiscountModel::where('status', 'Active')->get(),
                    'status' => $product->status
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Products Not Found.';
            return Response::Error($data, $msg);
        }
    }

    public function get_top_seller_products(Request $request){
        $search_query = $request->get('search_query');
        try {
            $user_ids = datavalue::getNearbySupplier(Auth::user()->latitude,Auth::user()->longitude);
            $product_id=DB::table('order_details')
                ->groupBy('product_id')
                ->select(DB::raw('count(product_id) as count'),'order_details.product_id')
                ->orderBy('count','desc')
                ->pluck('product_id')
                ->toArray();

            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status','<>','Deleted')
                ->whereIn('products.id',$product_id)
                ->groupBy('products.id')
                ->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                    'products.brand_id', 'products.category_id', 'products.product_name', 'products.print_name', 'products.product_image', 'products.product_description',
                    'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id'
                );
            if($search_query!=""){
                $products = $products->where("products.product_name","like",$search_query."%");
            }
            $products = $products->orderBy('supplier_products.status')->paginate(10);
            $data = ['count'=>count($products),
                'pagination'=>[
                    'current_page'=>$products->toArray()['current_page'],
                    'first_page_url'=>$products->toArray()['first_page_url'],
                    'from'=>$products->toArray()['from'],
                    'last_page'=>$products->toArray()['last_page'],
                    'last_page_url'=>$products->toArray()['last_page_url'],
                    'next_page_url'=>$products->toArray()['next_page_url'],
                    'path'=>$products->toArray()['path'],
                    'per_page'=>$products->toArray()['per_page'],
                    'prev_page_url'=>$products->toArray()['prev_page_url'],
                    'to'=>$products->toArray()['to'],
                    'total'=>$products->toArray()['total'],
                ],];
            foreach ($products as $product) {
                if($request->bearerToken()!=null){
                    $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                    $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                }else{
                    $is_added=0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added'=>$is_added,
                    'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id',$product->product_id)->first(),
                    // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                    'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    //  'discount' => DiscountModel::where('status', 'Active')->get(),
                    'status' => $product->status
                ]);

            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Products Not Found.';
            return Response::Error($data, $msg);
        }
    }
    public function get_top_seller_products_v2(Request $request){
        $search_query = $request->get('search_query');
        try {
            $user_ids = datavalue::getNearbySupplier(Auth::user()->latitude,Auth::user()->longitude);
            $product_id=DB::table('order_details')
                ->groupBy('product_id')
                ->select(DB::raw('count(product_id) as count'),'order_details.product_id')
                ->orderBy('count','desc')
                ->pluck('product_id')
                ->toArray();

            $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')
                ->whereIn('products.id',$product_id)
                ->groupBy('products.id')
                ->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                    'products.brand_id', 'products.category_id', 'products.product_name', 'products.print_name', 'products.product_image', 'products.product_description',
                    'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id'
                );
            if($search_query!=""){
                $products = $products->where("products.product_name","like",$search_query."%");
            }
            $products = $products->orderBy('supplier_products.status')->paginate(10);
            $data = ['count'=>count($products),
                'pagination'=>[
                    'current_page'=>$products->toArray()['current_page'],
                    'first_page_url'=>$products->toArray()['first_page_url'],
                    'from'=>$products->toArray()['from'],
                    'last_page'=>$products->toArray()['last_page'],
                    'last_page_url'=>$products->toArray()['last_page_url'],
                    'next_page_url'=>$products->toArray()['next_page_url'],
                    'path'=>$products->toArray()['path'],
                    'per_page'=>$products->toArray()['per_page'],
                    'prev_page_url'=>$products->toArray()['prev_page_url'],
                    'to'=>$products->toArray()['to'],
                    'total'=>$products->toArray()['total'],
                ],];
            foreach ($products as $product) {
                if($request->bearerToken()!=null){
                    $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                    $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                }else{
                    $is_added=0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added'=>$is_added,
                    'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id',$product->product_id)->first(),
                    // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                    'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    //  'discount' => DiscountModel::where('status', 'Active')->get(),
                    'status' => $product->status
                ]);

            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Products Not Found.';
            return Response::Error($data, $msg);
        }
    }
    public function get_suggestion(Request $request){
        $search_query = $request->search_query;
        $user_ids = datavalue::getNearbySupplier(Auth::user()->latitude,Auth::user()->longitude);
        $pro_ids = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->whereIn('supplier_products.user_id', $user_ids)
            ->where('products.product_name','like',$search_query.'%')->groupBy('products.id')->pluck('products.id')->toArray();
        $products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->where('products.product_name','like',$search_query.'%')->groupBy('products.id')->get();
        $products1 = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
            ->whereNotIn('products.id',$pro_ids)->where('products.product_name','like','%'.$search_query.'%')->groupBy('products.id')->get();
        $data = [];
        foreach ($products as $product) {
            if($request->bearerToken()!=null){
                $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
            }else{
                $is_added=0;
            }
            array_push($data, [
                'id' => $product->id,
                'supplier_id' => $product->user_id,
                'product_id' => $product->product_id,
                'is_added'=>$is_added,
                'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                'products' => ProductModel::where('id',$product->product_id)->first(),
                // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                'category_name' => CategoryModel::find($product->category_id)->category_name,
                'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                'discount_value' => $product->discount_value,
                'unit' => UnitModel::find($product->unit_id)->unit_name,
                'department' => DepartmentModel::find($product->department_id)->dept_name,
                'tax_id' => $product->tax_id,
                'tax' => TaxModel::find($product->tax_id),
                //  'discount' => DiscountModel::where('status', 'Active')->get(),
                'status' => $product->status
            ]);

        }
        foreach ($products1 as $product) {
            if($request->bearerToken()!=null){
                $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
            }else{
                $is_added=0;
            }
            array_push($data, [
                'id' => $product->id,
                'supplier_id' => $product->user_id,
                'product_id' => $product->product_id,
                'is_added'=>$is_added,
                'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                'products' => ProductModel::where('id',$product->product_id)->first(),
                // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                'category_name' => CategoryModel::find($product->category_id)->category_name,
                'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                'discount_value' => $product->discount_value,
                'unit' => UnitModel::find($product->unit_id)->unit_name,
                'department' => DepartmentModel::find($product->department_id)->dept_name,
                'tax_id' => $product->tax_id,
                'tax' => TaxModel::find($product->tax_id),
                //  'discount' => DiscountModel::where('status', 'Active')->get(),
                'status' => $product->status
            ]);

        }
        $msg = '';
        return Response::Success($data, $msg);
    }
    public function get_home_category(Request $request){
        $categories_list = CategoryModel::where('parent_id', 0)->where('in_home', 'Yes')->where('status', 'Active')->orderBy('priority','asc')->get();
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        try {
            $data = [];
            foreach ($categories_list as $row){
                $productsList = PageController::getProductByCategory($row->id,$latitude,$longitude,$request->bearerToken());
                $productsArray =[];
                foreach ($productsList as $product){
                    if($request->bearerToken()!=null){
                        $main_user_id=User::where('api_token',$request->bearerToken())->value('id');
                        $is_added=CartModel::where('user_id',$main_user_id)->where('supplier_id',$product->user_id)->where('product_id',$product->product_id)->count();
                    }else{
                        $is_added=0;
                    }
                    array_push($productsArray, [
                        'id' => $product->id,
                        'supplier_id' => $product->user_id,
                        'product_id' => $product->product_id,
                        'is_added'=>$is_added,
                        'shop_name' =>ShopDetailsModel::where('user_id',$product->user_id)->value('business_name'),
                        'products' => ProductModel::where('id',$product->product_id)->first(),
                        // 'product_image' => ProductModel::where('id',$product->product_id)->value('product_image'),
                        'product_other_images' => ProductImageModel::where('product_id',$product->product_id)->pluck('image')->toArray(),
                        'category_name' => CategoryModel::find($product->category_id)->category_name,
                        'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                        'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                        'quantity' => $product->quantity,
                        'price' => $product->price,
                        'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                        'discount_value' => $product->discount_value,
                        'unit' => UnitModel::find($product->unit_id)->unit_name,
                        'department' => DepartmentModel::find($product->department_id)->dept_name,
                        'tax_id' => $product->tax_id,
                        'tax' => TaxModel::find($product->tax_id),
                        //  'discount' => DiscountModel::where('status', 'Active')->get(),
                        'status' => $product->status
                    ]);
                }
                array_push($data,[
                    "category"=>$row,
                    "products" => $productsArray
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = $e->getMessage();
            return Response::Error($data, $msg);
        }
    }
    public function recentSearches(Request $request){
        try {
            $user_ids = datavalue::getNearbySupplier($request->latitude,$request->longitude);
            $recent_searches = RecentSearchesModel::latest()->take(10)->get();
            $pro_ids = ProductModel::where('status','Active')->where(function($query) use($recent_searches) {
                foreach ($recent_searches as $row) {
                    $query->orWhere('product_name', 'LIKE', '%'.$row->search_query.'%');
                }
            })->pluck('id')->toArray();
            $recent_products = DB::table('products')->join('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->join('discounts', 'discounts.id', '=', 'supplier_products.discount_id')
                ->join('units', 'units.id', '=', 'products.unit_id')
                ->whereIn('supplier_products.user_id', $user_ids)
                ->where('products.status', 'Active')->where('supplier_products.status', '<>','Deleted')
                ->whereIn('products.id', $pro_ids)
                ->groupBy('products.id')
                ->select('supplier_products.*', 'products.id as product_id', 'products.category_id', 'products.sub_category_id',
                    'products.brand_id', 'products.category_id', 'products.product_name', 'products.url', 'products.print_name', 'products.product_image', 'products.product_description',
                    'products.product_company', 'products.unit_id', 'products.department_id', 'products.tax_id', 'discounts.discount_name', 'units.unit_name'
                )->take(10)->get();
            $data=[];
            foreach ($recent_products as $product) {
                if ($request->bearerToken() != null) {
                    $main_user_id = User::where('api_token', $request->bearerToken())->value('id');
                    $is_added = CartModel::where('user_id', $main_user_id)->where('supplier_id', $product->user_id)->where('product_id', $product->product_id)->count();
                } else {
                    $is_added = 0;
                }
                array_push($data, [
                    'id' => $product->id,
                    'supplier_id' => $product->user_id,
                    'product_id' => $product->product_id,
                    'is_added' => $is_added,
                    'shop_name' => ShopDetailsModel::where('user_id', $product->user_id)->value('business_name'),
                    'products' => ProductModel::where('id', $product->product_id)->first(),
                    'product_other_images' => ProductImageModel::where('product_id', $product->product_id)->pluck('image')->toArray(),
                    'category_name' => CategoryModel::find($product->category_id)->category_name,
                    'sub_category_name' => CategoryModel::find($product->sub_category_id)->category_name,
                    'brand_name' => BrandModel::find($product->brand_id)->brand_name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'discount_id' => DiscountModel::find($product->discount_id)->discount_name,
                    'discount_value' => $product->discount_value,
                    'unit' => UnitModel::find($product->unit_id)->unit_name,
                    'department' => DepartmentModel::find($product->department_id)->dept_name,
                    'tax_id' => $product->tax_id,
                    'tax' => TaxModel::find($product->tax_id),
                    'status' => $product->status
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = $e->getMessage();
            return Response::Error($data, $msg);
        }
    }
}
