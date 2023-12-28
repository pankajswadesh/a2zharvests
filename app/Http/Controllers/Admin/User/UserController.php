<?php

namespace App\Http\Controllers\Admin\User;

use App\Model\UserLoginHistoryModel;
use App\repo\datavalue;
use App\Role;
use App\RoleUser;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Validation\Rule;
use function Psy\sh;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',2)->get();

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
                ->addColumn('role', function ($data) {
                    $role_id = RoleUser::where('user_id', $data->id)->value('role_id');
                    $role = Role::where('id', $role_id)->value('name');
                    return $role;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editUser', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delUser', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_user('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_user('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';




                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.user.index');
    }

    public function addUser(){
        return view('admin.user.add');
    }
    public function saveUser(Request $request)
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
            $role_id = 2;
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
            return redirect()->back()->with('success','User Added Successfully !!!');
        }
        public function editUser($id){
            $userById = User::where('id', $id)->first();
            $roles = Role::get();
            return view('admin.user.edit', compact('userById','roles'));
        }

    public function updateUser(Request $request)
    {
        $msg = [
            'user_name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your email',
            'phone.required' => 'Enter Your Phone No',
            'location.required' => 'Enter Your Location.',
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($request->get('id'))],
            'phone' => 'required',
            'location'=>'required',
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

            return redirect()->back()->with('success', 'User Updated Successfully !!!');
    }

    public function active_inactive_user(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            User::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_user('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            User::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_user('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delUser($id)
    {
        User:: where('id', $id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success', 'User Deleted Successfully !!!');
    }

    public function admin_index(){
        $users=User::join('role_user','role_user.user_id','=','users.id')->join('roles','roles.id','=','role_user.role_id')->where('roles.name','<>','User')->where('roles.name','<>','Member User')->select('users.*')->get();
        return view('admin.user.admin_index', compact('users'));
    }

    public function addAdminUser(){
        $roles = Role::where('roles.name','<>','User')->where('roles.name','<>','Member User')->get();
        return view('admin.user.add_admin',compact('roles'));
    }
    public function saveAdminUser(Request $request)
    {
        $msg = [
            'role_id.required' => 'Please Select Role',
            'name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your email',
            'phone.required' => 'Enter Your Phone No',
        ];
        $this->validate($request, [
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ], $msg);
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $role_id = $request->get('role_id');
        $sms=new datavalue();
        $referal_code=$sms->getUniqueCode('REF');
        $User = new User();
        $User->referal_code = $referal_code;
        $User->name = $name;
        $User->email = $email;
        $User->phone = $phone;
        $User->password = bcrypt('123456');
        $User->user_password = base64_encode('123456');
        $User->status = 'Active';
        $User->save();
        $User->attachRole($role_id);
        return redirect()->back()->with('success','User Added Successfully !!!');
    }
    public function editAdminUser($id){
        $userById = User::where('id', $id)->first();
        $roles = Role::where('roles.name','<>','User')->where('roles.name','<>','Member User')->get();
        return view('admin.user.edit_admin', compact('userById','roles'));
    }
    public function updateAdminUser(Request $request)
    {
        $msg = [
            'role_id.required' => 'Please Select Role',
            'name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your email',
            'phone.required' => 'Enter Your Phone No',
        ];
        $this->validate($request, [
            'role_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
        ], $msg);

        $id = $request->get('id');
        $name = $request->get('name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        User:: where('id',$id)->update([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

        return redirect()->back()->with('success', 'User Updated Successfully !!!');
    }

    public function active_inactive_admin_user(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            User::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_admin_user('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            User::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_admin_user('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delAdminUser($id)
    {
//        $Remove = User::findOrFail($id);
//        $Remove->detachRoles($Remove->roles);
//        $Remove->delete();
        User:: where('id', $id)->update([
            'status'=>'Deleted'
                ]);

        return redirect()->back()->with('success', 'User Deleted Successfully !!!');
    }


}
