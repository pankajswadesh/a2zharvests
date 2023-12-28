<?php

namespace App\Http\Controllers\Frontend;

use App\Model\CartModel;
use App\Model\DiscountModel;
use App\Model\ProductModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\UnitModel;
use App\repo\datavalue;
use App\repo\Response;
use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class CartController extends Controller
{
    public function add_to_cart(Request $request)
    {

        $msg = [
            'supplier_id.required' => 'Supplier id is required.',
            'product_id.required' => 'Product id is required.',
            'quantity.required' => 'Quantity is required.',
        ];
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $product_details = ProductModel::where('id',$request->product_id)->first();
                $supplier_product_details = SupplierProductModel::where('user_id',$request->supplier_id)->where('product_id',$request->product_id)->where('status','Active')->first();
                $tax_details = TaxModel::find($product_details["tax_id"]);
                $tax['tax_id'] = $tax_details->id;
                $tax['tax_name'] = $tax_details->tax_name;
                $tax['tax_value'] = $tax_details->tax_value;
                $tax['is_inclusive'] = $tax_details->is_inclusive;
                $discount['discount_type'] = DiscountModel::where('id',$supplier_product_details['discount_id'])->value('discount_name');
                $discount['discount_value'] = $supplier_product_details['discount_value'];
                // return json_encode($discount);
                $cart_check = CartModel::where('user_id', Auth::user()->id)->where('supplier_id', $request->get('supplier_id'))->where('product_id', $request->get('product_id'))->count();
                if ($cart_check == 0) {
                    CartModel::create([
                        'user_id' => Auth::user()->id,
                        'supplier_id' => $request->get('supplier_id'),
                        'product_id' => $request->get('product_id'),
                        'quantity' => $request->get('quantity'),
                        'price' => $supplier_product_details["price"],
                        'unit' => UnitModel::where('id',$product_details["unit_id"])->value('unit_name'),
                        'discount' => json_encode($discount),
                        'tax' => json_encode($tax),
                        'datetime' => NOW(),
                    ]);
                    $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->count()];
                    $msg = 'Item added to the cart.';
                    return array('status'=>'success','data'=>$data,'msg'=>$msg);
                } else {
                    $data = [];
                    $msg = 'Item was already added to the cart.';
                    return array('status'=>'error','data'=>$data,'msg'=>$msg);
                }

            } catch (Exception $e) {
                $data = [];
                $msg = 'Item not added to the cart.';
                return array('status'=>'error','data'=>$data,'msg'=>$e->getMessage());
            }
        } else {
            $data = $validator->errors();
            $msg = $validator->errors()->first();
            return array('status'=>'error','data'=>$data,'msg'=>$msg);
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
                $cart_details = CartModel::where('user_id', Auth::user()->id)->get();
                $grand_total = 0;
                $datavalue = new datavalue();
                foreach ($cart_details as $row){
                    $discount = json_decode($row->discount,true);
                    $sale_price = $datavalue->get_sale_price($row->price, $discount["discount_type"], $discount["discount_value"]);
                    $grand_total += $row->quantity * $sale_price;
                }
                $data = ['total_qty' => CartModel::where('user_id', Auth::user()->id)->count(),'grand_total'=>$grand_total];
                $msg = 'Cart Item Updated Successfully.';
                return array('status'=>'success','data'=>$data,'msg'=>$msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Cart Item Not Updated.';
                return array('status'=>'error','data'=>$data,'msg'=>$msg);
            }
        }else{
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return array('status'=>'error','data'=>$data,'msg'=>$msg);
        }
    }

    public function remove_cart(Request $request,$id){
        try {
            CartModel::where('id', $id)->where('user_id', Auth::user()->id)->delete();
            $msg = 'Item Deleted Successfully.';
            return redirect()->back()->with('success',$msg);
        } catch (Exception $e) {
            $msg = 'Item Not Deleted.';
            return redirect()->back()->with('error',$msg);
        }
    }
}
