<?php

namespace App\Http\Controllers\Admin\Manager;

use App\Model\UserLoginHistoryModel;
use App\repo\datavalue;
use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ManagerController extends Controller
{
    public function index()
    {
        if(request()->ajax()) {
            $data = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',5)->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($data) {
                    return $data->user_name;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('phone', function ($data) {
                    return $data->phone;
                })
                ->addColumn('location', function ($data) {
                    return $data->location;
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at;
                })
                ->addColumn('role', function ($data) {
                    $role_id = RoleUser::where('user_id', $data->id)->value('role_id');
                    $role = Role::where('id', $role_id)->value('name');
                    return $role;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editManager', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delManager', ['id' => $data->id])."'";
                    $url_suppliers = route('admin::managerSupplier', ['id' => $data->id]);
                    $url_delivery = route('admin::managerDelivery', ['id' => $data->id]);
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_manager('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_manager('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;
                                        ';
                    $edit .= '<a href="' . $url_suppliers . '" class="btn btn-xs btn btn-primary">Suppliers</a>&emsp;';
                    $edit .= '<a href="' . $url_delivery . '" class="btn btn-xs btn btn-info">Delivery</a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.manager.index');
    }

    public function add()
    {
        return view('admin.manager.add');
    }

    public function save(Request $request)
    {
        $msg = [
            'user_name.required' => 'Enter Your User Name.',
            'email.required' => 'Enter Your email.',
            'phone.required' => 'Enter Your Phone No.',
            'location.required' => 'Enter Your Location.',
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email'=>'required|email|unique:users',
            'phone'=>'required',
            'location'=>'required',
        ], $msg);
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $location = $request->get('location');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $sms=new datavalue();
        $referal_code=$sms->getUniqueCode('REF');
        $role_id = 5;
        $User = new User();
        $User->referal_code = $referal_code;
        $User->user_name = $user_name;
        $User->api_token = sha1(time());
        $User->email = $email;
        $User->phone = $phone;
        $User->location = $location;
        $User->latitude = $latitude;
        $User->longitude = $longitude;
        $User->password = bcrypt('123456');
        $User->status = 'Active';
        $User->save();
        $User->attachRole($role_id);
        return redirect()->back()->with('success','Manager Added Successfully !!!');
    }

    public function edit($id){
        $userById = User::where('id', $id)->first();
        return view('admin.manager.edit', compact('userById'));
    }

    public function update(Request $request)
    {
        $msg = [
            'user_name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your email',
            'phone.required' => 'Enter Your Phone No',
            'location.required' => 'Enter Your Location.'
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($request->get('id'))],
            'phone' => 'required',
            'location'=>'required'
        ], $msg);

        $id = $request->get('id');
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $location = $request->get('location');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        User:: where('id',$id)->update([
            'user_name' => $user_name,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        return redirect()->back()->with('success', 'Manager Updated Successfully !!!');
    }


    public function active_inactive_manager(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            User::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_supplier('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            User::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_supplier('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delete($id)
    {
        User::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success', 'Manager Deleted Successfully !!!');
    }
    public function managerSupplier($id){
        $manager_details = User::find($id);
        if(request()->ajax()) {
            $data = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.parent_id',$id)->where('role_user.role_id',3);
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($data) {
                    return $data->user_name;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('phone', function ($data) {
                    return $data->phone;
                })
                ->addColumn('location', function ($data) {
                    return $data->location;
                })
                ->addColumn('login_time', function ($data) {
                    $log_in_time=UserLoginHistoryModel::where('user_id',$data->id)->value('login_time');
                    return $log_in_time;
                })
                ->addColumn('logout_time', function ($data) {
                    $log_out_history=UserLoginHistoryModel::where('user_id',$data->id)->value('logout_time');
                    return $log_out_history;
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at;
                })
                ->addColumn('vendor_commision', function ($data) {
                    return $data->vendor_commision;
                })
                ->addColumn('role', function ($data) {
                    $role_id = RoleUser::where('user_id', $data->id)->value('role_id');
                    $role = Role::where('id', $role_id)->value('name');
                    return $role;
                })
                ->addColumn('action', function ($data) {
                    $view_data = route('admin::viewSupplier', ['id' => $data->id]);
                    $url_update = route('admin::editSupplier', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delSupplier', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_supplier('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_supplier('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;
                                        <a href="' . $view_data . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-eye-open"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.manager.suppliers',compact('manager_details'));
    }
    public function assignSupplier($manager_id){
        $suppliers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',3)->where(function ($query) use ($manager_id){
            $query->where('parent_id',$manager_id)->orWhere('parent_id',null);
        })->get();
        $exist_ids = User::join('role_user','role_user.user_id','=','users.id')->where('users.status','<>','Deleted')->where('role_user.role_id',3)->where('users.parent_id',$manager_id)->pluck('users.id')->toArray();
        return view('admin.manager.map_suppliers',compact('suppliers','manager_id','exist_ids'));
    }
    public function assignSupplierSubmit(Request $request,$manager_id){
        User::where('parent_id',$manager_id)->whereHas('roles', function ($query) {
            $query->where('roles.id', 3);
        })->update([
            'parent_id' => null,
        ]);
        User::whereIn('id',$request->supplier_ids)->update([
          'parent_id' => $manager_id,
        ]);
        return redirect()->back()->with('success', 'Supplier Assigned Successfully !!!');
    }
    public function managerDelivery($id){
        $manager_details = User::find($id);
        if(request()->ajax()) {
            $data = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.parent_id',$id)->where('role_user.role_id',4);

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($data) {
                    return $data->user_name;
                })
                ->addColumn('email', function ($data) {
                    return $data->email;
                })
                ->addColumn('phone', function ($data) {
                    return $data->phone;
                })
                ->addColumn('location', function ($data) {
                    return $data->location;
                })
                ->addColumn('login_time', function ($data) {
                    $log_in_time=UserLoginHistoryModel::where('user_id',$data->id)->value('login_time');
                    return $log_in_time;
                })
                ->addColumn('logout_time', function ($data) {
                    $log_out_history=UserLoginHistoryModel::where('user_id',$data->id)->value('logout_time');
                    return $log_out_history;
                })
                ->addColumn('created_at', function ($data) {
                    return $data->created_at;
                })
                ->addColumn('default_delivery', function ($data) {
                    return $data->is_default_delivery;
                })
                ->addColumn('role', function ($data) {
                    $role_id = RoleUser::where('user_id', $data->id)->value('role_id');
                    $role = Role::where('id', $role_id)->value('name');
                    return $role;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editDelivery', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delDelivery', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_delivery('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_delivery('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';




                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.manager.delivery',compact('manager_details'));
    }
    public function assignDelivery($manager_id){
        $delivery = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',4)->where(function ($query) use ($manager_id){
            $query->where('parent_id',$manager_id)->orWhere('parent_id',null);
        })->get();
        $exist_ids = User::join('role_user','role_user.user_id','=','users.id')->where('users.status','<>','Deleted')->where('role_user.role_id',4)->where('users.parent_id',$manager_id)->pluck('users.id')->toArray();
        return view('admin.manager.map_delivery',compact('delivery','manager_id','exist_ids'));
    }
    public function assignDeliverySubmit(Request $request,$manager_id){
        User::where('parent_id',$manager_id)->whereHas('roles', function ($query) {
            $query->where('roles.id', 4);
        })->update([
            'parent_id' => null,
        ]);
        User::whereIn('id',$request->delivery_ids)->update([
            'parent_id' => $manager_id,
        ]);
        return redirect()->back()->with('success', 'Delivery Assigned Successfully !!!');
    }
}
