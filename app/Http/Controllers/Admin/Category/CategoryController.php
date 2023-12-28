<?php

namespace App\Http\Controllers\Admin\Category;

use App\Model\CategoryModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Image;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = CategoryModel::select('*')->where('status','<>','Deleted')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function ($data) {
                    return ucfirst($data->category_name);
                })
                ->addColumn('parent_id', function ($data) {
                    if($data->parent_id==0){
                        return 'Main';
                    }else{
                        return ucfirst($data->category->category_name);
                    }
                })
                ->addColumn('category_image', function ($data) {
                    return '<img src="'.$data->category_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editCategory', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delCategory', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_category('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_category('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','category_image'])
                ->toJson();
        }
        return view('admin.category.index');
    }

    public function addCategory(){
        $categories=CategoryModel::where('parent_id',0)->where('status','<>','Deleted')->get();
        return view('admin.category.add',compact('categories'));
    }
    public function saveCategory(Request $request)
    {
        $msg = [
            'parent_id.required' => 'Please Select Category.',
            'category_name.required' => 'Enter Category Name.',
            'image.required' => 'Choose Category Image.',
        ];
        $this->validate($request, [
            'parent_id' => 'required',
            'category_name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $msg);
        $parent_id = $request->get('parent_id');
        $category_name = $request->get('category_name');
        $url = str_slug($request->get('category_name'));
        try {
            $photo = $request->file('image');
            $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
            $destinationPath = public_path('images/category');
//            $thumb_img = Image::make($photo->getRealPath())->resize(1500, 500);
            $photo->move($destinationPath,$imageName);
            CategoryModel::create([
                'parent_id'=>$parent_id,
                'category_name'=>$category_name,
                'category_image'=>url('/images/category/' . $imageName),
                'url'=>$url,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Category Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Category Not addded.');
        }
    }
    public function editCategory($id){
        $categoryById = CategoryModel::find($id);
        $categories=CategoryModel::where('parent_id',0)->where('status','<>','Deleted')->get();
        return view('admin.category.edit', compact('categories','categoryById'));
    }

    public function updateCategory(Request $request)
    {
        $msg = [
            'parent_id.required' => 'Please Select Category.',
            'category_name.required' => 'Enter Category Name.',
        ];
        $this->validate($request, [
            'parent_id' => 'required',
            'category_name' => 'required',
        ], $msg);

        $id = $request->get('id');
        $parent_id = $request->get('parent_id');
        $category_name = $request->get('category_name');
        $url = str_slug($request->get('category_name'));
        $category_image=CategoryModel::where('id',$id)->value('category_image');
        try {
            if(!empty($request->hasFile('image'))){
                $category_image=explode('/',$category_image);
                $category_image=end($category_image);
                if(file_exists(public_path().'/images/category/'.$category_image)){
                    if($category_image!='avatar.png') {
                        unlink(public_path() . '/images/category/' . $category_image);
                    }
                }
                $photo = $request->file('image');
                $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
                $destinationPath = public_path('images/category');
//                $thumb_img = Image::make($photo->getRealPath())->resize(1500, 500);
//                $thumb_img->save($destinationPath.'/'.$imageName,100);
                $photo->move($destinationPath,$imageName);
                $imageName=url('/images/category/' . $imageName);
            }else{
                $imageName =$category_image;
            }
            CategoryModel:: where('id', $id)->update([
                'parent_id' => $parent_id,
                'category_name' => $category_name,
                'category_image' => $imageName,
                'url' => $url,
            ]);

            return redirect()->back()->with('success', 'Category Updated Successfully !!!');
        }catch(Exception $e) {
            return $e;
           // return redirect()->back()->with('error','Category Not Updated.');
        }
    }

    public function active_inactive_category(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            CategoryModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_category('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            CategoryModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_category('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delCategory($id){
        CategoryModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Category Deleted Successfully !!!');
    }

    public function homeCategory(){
        if(request()->ajax()) {
            $data = CategoryModel::where('parent_id',0)->where('status','<>','Deleted')->orderBy('priority','desc')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function ($data) {
                    return ucfirst($data->category_name);
                })
                ->addColumn('category_image', function ($data) {
                    return '<img src="'.$data->category_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('priority', function ($data) {
                    $id = "$data->id";
                    if($data->in_home=='Yes') {
                        return '<input type="text" value="' . $data->priority . '" name="priority" onchange="set_priority(' . $id . ',this.value)"><br><span id="priority_msg_'.$id.'" style="color: green;"></span>';
                    }else{
                        return 'N/A';
                    }
                })
                ->addColumn('action', function ($data) {
                    $edit='<span id="status'.$data->id.'">';
                    if($data->in_home=='Yes'){
                        $edit.='<a href="javascript:in_home('.$data->id.','.$data->in_home.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle">Yes</span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:in_home('.$data->id.','.$data->in_home.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle">No</span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    return $edit;
                })
                ->rawColumns(['action','category_image','priority'])
                ->toJson();
        }
        return view('admin.category.home_category');
    }
    public function CategoryInHome(Request $request){
        $id = $request->get('id');
        $in_home = $request->get('in_home');
        if($in_home=='Yes'){
            CategoryModel::where('id',$id)->update([
                'in_home' => 'No',
            ]);
            $st='No';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="in_home('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle">No</span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            CategoryModel::where('id',$id)->update([
                'in_home' => 'Yes',
            ]);
            $st='Yes';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_category('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle">Yes</span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }
    public function CategoryPriority(Request $request){
        $id = $request->get('id');
        $priority = $request->get('priority');
        $check = CategoryModel::where('priority',$priority)->count();
        if($check==0){
            CategoryModel::where('id',$id)->update([
                'priority' => $priority,
            ]);
            return json_encode(array('status'=>'success','msg'=>'Priority set successfully.'));
        }else{
            CategoryModel::where('priority',$priority)->update([
                'priority' => null,
            ]);
            CategoryModel::where('id',$id)->update([
                'priority' => $priority,
            ]);
            return json_encode(array('status'=>'success','msg'=>'Priority set and one is set to blank.'));
        }

    }
}
