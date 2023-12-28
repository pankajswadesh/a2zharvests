<?php

namespace App\Http\Controllers\Admin\Seo;

use App\Model\SeoDataModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SeoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = SeoDataModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editSeoData', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.seo_data.index');
    }

    public function edit($id){
        $info = SeoDataModel::find($id);
        return view('admin.seo_data.edit', compact('info'));
    }

    public function update(Request $request,$id)
    {
        $msg = [
            'title.required' => 'Enter Seo Title.',
            'description.required' => 'Enter Seo Description.',
            'keywords.required' => 'Enter Seo Keywords.',
        ];
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'keywords' => 'required',
        ], $msg);
        $data = $request->except('_token');
        try {
            SeoDataModel:: where('id', $id)->update($data);
            return redirect()->back()->with('success', 'Seo Data Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Seo Data Not Updated.');
        }
    }
}
