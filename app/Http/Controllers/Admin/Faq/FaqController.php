<?php

namespace App\Http\Controllers\Admin\Faq;

use App\Model\FaqModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class FaqController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $data = FaqModel::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editFaq', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delFaq', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_faq('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_faq('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.faq.index');
    }

    public function add()
    {
        return view('admin.faq.add');
    }

    public function save(Request $request)
    {
        $msg = [
            'question.required' => 'Enter Question.',
            'answer.required' => 'Enter Answer.'
        ];
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required'
        ], $msg);
        FaqModel::create($request->except('_token'));
        return redirect()->back()->with('success', 'Faq Added Successfully !!!');
    }

    public function edit($id)
    {
        $info = FaqModel::find($id);
        return view('admin.faq.edit', compact('info'));
    }

    public function update(Request $request,$id)
    {
        $msg = [
            'question.required' => 'Enter Question.',
            'answer.required' => 'Enter Answer.'
        ];
        $this->validate($request, [
            'question' => 'required',
            'answer' => 'required'
        ], $msg);
        FaqModel::where('id',$id)->update($request->except('_token'));
        return redirect()->back()->with('success', 'Faq updated successfully !!!');

    }
    public function active_inactive_faq(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            FaqModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_faq('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            FaqModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_faq('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
    }
    public function delete($id)
    {
        FaqModel::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Faq deleted successfully !!!');
    }
}
