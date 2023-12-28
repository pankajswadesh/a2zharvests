<?php

namespace App\Http\Controllers\Admin\Product;

use App\Model\BrandModel;
use App\Model\CategoryModel;
use App\Model\DepartmentModel;
use App\Model\ProductImageModel;
use App\Model\ProductModel;
use App\Model\TaxModel;
use App\Model\UnitModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

   public function index()
    {
        if(request()->ajax()) {
            $data = ProductModel::with(['category','sub_category','brand'])->where('status','!=','Deleted')->latest();
            return Datatables::of($data)
                ->addColumn('checkbox', function ($data) {
                 $check_box='<input type="checkbox" name="product_checkbox[]" value="'.$data->id.'" class="product_checkbox">';
                 return $check_box;
                })
                ->addIndexColumn()
                ->addColumn('category_id', function ($data) {
                    return ucfirst($data->category->category_name);
                })
                ->addColumn('sub_category_id', function ($data) {
                    return ucfirst($data->sub_category->category_name);
                })
                ->addColumn('brand_id', function ($data) {
                    return ucfirst($data->brand->brand_name);
                })
                ->addColumn('product_image', function ($data) {
                    return '<img src="'.$data->product_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('unit_id', function ($data) {
                    return ucfirst($data->unit->unit_name);
                })
                ->addColumn('department_id', function ($data) {
                    return ucfirst($data->department->dept_name);
                })
                ->addColumn('tax_id', function ($data) {
                    return ucfirst($data->tax->tax_value);
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
                ->rawColumns(['checkbox','category_id','sub_category_id','brand_id','product_image','unit_id','department_id','tax_id','action'])
                ->make(true);
        }
        return view('admin.product.index');
    }

    public function addProduct(){
        $categories=CategoryModel::where('parent_id',0)->where('status','Active')->get();
        $brands=BrandModel::where('status','Active')->get();
        $units=UnitModel::where('status','Active')->get();
        $depts=DepartmentModel::where('status','Active')->get();
        $taxs=TaxModel::where('status','Active')->get();
        return view('admin.product.add',compact('categories','brands','units','depts','taxs'));
    }

    public function get_sub_category(Request $request)
    {
        $subcategory = categoryModel::where('parent_id', $request->id)->where('status', "Active")->get();
        $html = '<option value="">select subcategory</option>';
        foreach ($subcategory as $sub) {
            $html.='<option value="'.$sub->id.'">'.$sub->category_name.'</option>';
        }
        return $html;
    }

    public function get_old_sub_category(Request $request)
    {
        $subcategory = categoryModel::where('parent_id', $request->id)->where('status', "Active")->get();
        $old_sub_category_id=$request->old_sub_category_id;
        $html = '<option value="">select subcategory</option>';
        foreach ($subcategory as $sub) {
            if($old_sub_category_id==$sub->id){
                $selected='selected';
            }else{
                $selected='';
            }
            $html.='<option value="'.$sub->id.'" '.$selected.'>'.$sub->category_name.'</option>';
        }
        return $html;
    }

    public function saveProduct(Request $request){

        $msg = [
            'category_id.required' => 'Please Select Category.',
            'subcategory.required' => 'Please select your sub category',
            'brand_id.required' => 'Please Select Brand.',
            'product_name.required' => 'Enter Product Name.',
            'print_name.required' => 'Enter Print Name.',
            'image.required' => 'Choose Category Image.',
            'product_description.required' => 'Enter Product Description.',
            'product_company.required' => 'Enter Product Company.',
            'unit_id.required' => 'Please Select Unit.',
            'department_id.required' => 'Please Select Department.',
            'tax_id.required' => 'Please Select Tax.',
        ];
        $this->validate($request, [
            'category_id' => 'required',
            'subcategory' => 'required',
            'brand_id' => 'required',
            'product_name' => 'required',
            'print_name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'product_description' => 'required',
            'product_company' => 'required',
            'unit_id' => 'required',
            'department_id' => 'required',
            'tax_id' => 'required',
        ], $msg);

        try {
            $photo = $request->file('image');
            $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
            $destinationPath = public_path('images/product');
//            $thumb_img = Image::make($photo->getRealPath())->resize(200, 200);
//            $thumb_img->save($destinationPath.'/'.$imageName,80);
            $photo->move($destinationPath,$imageName);
            $imageName=url('/images/product/' . $imageName);

            ProductModel::create([
                'category_id'=>$request->category_id,
                'sub_category_id'=>$request->subcategory,
                'brand_id'=>$request->brand_id,
                'product_name'=>$request->product_name,
                'print_name'=>$request->print_name,
                'product_image'=>url('/images/product/' . $imageName),
                'product_description'=>$request->product_description,
                'product_company'=>$request->product_company,
                'unit_id'=>$request->unit_id,
                'department_id'=>$request->department_id,
                'tax_id'=>$request->tax_id,
                'url'=>str_slug($request->product_name),
                'status'=>'Active',
            ]);
            return redirect()->back()->withInput()->with('success','Product Added Successfully !!!');

        }catch(Exception $e) {

            return redirect()->back()->with('error','Product Not added.');
        }
    }

        public function editProduct($id)
        {
            $productById = ProductModel::where('id',$id)->first();
            $categories=CategoryModel::where('parent_id',0)->where('status','Active')->get();
            $subcategories = categoryModel::where('parent_id',$productById->category_id)->get();
            $brands=BrandModel::where('status','Active')->get();
            $units=UnitModel::where('status','Active')->get();
            $depts=DepartmentModel::where('status','Active')->get();
            $taxs=TaxModel::where('status','Active')->get();
            return view('admin.product.edit',compact('categories','brands','units','depts','taxs','productById','subcategories'));
        }

        public function updateProduct(Request $request)
        {
            $msg = [
                'category_id.required' => 'Please Select Category.',
                'subcategory.required' => 'Please select your sub category',
                'brand_id.required' => 'Please Select Brand.',
                'product_name.required' => 'Enter Product Name.',
                'print_name.required' => 'Enter Print Name.',
                'product_description.required' => 'Enter Product Description.',
                'product_company.required' => 'Enter Product Company.',
                'unit_id.required' => 'Please Select Unit.',
                'department_id.required' => 'Please Select Department.',
                'tax_id.required' => 'Please Select Tax.',
            ];
            $this->validate($request, [
                'category_id' => 'required',
                'subcategory' => 'required',
                'brand_id' => 'required',
                'product_name' => 'required',
                'print_name' => 'required',
                'product_description' => 'required',
                'product_company' => 'required',
                'unit_id' => 'required',
                'department_id' => 'required',
                'tax_id' => 'required',
            ], $msg);
            $id = $request->get('id');
            $product_image=ProductModel::where('id',$id)->value('product_image');
            try {
                if(!empty($request->hasFile('image'))){
                    $product_image=explode('/',$product_image);
                    $product_image=end($product_image);
                    if($product_image!="" && file_exists(public_path().'/images/product/'.$product_image)){
                        if($product_image!='avatar.png') {
                            unlink(public_path() . '/images/product/' . $product_image);
                        }
                    }
                    $photo = $request->file('image');
                    $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
                    $destinationPath = public_path('images/product');
//                    $thumb_img = Image::make($photo->getRealPath())->resize(200, 200);
//                    $thumb_img->save($destinationPath.'/'.$imageName,80);
                    $photo->move($destinationPath,$imageName);
                    $imageName=url('/images/product/' . $imageName);
                }else{
                    $imageName =$product_image;
                }

             ProductModel::where('id',$id)->update([
                    'category_id'=>$request->category_id,
                    'sub_category_id'=>$request->get('subcategory'),
                    'brand_id'=>$request->brand_id,
                    'product_name'=>$request->product_name,
                    'print_name'=>$request->print_name,
                    'product_image'=>$imageName,
                    'product_description'=>$request->product_description,
                    'product_company'=>$request->product_company,
                    'unit_id'=>$request->unit_id,
                    'department_id'=>$request->department_id,
                    'tax_id'=>$request->tax_id,
                    'url'=>str_slug($request->product_name)
                ]);
                return redirect()->back()->with('success','Product Updated Successfully !!!');

            }catch(Exception $e) {
                return redirect()->back()->with('error',$e->getMessage());
            }
        }

    public function active_inactive_product(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            ProductModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_product('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            ProductModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_product('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delProduct($id){
        ProductModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Product Deleted Successfully !!!');
    }


    public function productImage($id)
    {
        $galleries=ProductImageModel::where('product_id',$id)->get();
        return view('admin.product.product-image',compact('galleries','id'));
    }


    public function saveProductImages(Request $request){
        $msg = [
            'gallery.required' => 'Please Select At Least One Image.'
        ];
        $this->validate($request, [
            'gallery' => 'required',
        ], $msg);

        $product_id=$request->get('product_id');
        if ($request->hasFile('gallery')) {
            $files = $request->file('gallery');
            try {
                foreach ($files as $file) {
                    $imageName =  str_random(6) . '_' .time().'.'.$file->getClientOriginalExtension();
                    $destinationPath = public_path('images/product');
//                    $thumb_img = Image::make($file->getRealPath())->resize(200, 200);
//                    $thumb_img->save($destinationPath.'/'.$imageName,80);
                    $file->move($destinationPath,$imageName);
                    $imageName=url('/images/product/' . $imageName);
                    ProductImageModel::create([
                        'product_id' => $product_id,
                        'image' => $imageName,
                    ]);
                }
                return redirect()->back()->with('success', 'Product Images Added Successfully !!!');
            }catch(Exception $e) {
                return redirect()->back()->with('error','Product Images Not Added.');
            }
        }else{
            return redirect()->back()->with('error','Please select atleast one image.');
        }

    }

    public function delProductImages($id){
        $product_image=ProductImageModel::where('id',$id)->value('image');
        $product_image=explode('/',$product_image);
        $product_image=end($product_image);
        if($product_image!="" && file_exists(public_path().'/images/product/'.$product_image)){
            unlink(public_path().'/images/product/'.$product_image);
        }
        ProductImageModel:: where('id',$id)->delete();
        return redirect()->back();
    }

    public function importProduct(){
        return view('admin.product.import_product');
    }

    public function save_import_product(Request $request)
    {
        $msg = [
            'import_file.required' => 'Please Select File.',
        ];
        $this->validate($request, [
            'import_file' => 'required|mimes:xls,xlsx',
        ], $msg);
        $path = $request->file('import_file')->getRealPath();
        $data = Excel::load($path)->get();
        if ($data->count() > 0) {
            $i=0;
            foreach ($data as $key => $value) {
                if($value->category_name && $value->sub_category_name && $value->brand_name && $value->product_name && $value->print_name  && $value->product_description && $value->product_company && $value->unit_name && $value->department_name && $value->tax_name) {

                   /* category import*/
                    $category=CategoryModel::where('url',str_slug($value->category_name))->first();
                    if($category!=''){
                        $category_id= $category->id;
                    }else{
                        $category=CategoryModel::create([
                            'parent_id'=>0,
                            'category_name'=>$value->category_name,
                            'category_image'=>url('/images/category/avatar.png'),
                            'url'=>str_slug($value->category_name),
                            'status'=>'Active',
                        ]);
                        $category_id= $category->id;
                    }
                    /* category import*/

                    /* sub category import*/
                    $sub_category=CategoryModel::where('url',str_slug($value->sub_category_name))->where('parent_id',$category->id)->first();
                    if($sub_category!=''){
                        $sub_category_id= $sub_category->id;
                    }else{
                        $sub_category=CategoryModel::create([
                            'parent_id'=>$category->id,
                            'category_name'=>$value->sub_category_name,
                            'category_image'=>url('/images/category/avatar.png'),
                            'url'=>str_slug($value->sub_category_name),
                            'status'=>'Active',
                        ]);
                        $sub_category_id= $sub_category->id;
                    }
                    /* sub category import*/

                    /* brand import*/
                    $brand=BrandModel::where('url',str_slug($value->brand_name))->first();
                    if($brand!=''){
                        $brand_id= $brand->id;
                    }else{
                        $brand=BrandModel::create([
                            'brand_name'=>$value->brand_name,
                            'url'=>str_slug($value->brand_name),
                            'status'=>'Active',
                        ]);
                        $brand_id= $brand->id;
                    }
                    /* brand import*/

                    /* unit import*/
                    $unit=UnitModel::where('unit_name',$value->unit_name)->first();
                    if($unit!=''){
                        $unit_id= $unit->id;
                    }else{
                        $unit= UnitModel::create([
                            'unit_name'=>$value->unit_name,
                            'status'=>'Active',
                        ]);
                        $unit_id= $unit->id;
                    }
                    /* unit import*/

                    /* department import*/
                    $department=DepartmentModel::where('dept_name',$value->department_name)->first();
                    if($department!=''){
                        $department_id= $department->id;
                    }else{
                        $department= DepartmentModel::create([
                            'dept_name'=>$value->department_name,
                            'status'=>'Active',
                        ]);
                        $department_id= $department->id;
                    }
                    /* department import*/

                    /* tax import*/
                    $tax=TaxModel::where('tax_name',$value->tax_name)->first();
                    if($tax!=''){
                        $tax_id= $tax->id;
                    }else{
                        $tax= TaxModel::create([
                            'tax_name'=>$value->tax_name,
                            'is_inclusive'=>'No',
                            'status'=>'Active',
                        ]);
                        $tax_id= $tax->id;
                    }
                    /* tax import*/

                    ProductModel::create([
                        'category_id'=>$category_id,
                        'sub_category_id'=>$sub_category_id,
                        'brand_id'=>$brand_id,
                        'product_name'=>$value->product_name,
                        'print_name'=>$value->product_name,
                        'product_image'=>url('/images/product').'/'.$value->product_image,
                        'product_description'=>$value->product_description,
                        'product_company'=>$value->product_company,
                        'unit_id'=>$unit_id,
                        'department_id'=>$department_id,
                        'tax_id'=>$tax_id,
                        'url'=>str_slug($value->product_name),
                        'status'=>'Active',
                    ]);
                    $i++;

                 //  echo $category_id.'--'.$sub_category_id.'--'.$brand_id.'--'.$unit_id.'--'.$department_id.'--'.$tax_id.'<br/>';
                }
            }
            return redirect()->back()->with('success',$i." Product Imported Successfully.");

        }
    }

    public function importImage(){
        return view('admin.product.import_image');
    }

    public function saveImportImage(Request $request){
        if ($request->hasFile('gallery')) {
            $files = $request->file('gallery');
            try {
                foreach ($files as $file) {
                    $imageName =  $file->getClientOriginalName();
                    $destinationPath = public_path('images/product');
                    //  $thumb_img = Image::make($file->getRealPath())->resize(200, 200);
                    // $thumb_img->save($destinationPath.'/'.$imageName,80);
                    $file->move($destinationPath.'/',$imageName);
                }
                return redirect()->back()->with('success', 'Product Images Imported Successfully !!!');
            }catch(Exception $e) {
                return redirect()->back()->with('error','Product Images Not Imported.');
            }
        }else{
            return redirect()->back()->with('error','Select at least one image.');
        }
    }

    public function bulk_product_delete(Request $request){
        $product_ids=$request->get('id');
        try {
            ProductModel::whereIn('id', $product_ids)->update([
                'status' => 'Deleted'
            ]);
            return json_encode(array('status'=>'success','msg'=>'Product Deleted Successfully'));
        }catch (Exception $e){
            return json_encode(array('status'=>'error','msg'=>'Product Not Deleted'));
        }
    }




}
