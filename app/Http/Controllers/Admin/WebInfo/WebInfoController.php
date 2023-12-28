<?php

namespace App\Http\Controllers\Admin\WebInfo;

use App\Model\WebInfoModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class WebInfoController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $data = WebInfoModel::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editWebInfo', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.web_info.index');
    }
    public function edit($id)
    {
        $info = WebInfoModel::find($id);
        return view('admin.web_info.edit', compact('info'));
    }

    public function update(Request $request,$id)
    {
        $msg = [
            'value.required' => 'Enter value.'
        ];
        $this->validate($request, [
            'value' => 'required'
        ], $msg);
        WebInfoModel::where('id',$id)->update($request->except('_token'));
        return redirect()->back()->with('success', 'Info updated successfully !!!');

    }
}
