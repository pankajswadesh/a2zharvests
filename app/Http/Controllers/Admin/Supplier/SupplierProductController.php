<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Model\BrandModel;
use App\Model\CategoryModel;
use App\Model\DiscountModel;
use App\Model\ProductModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\UnitModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class SupplierProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index(Request $request)
    {
        $categories=CategoryModel::where('parent_id',0)->where('status','Active')->get();
        if($request->get('category_id')!=''){
            $category_id=$request->get('category_id');
        }else{
            $category_id='';
        }
        if($request->get('subcategory')!=''){
            $subcategory=$request->get('subcategory');
        }else{
            $subcategory='';
        }
        if(request()->ajax()) {
            $product_ids=SupplierProductModel::where('user_id',Auth::user()->id)->pluck('product_id')->toArray();
            $data = ProductModel::select('*')->whereNotIn('id',$product_ids)->where('status','=','Active')->with('category')->with('sub_category')->with('brand')->whereHas('unit')->whereHas('department')->whereHas('tax');
                if($category_id!='') {
                    $data->where('category_id', '=', $category_id);
                }
                if($subcategory!=''){
                    $data->where('sub_category_id', '=',$subcategory);
                }
             $data=$data->get();
            return Datatables::of($data)
                ->addColumn('checkbox', function ($data) {
                    $check_box='<input type="checkbox" name="product_checkbox[]" value="'.$data->id.'" class="product_checkbox">';
                    return $check_box;
                })
                ->addIndexColumn()
                ->addColumn('category_id', function ($data) {
                    return ucfirst($data->category['category_name']);
                })->addColumn('sub_category_id', function ($data) {
                    return ucfirst($data->sub_category['category_name']);
                })
                ->addColumn('brand_id', function ($data) {
                    return ucfirst($data->brand['brand_name']);
                })
                ->addColumn('product_name', function ($data) {
                    return ucfirst($data->product_name);
                })
                ->addColumn('product_image', function ($data) {
                    return '<img src="'.$data->product_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('unit_id', function ($data) {
                    return ucfirst($data->unit['unit_name']);
                })
                ->addColumn('quantity', function ($data) {
                    return '<input type="text" class="form-control"  name="quantity['.$data->id.']"  id="quantity_'.$data->id.'" value="" placeholder="Enter Product Quantity." />';
                })
                ->addColumn('price', function ($data) {
                    return '<input type="text" class="form-control"  name="price['.$data->id.']" value="" placeholder="Enter Product Price." />';
                })
                ->addColumn('discount', function ($data) {
                    $discount=DiscountModel::where('status','Active')->get();
                    $dis='<select class="form-control" name="discount['.$data->id.']" data-parsley-required="true">
                            <option value="">Select Discount</option>';
                    foreach ($discount as $discon) {
                        $dis .= '<option value="' . $discon->id . '">' . $discon->discount_name . '</option>';
                    }
                    $dis.='</select>';
                    return $dis;
                })

                ->addColumn('discount_value', function ($data) {
                    return '<input type="text" class="form-control"  name="discount_value['.$data->id.']" value="" placeholder="Enter Product Discount Value." />';
                })

                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editProduct', ['id' => $data->id]);
                    $url_image = route('admin::productImage', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delProduct', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_product('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_product('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_image . '" class="fancybox fancybox.iframe btn btn-xs btn btn-info" title="Images"><span class="fa fa-image"></span></a>&emsp;<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','product_image','checkbox','quantity','price','discount','discount_value'])
                ->toJson();
        }
        return view('admin.supplier_product.index',compact('categories','category_id','subcategory'));
    }

    public function bulk_product_add(Request $request){
        $product_ids=$request->get('id');
        try {
            for ($i = 0; $i < count($product_ids); $i++) {
                SupplierProductModel::create([
                    'user_id'=>Auth::user()->id,
                    'product_id'=> $request->get('id')[$i],
                    'quantity'=> $request->get('quantity')[$i],
                    'price'=> $request->get('price')[$i],
                    'discount_id'=> $request->get('discount')[$i],
                    'discount_value'=> $request->get('discount_value')[$i],
                    'status'=>'Inactive',
                ]);
            }
            return json_encode(array('status'=>'success','msg'=>'Product Deleted Successfully'));
        }catch (Exception $e){
            return json_encode(array('status'=>'error','msg'=>$e->getMessage()));
        }
    }

    public function manageMyProduct(Request $request)
    {

        if(request()->ajax()) {
            $data = SupplierProductModel::where('user_id',Auth::user()->id)->where('status','<>','Deleted')->get();

            return Datatables::of($data)
                ->addColumn('checkbox', function ($data) {
                    $check_box='<input type="checkbox" name="product_checkbox[]" value="'.$data->id.'" class="product_checkbox">';
                    return $check_box;
                })
                ->addIndexColumn()
                ->addColumn('product_name', function ($data) {
                    return ucfirst($data->product->product_name);
                })
                ->addColumn('category_id', function ($data) {
                    $category_name=CategoryModel::find($data->product->category_id)->category_name;
                    return ucfirst($category_name);
                })
                ->addColumn('sub_category_id', function ($data) {
                    $sub_category_name=CategoryModel::find($data->product->sub_category_id)->category_name;
                    return ucfirst($sub_category_name);
                })
                ->addColumn('brand_id', function ($data) {
                    $brand_name=BrandModel::find($data->product->brand_id)->brand_name;
                    return ucfirst($brand_name);
                })
                ->addColumn('product_image', function ($data) {
                    return '<img src="'.$data->product->product_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('unit_id', function ($data) {
                    $unit_name=UnitModel::find($data->product->unit_id)->unit_name;
                    return ucfirst($unit_name);
                })

                ->addColumn('quantity', function ($data) {
                    return '<input type="text" class="form-control"  name="quantity['.$data->id.']"  id="quantity_'.$data->id.'" value="'.$data->quantity.'" placeholder="Enter Product Quantity." />';
                })
                ->addColumn('price', function ($data) {
                    return '<input type="text" class="form-control"  name="price['.$data->id.']" value="'.$data->price.'" placeholder="Enter Product Price." />';
                })
                ->addColumn('discount', function ($data) {
                    $discount=DiscountModel::where('status','Active')->get();
                    $dis='<select class="form-control" name="discount['.$data->id.']" data-parsley-required="true">
                            <option value="">Select Discount</option>';
                    foreach ($discount as $discon) {
                        if ($discon->id == $data->discount_id) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        $dis .= '<option value="' . $discon->id . '" '.$selected.'>' . $discon->discount_name . '</option>';
                    }
                    $dis.='</select>';
                    return $dis;
                })

                ->addColumn('discount_value', function ($data) {
                    return '<input type="text" class="form-control"  name="discount_value['.$data->id.']" value="'.$data->discount_value.'" placeholder="Enter Product Discount Value." />';
                })

                ->addColumn('action', function ($data) {
                    $url_delete = "'".route('admin::delMyProduct', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_my_product('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_my_product('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','product_image','checkbox','quantity','price','discount','discount_value'])
                ->toJson();
        }
        return view('admin.supplier_product.product_mapped');
    }

    public function bulk_product_update(Request $request){
        $product_ids=$request->get('id');
        try {
            for ($i = 0; $i < count($product_ids); $i++) {
                SupplierProductModel::where('id',$request->get('id')[$i])->update([
                    'quantity'=> $request->get('quantity')[$i],
                    'price'=> $request->get('price')[$i],
                    'discount_id'=> $request->get('discount')[$i],
                    'discount_value'=> $request->get('discount_value')[$i],
                ]);
            }
            return json_encode(array('status'=>'success','msg'=>'Product Updated Successfully'));
        }catch (Exception $e){
            return json_encode(array('status'=>'error','msg'=>$e->getMessage()));
        }
    }

    public function active_inactive_my_product(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            SupplierProductModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_my_product('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            SupplierProductModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_my_product('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delMyProduct($id){
        SupplierProductModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Product Deleted Successfully !!!');
    }
}
