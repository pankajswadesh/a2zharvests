<?php

namespace App\Http\Controllers\Admin\Slider;

use App\Model\SliderModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;

class SliderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = SliderModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()

                ->addColumn('slider_image', function ($data) {
                    return '<img src="'.$data->slider_image.'" width="100px" height="100px"/>';
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editSlider', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delSlider', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_slider('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_slider('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action','slider_image'])
                ->toJson();
        }
        return view('admin.slider.index');
    }

    public function addSlider(){
        return view('admin.slider.add');
    }
    public function saveSlider(Request $request)
    {
        $msg = [
            'image.required' => 'Choose Slider Image.',
        ];
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $msg);
        try {
            $photo = $request->file('image');
            $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
            $destinationPath = public_path('images/slider');
            $thumb_img = Image::make($photo->getRealPath())->resize(1680, 450);
            $thumb_img->save($destinationPath.'/'.$imageName,100);
            SliderModel::create([
                'slider_image'=>url('/images/slider/' . $imageName),
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Slider Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Slider Not addded.');
        }
    }
    public function editSlider($id){
        $sliderById = SliderModel::find($id);
        return view('admin.slider.edit', compact('sliderById'));
    }

    public function updateSlider(Request $request)
    {
        $msg = [
            'image.required' => 'Choose Slider Image.',
        ];
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $msg);

        $id = $request->get('id');
        $slider_image=SliderModel::where('id',$id)->value('slider_image');
        try {
            if(!empty($request->hasFile('image'))){
                $slider_image=explode('/',$slider_image);
                $slider_image=end($slider_image);
                if(file_exists(public_path().'/images/slider/'.$slider_image)){
                        unlink(public_path() . '/images/slider/' . $slider_image);
                }
                $photo = $request->file('image');
                $imageName =  str_random(6) . '_' .time().'.'.$photo->getClientOriginalExtension();
                $destinationPath = public_path('images/slider');
                $thumb_img = Image::make($photo->getRealPath())->resize(1680, 450);
                $thumb_img->save($destinationPath.'/'.$imageName,100);
                $imageName=url('/images/slider/' . $imageName);
            }else{
                $imageName =$slider_image;
            }

            SliderModel:: where('id', $id)->update([
                'slider_image' => $imageName,
            ]);

            return redirect()->back()->with('success', 'Slider Updated Successfully !!!');

        }catch(Exception $e) {
            return $e;
             return redirect()->back()->with('error','Slider Not Updated.');
        }
    }

    public function active_inactive_slider(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            SliderModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_slider('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            SliderModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_slider('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delSlider($id){
        $slider_image=SliderModel::where('id',$id)->value('slider_image');
        $slider_image=explode('/',$slider_image);
        $slider_image=end($slider_image);
        if(file_exists(public_path().'/images/slider/'.$slider_image)) {
            unlink(public_path() . '/images/slider/' . $slider_image);
        }
        SliderModel::where('id',$id)->delete();
        return redirect()->back()->with('success','Slider Deleted Successfully !!!');
    }
}
