<?php

namespace App\Http\Controllers\api\v1\Vendor\Product;

use App\Model\CategoryModel;
use App\Model\DiscountModel;
use App\Model\ProductModel;
use App\Model\SupplierProductModel;
use App\repo\Response;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    public function get_category(){
        try {
            $data =CategoryModel::where('parent_id', 0)->where('status', 'Active')->orderByRaw('ISNULL(priority), priority ASC')->get();
            $msg = '';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'No Category Found.';
            return Response::Error($data, $msg);
        }
    }

    public function get_sub_category($id){
        try {
            $categories = CategoryModel::where('parent_id',$id)->where('status', 'Active')->get();
            $data = $categories;
            $msg = '';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'No Sub Category Found.';
            return Response::Error($data, $msg);
        }
    }
    public function get_product_list($sub_cat_id)
    {
        try {
            $user_id = Auth::user()->id;
            $product_ids=SupplierProductModel::where('user_id',$user_id)->pluck('product_id')->toArray();
            $products = ProductModel::whereNotIn('id',$product_ids)->where('sub_category_id',$sub_cat_id)->where('status','Active')->get();
            $data = [];
            foreach ($products as $product){
                array_push($data,[
                    'product_id'=>$product->id,
                    'category_name'=>$product->category->category_name,
                    'sub_category_name'=>$product->sub_category->category_name,
                    'brand_name'=>$product->brand->brand_name,
                    'product_name'=>$product->product_name,
                    'print_name'=>$product->print_name,
                    'product_main_image'=>$product->product_image,
                    'product_other_image'=>$product->images->pluck('image')->toArray(),
                    'product_description'=>$product->product_description,
                    'product_company'=>$product->product_company,
                    'unit'=>$product->unit->unit_name,
                    'department'=>$product->department->dept_name,
                    'tax'=>$product->tax,
                    'discount'=>DiscountModel::where('status','Active')->get(),
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg = 'No Product Found.';
            return Response::Error($data, $msg);
        }
    }

    public function get_discount(){
        try {
            $discount=DiscountModel::where('status','Active')->get();
            $data = $discount;
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Discount Not Found';
            return Response::Error($data, $msg);
        }
    }

    public function vendor_product_mapped(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            $data1=[];
            $data=$request->all();
            for ($i = 0; $i < count($data); $i++) {
                SupplierProductModel::create([
                    'user_id'=>$user_id,
                    'product_id'=>$data[$i]['product_id'],
                    'quantity'=>$data[$i]['quantity'],
                    'price'=>$data[$i]['price'],
                    'discount_id'=>$data[$i]['discount_id'],
                    'discount_value'=>$data[$i]['discount_value'],
                    'status'=>'Inactive',
                ]);
            }
            $msg = 'Products are added into supplier lists.';
            return Response::Success($data1, $msg);
        }catch(Exception $e) {
                $data = [];
                $msg = 'Products are not added into supplier lists.';
                return Response::Error($data, $msg);
            }
    }

    public function get_vendor_product_list(Request $request){
        try{
            $user_id=Auth::user()->id;
            $sub_category_id=$request->get('sub_category_id');
            $products=SupplierProductModel::where('user_id',$user_id)->where('status','<>','Deleted')->get();
            $data = [];
            foreach ($products as $product){
                $product_data=ProductModel::find($product->product_id);
                if($sub_category_id==$product_data->sub_category_id) {
                    array_push($data, [
                        'id' => $product->id,
                        'products' => $product_data,
                        'category_name' => $product_data->category->category_name,
                        'sub_category_name' => $product_data->sub_category->category_name,
                        'brand_name' => $product_data->brand->brand_name,
                        'quantity' => $product->quantity,
                        'price' => $product->price,
                        'discount_id' => $product->discount->discount_name,
                        'discount_value' => $product->discount_value,
                        'unit' => $product_data->unit->unit_name,
                        'department'=>$product_data->department->dept_name,
                        'tax'=>$product_data->tax,
                        'discount'=>DiscountModel::where('status','Active')->get(),
                        'status' => $product->status
                    ]);
                }
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Products are not found in supplier lists.';
            return Response::Error($data, $e);
        }
    }

    public function update_vendor_product(Request $request){
        $msg = [
            'id.required' => 'Product Id required.',
            'quantity.required' => 'Enter Your Quantity.',
            'price.required' => 'Enter Your Price.',
            'discount_id.required' => 'Enter Your Discount Type.',
            'discount_value.required' => 'Enter Your Discount Value.',
            'status.required' => 'Select Your Product Status.',
        ];
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
            'price' => 'required|numeric',
            'discount_id' => 'required',
            'discount_value' => 'required|numeric',
            'status' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                SupplierProductModel::where('id',$request->id)->update([
                    'quantity'=>$request->quantity,
                    'price'=>$request->price,
                    'discount_id'=>$request->discount_id,
                    'discount_value'=>$request->discount_value,
                    'status'=>$request->status,
                ]);
                $data = [];
                $msg = 'Product Updated Successfully.';
                return Response::Success($data, $msg);
            }catch (Exception $e) {
                $data = [];
                $msg = 'Product Not Updated.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function update_vendor_product_status(Request $request){
        $msg = [
            'id.required' => 'Product Id required.',
            'status.required' => 'Product Status is required.',
        ];
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'status' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                SupplierProductModel::where('id',$request->id)->where('user_id',Auth::user()->id)->update([
                    'status'=>$request->status,
                ]);
                $product_details=SupplierProductModel::where('id',$request->id)->where('user_id',Auth::user()->id)->first();
                $data = $product_details;
                $msg = 'Product Status Updated Successfully.';
                return Response::Success($data, $msg);
            }catch (Exception $e) {
                $data = [];
                $msg = 'Product Status Not Updated.';
                return Response::Error($data, $msg);
            }
        }else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }
}
