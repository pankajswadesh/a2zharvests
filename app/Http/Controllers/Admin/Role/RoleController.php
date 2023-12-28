<?php

namespace App\Http\Controllers\Admin\Role;

use App\Permission;
use App\PermissionRole;
use App\Role;
use App\RoleUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = Role::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->name;
                })
                ->addColumn('display_name', function ($data) {
                    return $data->display_name;
                })
                ->addColumn('description', function ($data) {
                    return $data->description;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editRole', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delRole', ['id' => $data->id])."'";

                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.role.index');
    }

    public function addRole()
    {
        $permissions = [];
        $allPermissionGroups = Permission::select('group_name')->where('group_name','!=','Permission')->groupBy('group_name')->get();
        foreach ($allPermissionGroups as $group){
            $datas = Permission::where('group_name',$group->group_name)->get();
            foreach ($datas as $data){
                $permissions[$group->group_name][] = $data;
            }
        }
        return view('admin.role.add',compact('permissions'));
    }

    public function saveRole(Request $request)
    {
        $msg = [
            'name.required' => 'Enter Role Name',
            'display_name.required' => 'Enter Role Display Name',
            'description.required' => 'Enter Role Description',
        ];
        $this->validate($request, [
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required',
        ], $msg);

        $name = $request->get('name');
        $display_name = $request->get('display_name');
        $description = $request->get('description');

        $Role = new Role();
        $Role->name = $name;
        $Role->display_name = $display_name;
        $Role->description = $description;
        $Role->save();

        if($request->has('permissions')) {
            $permissions = $request->get('permissions');
            foreach ($permissions as $permission) {
                $Role->attachPermission($permission);
            }
        }
        return redirect()->back()->with('success', 'Role Added Successfully !!!');
    }

    public function editRole($id)
    {
        $roleById = Role::findOrFail($id);
        $permissions = [];
        $allPermissionGroups = Permission::select('group_name')->groupBy('group_name')->get();
        foreach ($allPermissionGroups as $group){
            $datas = Permission::where('group_name',$group->group_name)->where('group_name','!=','Permission')->get();
            foreach ($datas as $data){
                $checkPermission = PermissionRole::where('permission_id',$data->id)->where('role_id',$id)->count();
                $permissions[$group->group_name][] = [
                    'id'=>$data->id,
                    'group_name'=>$data->group_name,
                    'name'=>$data->name,
                    'display_name'=>$data->display_name,
                    'description'=>$data->description,
                    'count'=>$checkPermission,
                ];
            }
        }
        return view('admin.role.edit', compact('roleById','permissions'));
    }

    public function updateRole(Request $request)
    {
        $msg = [
            'name.required' => 'Enter Role Name',
            'display_name.required' => 'Enter Role Display Name',
            'description.required' => 'Enter Role Description',
        ];
        $this->validate($request, [
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required',
        ], $msg);

        $id = $request->get('id');
        $name = $request->get('name');
        $display_name = $request->get('display_name');
        $description = $request->get('description');
        $SelectedPermissions = $request->get('permissions');
        $allPermission = Permission::select('id')->where('group_name','!=','Permission')->get();
        $Role = Role::findOrFail($id);
        foreach ($allPermission as $permission){
            if($request->has('permissions') && in_array($permission->id,$SelectedPermissions)){ //selected
                $checkPermission = PermissionRole::where('permission_id',$permission->id)->where('role_id',$Role->id)->count();
                if(empty($checkPermission)) {
                    $Role->attachPermission($permission);
                }
            }else{
                $checkPermission = PermissionRole::where('permission_id',$permission->id)->where('role_id',$Role->id)->count();
                if(!empty($checkPermission)) {
                    $Role->detachPermission($permission);
                }
            }
        }

        $Role->name = $name;
        $Role->display_name = $display_name;
        $Role->description = $description;
        $Role->save();


        return redirect()->back()->with('success', 'Role updated successfully !!!');

    }

    public function delRole($id)
    {
        Role::where('id',$id)->delete();
        PermissionRole::where('role_id',$id)->delete();
        return redirect()->back()->with('success', 'Role deleted successfully !!!');
    }


}
