<?php

namespace App\Http\Controllers\api\v1\User\Cart;

use App\Model\CartModel;
use App\Model\CashbackSettingsModel;
use App\Model\CategoryModel;
use App\Model\DeliverySettingModel;
use App\Model\DiscountModel;
use App\Model\OrderModel;
use App\Model\ProductImageModel;
use App\Model\ProductModel;
use App\Model\PromocodesModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\TaxValueModel;
use App\repo\Response;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function add_to_cart(Request $request)
    {
        $msg = [
            'supplier_id.required' => 'Supplier id is required.',
            'product_id.required' => 'Product id is required.',
            'quantity.required' => 'Quantity is required.',
            'price.unique' => 'Price is required.',
            'unit.unique' => 'Unit is required.',
            'discount_id.required' => 'Discount Type is required.',
            'discount_value.required' => 'Discount Value is required.',
            'tax_id.required' => 'Tax is required.',
        ];
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
            'price' => 'required',
            'unit' => 'required',
            'discount_id' => 'required',
            'discount_value' => 'required',
            'tax_id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $tax_details = TaxModel::find($request->get('tax_id'));
                $tax['tax_id'] = $tax_details->id;
                $tax['tax_name'] = $tax_details->tax_name;
                $tax['tax_value'] = $tax_details->tax_value;
                $tax['is_inclusive'] = $tax_details->is_inclusive;
                $discount['discount_type'] = $request->get('discount_id');
                $discount['discount_value'] = $request->get('discount_value');
                // return json_encode($discount);
                $cart_check = CartModel::where('user_id', Auth::user()->id)->where('supplier_id', $request->get('supplier_id'))->where('product_id', $request->get('product_id'))->count();
                if ($cart_check == 0) {
                    CartModel::create([
                        'user_id' => Auth::user()->id,
                        'supplier_id' => $request->get('supplier_id'),
                        'product_id' => $request->get('product_id'),
                        'quantity' => $request->get('quantity'),
                        'price' => $request->get('price'),
                        'unit' => $request->get('unit'),
                        'discount' => json_encode($discount),
                        'tax' => json_encode($tax),
                        'datetime' => NOW(),
                    ]);
                    $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                    $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count()];
                    foreach ($cart_details as $details) {
                        $discount = json_decode($details->discount, true);
                        $tax = json_decode($details->tax, true);
                        array_push($data, ['cart_details' => [
                            'id' => $details->id,
                            'user_id' => $details->user_id,
                            'supplier_id' => $details->supplier_id,
                            'product_id' => $details->product_id,
                            'product_name' => $details->product->product_name,
                            'quantity' => $details->quantity,
                            'price' => $details->price,
                            'unit' => $details->unit,
                            'discount_type' => $discount['discount_type'],
                            'discount_value' => $discount['discount_value'],
                            'tax_name' => $tax['tax_name'],
                            'tax_value' => $tax['tax_value'],
                            'is_inclusive' => $tax['is_inclusive'],
                        ]]);
                    }

                    $msg = 'Item added to the cart.';
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg = 'Item was already added to the cart.';
                    return Response::Error($data, $msg);
                }

            } catch (Exception $e) {
                $data = [];
                $msg = 'Item not added to the cart.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
            // return response()->json(['error' => $validator->errors()]);
        }
    }


    public function update_cart(Request $request)
    {
        $msg = [
            'id.required' => 'Cart id is required.',
            'quantity.required' => 'Quantity is required.',
        ];
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                CartModel::where('id', $request->get('id'))->where('user_id', Auth::user()->id)->update([
                    'quantity' => $request->get('quantity')
                ]);
                $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count()];
                foreach ($cart_details as $details) {
                    $discount = json_decode($details->discount, true);
                    $tax = json_decode($details->tax, true);
                    array_push($data, ['cart_details' => [
                        'id' => $details->id,
                        'user_id' => $details->user_id,
                        'supplier_id' => $details->supplier_id,
                        'product_id' => $details->product_id,
                        'product_name' => $details->product->product_name,
                        'quantity' => $details->quantity,
                        'price' => $details->price,
                        'unit' => $details->unit,
                        'discount_type' => $discount['discount_type'],
                        'discount_value' => $discount['discount_value'],
                        'tax_name' => $tax['tax_name'],
                        'tax_value' => $tax['tax_value'],
                        'is_inclusive' => $tax['is_inclusive'],
                    ]]);
                }
                $msg = 'Cart Item Updated Successfully.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Cart Item Not Updated.';
                return Response::Error($data, $msg);
            }
        }else{
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function remove_cart(Request $request){
        $msg = [
            'id.required' => 'Cart id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                CartModel::where('id', $request->get('id'))->where('user_id', Auth::user()->id)->delete();
                $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count()];
                foreach ($cart_details as $details) {
                    $discount = json_decode($details->discount, true);
                    $tax = json_decode($details->tax, true);
                    array_push($data, ['cart_details' => [
                        'id' => $details->id,
                        'user_id' => $details->user_id,
                        'supplier_id' => $details->supplier_id,
                        'product_id' => $details->product_id,
                        'product_name' => $details->product->product_name,
                        'quantity' => $details->quantity,
                        'price' => $details->price,
                        'unit' => $details->unit,
                        'discount_type' => $discount['discount_type'],
                        'discount_value' => $discount['discount_value'],
                        'tax_name' => $tax['tax_name'],
                        'tax_value' => $tax['tax_value'],
                        'is_inclusive' => $tax['is_inclusive'],
                    ]]);
                }
                $msg = 'Item Deleted Successfully.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Item Not Deleted.';
                return Response::Error($data, $msg);
            }
        }else{
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function destroy_cart(){
        try{
            CartModel::where('user_id',Auth::user()->id)->delete();
            $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
            $data = ['total_qty' => CartModel::where('user_id',Auth::user()->id)->whereHas('active_supplier_product')->count()];
            foreach ($cart_details as $details) {
                $discount = json_decode($details->discount, true);
                $tax = json_decode($details->tax, true);
                array_push($data, ['cart_details' => [
                    'id' => $details->id,
                    'user_id' => $details->user_id,
                    'supplier_id' => $details->supplier_id,
                    'product_id' => $details->product_id,
                    'product_name' => $details->product->product_name,
                    'quantity' => $details->quantity,
                    'price' => $details->price,
                    'unit' => $details->unit,
                    'discount_type' => $discount['discount_type'],
                    'discount_value' => $discount['discount_value'],
                    'tax_name' => $tax['tax_name'],
                    'tax_value' => $tax['tax_value'],
                    'is_inclusive' => $tax['is_inclusive'],
                ]]);
            }
            $msg = 'Cart is empty.';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'Cart is not empty.';
            return Response::Error($data, $msg);
        }
    }

    public function get_cart_details(Request $request){
        try {
            $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
            $prodcut_ids = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->pluck('product_id')->toArray();
            $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count(), 'prodcut_ids' => $prodcut_ids];
            $total_amount = 0;
            $gross_amount = 0;
            $total_discount = 0;
            $total_tax = 0;
            foreach ($cart_details as $details) {
                $discount = json_decode($details->discount, true);
                $tax = json_decode($details->tax, true);
                $supplier_product=SupplierProductModel::where('product_id',$details->product_id)->where('user_id',$details->supplier_id)->where('status','Active')->first();
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
                if($tax_details->is_inclusive=='No') {
                    $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                    $gross_amount = $gross_amount + $product_with_tax_price;
                    $total_tax = $total_tax + (($product_discount_price * $tax_details['tax_value']) / 100);
                }else{
                    $product_with_tax_price = $product_discount_price;
                    $gross_amount = $gross_amount + $product_with_tax_price;
                    $total_tax = $total_tax;
                }
                array_push($data, ['cart_details' => [
                    'id' => $details->id,
                    'user_id' => $details->user_id,
                    'supplier_id' => $details->supplier_id,
                    'product_id' => $details->product_id,
                    'product_name' => $details->product->product_name,
                    'product_image' => $details->product->product_image,
                    'product_other_image' => ProductImageModel::where('product_id', $details->product_id)->pluck('image')->toArray(),
                    'quantity' => $details->quantity,
                    'price' => $details->price,
                    'unit' => $details->unit,
                    'discount_type' => $discount['discount_type'],
                    'discount_value' => $discount['discount_value'],
                    'tax_name' => $tax['tax_name'],
                    'tax_value' => $tax['tax_value'],
                    'is_inclusive' => $tax['is_inclusive'],
                    'gross_amount' => $product_with_tax_price
                ]]);
            }
            $data["gross_amount"] = $gross_amount;
            $msg = '';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $e->getMessage());
        }
    }

    public function get_check_out_cart_details(){
        try {
            $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
            $prodcut_ids = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->pluck('product_id')->toArray();
            $is_delivery_charge=1;
            $delivery_charge_details = DeliverySettingModel::first();
            $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count(), 'prodcut_ids' => $prodcut_ids];
            $total_amount = 0;
            $gross_amount = 0;
            $total_discount = 0;
            foreach ($cart_details as $details) {
                $tax_value = TaxValueModel::where('tax_id', $details->product->tax_id)->get();
                $supplier_product=SupplierProductModel::where('product_id',$details->product_id)->where('user_id',$details->supplier_id)->where('status','Active')->first();
                $total_amount = $total_amount + ($details->quantity * $supplier_product->price);
                $product_total_price = $supplier_product->price;
                $discount_details = DiscountModel::find($supplier_product->discount_id);
                $tax_details = TaxModel::find($details->product->tax_id);
                $product_discount = 0;
                if ($discount_details['discount_name'] == '%') {
                    $product_discount = (($product_total_price * $supplier_product->discount_value) / 100) ;
                    $product_discount_price = ceil($product_total_price - $product_discount) * $details->quantity;
                } else if ($discount_details['discount_name'] == 'rs') {
                    $product_discount = ($details->quantity * $supplier_product->discount_value);
                    $product_discount_price = ceil($product_total_price - $product_discount) * $details->quantity;
                } else {
                    // $product_discount = 0;
                    $product_discount_price = ceil($product_total_price) * $details->quantity;
                }
                $total_discount = $total_discount + $product_discount;
                if($tax_details->is_inclusive=='No') {
                    $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                    $gross_amount = $gross_amount + ceil($product_with_tax_price);
                }else{
                    $product_with_tax_price = $product_discount_price;
                    $gross_amount = $gross_amount + ceil($product_with_tax_price);
                }
                array_push($data, ['cart_details' => [
                    'id' => $details->id,
                    'user_id' => $details->user_id,
                    'supplier_id' => $details->supplier_id,
                    'product_id' => $details->product_id,
                    'product_name' => $details->product->product_name,
                    'product_image' => $details->product->product_image,
                    'product_other_image' => ProductImageModel::where('product_id', $details->product_id)->pluck('image')->toArray(),
                    'quantity' => $details->quantity,
                    'weight' => SupplierProductModel::where('product_id',$details->product_id)->where('user_id',$details->supplier_id)->where('status','Active')->value('quantity'),
                    'price' => SupplierProductModel::where('product_id',$details->product_id)->where('user_id',$details->supplier_id)->where('status','Active')->value('price'),
                    'unit' => $details->unit,
                    'discount_type' => $discount_details['discount_name'],
                    'discount_value' => SupplierProductModel::where('product_id',$details->product_id)->where('user_id',$details->supplier_id)->where('status','Active')->value('discount_value'),
                    'tax_name' => $tax_details['tax_name'],
                    'is_inclusive' => $tax_details['is_inclusive'],
                    'tax_value' => $tax_value,
                    'total_tax_value' => $tax_details['tax_value'],
                    'product_with_tax_price' =>$product_with_tax_price
                ]]);
            }
            $data["gross_amount"] = $gross_amount;
            if($is_delivery_charge>0 && $delivery_charge_details["max_amount"]>$gross_amount){
                $delivery_charge = $delivery_charge_details["delivery_charge"];
            }else{
                $delivery_charge= 0;
            }
            $check_new_user = OrderModel::where('user_id',Auth::user()->id)->count();
            if($check_new_user==0){
                $promo_codes = PromocodesModel::where("min_amount","<",$gross_amount)->where("status","Active")->get();
            }else{
                $promo_codes = PromocodesModel::where("min_amount","<",$gross_amount)->where("for","All Users")->where("status","Active")->get();
            }
            $cashback = CashbackSettingsModel::where("min_amount","<",$gross_amount)->orderBy("min_amount",'desc')->first();
            $data["delivery_charge"] = $delivery_charge;
            $data["promo_codes"] = $promo_codes;
            if(!empty($cashback)){
                $casback_amount = ($gross_amount/100)*$cashback["cashback_percent"];
                if($casback_amount>=$cashback["cashback_upto"]){
                    $casback_amount = $cashback["cashback_upto"];
                }
                $cashback_data["amount"] = $casback_amount;
                $cashback_data["eligible"] = "Yes";
            }else{
                $cashback_data["amount"] = 0;
                $cashback_data["eligible"] = "No";
            }
            $data["cashback_data"] = $cashback_data;
            $msg = '';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $e->getMessage());
        }

    }

}
