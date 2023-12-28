<?php

namespace App\Http\Controllers\Admin\Department;

use App\Model\DepartmentModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    public function index()
    {

        if(request()->ajax()) {
            $data = DepartmentModel::select('*')->where('status','<>','Deleted')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('dept_name', function ($data) {
                    return ucfirst($data->dept_name);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editDepartment', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delDepartment', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_department('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_department('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.department.index');
    }

    public function addDepartment()
    {
        return view('admin.department.add');
    }

    public function saveDepartment(Request $request)
    {
        $msg = [
            'dept_name.required' => 'Enter Department Name.',
        ];
        $this->validate($request, [
            'dept_name' => 'required',
        ], $msg);
        $dept_name = $request->get('dept_name');
        try {
            DepartmentModel::create([
                'dept_name'=>$dept_name,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Department Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Department Not addded.');
        }

    }

    public function editDepartment($id)
    {
        $departmentById = DepartmentModel::find($id);
        return view('admin.department.edit', compact('departmentById'));
    }

    public function updateDepartment(Request $request)
    {
        $msg = [
            'dept_name.required' => 'Enter Department Name.',
        ];
        $this->validate($request, [
            'dept_name' => 'required',
        ], $msg);

        $id = $request->get('id');
        $dept_name = $request->get('dept_name');
        try {
            DepartmentModel:: where('id', $id)->update([
                'dept_name' => $dept_name,
            ]);

            return redirect()->back()->with('success', 'Department Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Department Not Updated.');
        }
    }

    public function delDepartment($id)
    {
        DepartmentModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Department Deleted Successfully !!!');
    }

    public function active_inactive_department(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            DepartmentModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_department('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            DepartmentModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_department('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }




}
