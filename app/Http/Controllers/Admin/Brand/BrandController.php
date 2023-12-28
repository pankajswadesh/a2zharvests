<?php

namespace App\Http\Controllers\Admin\Brand;

use App\Model\BrandModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = BrandModel::select('*')->where('status','<>','Deleted')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('brand_name', function ($data) {
                    return ucfirst($data->brand_name);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editBrand', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delBrand', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_brand('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_brand('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.brand.index');
    }

    public function addBrand(){
        return view('admin.brand.add');
    }
    public function saveBrand(Request $request)
    {
        $msg = [
            'brand_name.required' => 'Enter Brand Name.',
        ];
        $this->validate($request, [
            'brand_name' => 'required',
        ], $msg);
        $brand_name = $request->get('brand_name');
        $url = str_slug($request->get('brand_name'));
        try {
            BrandModel::create([
                'brand_name'=>$brand_name,
                'url'=>$url,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Brand Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Brand Not addded.');
        }
    }
    public function editBrand($id){
        $brandById = BrandModel::find($id);
        return view('admin.brand.edit', compact('brandById'));
    }

    public function updateBrand(Request $request)
    {
        $msg = [
            'brand_name.required' => 'Enter Brand Name.',
        ];
        $this->validate($request, [
            'brand_name' => 'required',
        ], $msg);

        $id = $request->get('id');
        $brand_name = $request->get('brand_name');
        $url = str_slug($request->get('brand_name'));
        try {
            BrandModel:: where('id', $id)->update([
                'brand_name' => $brand_name,
                'url' => $url,
            ]);

            return redirect()->back()->with('success', 'Brand Updated Successfully !!!');
        }catch(Exception $e) {
             return redirect()->back()->with('error','Brand Not Updated.');
        }
    }

    public function active_inactive_brand(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            BrandModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_brand('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            BrandModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_brand('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delBrand($id){
        BrandModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Brand Deleted Successfully !!!');
    }
}
