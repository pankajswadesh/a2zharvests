<?php

namespace App\Http\Controllers\Admin\Banner;

use App\Model\ImageBannerModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class ImageBannerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = ImageBannerModel::select('*')->get();
            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('image', function ($data) {
                    return '<img src="'.$data->image.'" width="100px" height="100px"/>';
                })
                ->addColumn('type', function ($data) {
                    return $data->type;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editImageBanner', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delImageBanner', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_image_banner('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_image_banner('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','image'])
                ->toJson();
        }
        return view('admin.image_banner.index');
    }

    public function addImageBanner(){
        return view('admin.image_banner.add');
    }
    public function saveImageBanner(Request $request)
    {
        $msg = [
            'image.required' => 'Choose Image banner Image.',
            'type.required' => 'Choose Image banner Type.',
        ];
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required'
        ], $msg);
        try {
            $photo = $request->file('image');
            $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
            $destinationPath = public_path('images/banner');
            if($request->get('type')=='Banner') {
                $thumb_img = Image::make($photo->getRealPath())->resize(700, 340);
            }else{
                $thumb_img = Image::make($photo->getRealPath())->resize(800, 335);
            }
            $thumb_img->save($destinationPath.'/'.$imageName,100);
            ImageBannerModel::create([
                'image'=>url('/images/banner/' . $imageName),
                'type'=>$request->get('type'),
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Image Banner Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Image Banner Not added.');
        }
    }
    public function editImageBanner($id){
        $imageBannerById = ImageBannerModel::find($id);
        return view('admin.image_banner.edit', compact('imageBannerById'));
    }

    public function updateImageBanner(Request $request)
    {
        $msg = [
            'type.required' => 'Choose Image banner Type.',
        ];
        $this->validate($request, [
            'type' =>'required'
        ], $msg);

        $id = $request->get('id');
        $image=ImageBannerModel::where('id',$id)->value('image');
        try {
            if(!empty($request->hasFile('image'))){
                $image=explode('/',$image);
                $image=end($image);
                if(file_exists(public_path().'/images/banner/'.$image)){
                    unlink(public_path() . '/images/banner/' . $image);
                }
                $photo = $request->file('image');
                $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
                $destinationPath = public_path('images/banner');
                if($request->get('type')=='Banner') {
                    $thumb_img = Image::make($photo->getRealPath())->resize(700, 340);
                }else{
                    $thumb_img = Image::make($photo->getRealPath())->resize(800, 335);
                }
                $thumb_img->save($destinationPath.'/'.$imageName,100);

                $imageName=url('/images/banner/' . $imageName);
            }else{
                $imageName =$image;
            }

            ImageBannerModel:: where('id', $id)->update([
                'image' => $imageName,
                'type'=>$request->get('type'),
            ]);

            return redirect()->back()->with('success', 'Image banner Updated Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Slider Not Updated.');
        }
    }

    public function active_inactive_image_banner(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            ImageBannerModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_image_banner('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            ImageBannerModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_image_banner('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delImageBanner($id){
        $image=ImageBannerModel::where('id',$id)->value('image');
        $image=explode('/',$image);
        $image=end($image);
        if(file_exists(public_path().'/images/banner/'.$image)) {
            unlink(public_path() . '/images/banner/' . $image);
        }
        ImageBannerModel::where('id',$id)->delete();
        return redirect()->back()->with('success','Image Banner Deleted Successfully !!!');
    }
}
