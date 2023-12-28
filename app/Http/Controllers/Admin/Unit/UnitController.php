<?php

namespace App\Http\Controllers\Admin\Unit;

use App\Model\UnitModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $data = UnitModel::select('*')->where('status','<>','Deleted');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('unit_name', function ($data) {
                    return ucfirst($data->unit_name);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editUnit', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delUnit', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_unit('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_unit('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.unit.index');
    }

    public function addUnit()
    {
        return view('admin.unit.add');
    }

    public function saveUnit(Request $request)
    {
        $msg = [
            'unit_name.required' => 'Enter Unit Name.',
        ];
        $this->validate($request, [
            'unit_name' => 'required|alpha',
        ], $msg);
        $unit_name = $request->get('unit_name');
        try {
            UnitModel::create([
                'unit_name'=>$unit_name,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Unit Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Unit Not addded.');
        }
    }

    public function editUnit($id)
    {
        $unitById = UnitModel::find($id);
        return view('admin.unit.edit', compact('unitById'));
    }


    public function updateUnit(Request $request)
    {
        $msg = [
            'unit_name.required' => 'Enter unit Name.',
        ];
        $this->validate($request, [
            'unit_name' => 'required|alpha',
        ], $msg);

        $id = $request->get('id');
        $unit_name = $request->get('unit_name');
        try {
            UnitModel:: where('id', $id)->update([
                'unit_name' => $unit_name,
            ]);

            return redirect()->back()->with('success', 'Unit Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Unit Not Updated.');
        }
    }

    public function delUnit($id)
    {
        UnitModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Unit Deleted Successfully !!!');
    }


    public function active_inactive_unit(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            UnitModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_unit('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            UnitModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_unit('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }


}
