<?php

namespace App\Http\Controllers\Admin\Banner;

use App\Model\TextBannerModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TextBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = TextBannerModel::select('*')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('title', function ($data) {
                    return ucfirst($data->title);
                })
                ->addColumn('description', function ($data) {
                    return $data->description;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editTextBanner', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delTextBanner', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_text_banner('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_text_banner('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','category_image'])
                ->toJson();
        }
        return view('admin.text_banner.index');
    }

    public function addTextBanner(){
        return view('admin.text_banner.add');
    }
    public function saveTextBanner(Request $request)
    {
        $msg = [
            'title.required' => 'Please Select Category.',
            'description.required' => 'Enter Category Name.',
        ];
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ], $msg);
        $title = $request->get('title');
        $description = $request->get('description');
        try {
            TextBannerModel::create([
                'title'=>$title,
                'description'=>$description,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Text Banner Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Text Banner Not addded.');
        }
    }
    public function editTextBanner($id){
        $textBannerById = TextBannerModel::find($id);
        return view('admin.text_banner.edit', compact('textBannerById'));
    }

    public function updateTextBanner(Request $request)
    {
        $msg = [
            'title.required' => 'Please Select Category.',
            'description.required' => 'Enter Category Name.',
        ];
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ], $msg);

        $id = $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        try {
            TextBannerModel:: where('id', $id)->update([
                'title' => $title,
                'description' => $description,
            ]);

            return redirect()->back()->with('success', 'Text Banner Updated Successfully !!!');
        }catch(Exception $e) {
             return redirect()->back()->with('error','Text Banner Not Updated.');
        }
    }

    public function active_inactive_text_banner(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            TextBannerModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_text_banner('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            TextBannerModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_text_banner('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delTextBanner($id){
        TextBannerModel::where('id',$id)->delete();
        return redirect()->back()->with('success','Text Banner Deleted Successfully !!!');
    }
}
