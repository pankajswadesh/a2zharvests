<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Model\OrderDetailsModel;
use App\Model\UserLoginHistoryModel;
use App\repo\datavalue;
use App\Role;
use App\RoleUser;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 4)->get();
            }else{
                $data = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.parent_id',Auth::user()->id)->where('role_user.role_id', 4)->get();
            }
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
                ->addColumn('created_at', function ($data) {
                    return $data->created_at;
                })
                ->addColumn('default_delivery', function ($data) {
                    return $data->is_default_delivery;
                })
                ->addColumn('manager_name', function ($data) {
                    if($data->parent_id==null){
                        return 'Not Set';
                    }else{
                        return User::where('id',$data->parent_id)->value('user_name');
                    }
                })
                ->addColumn('total_tips', function ($data) {
                    $tips = OrderDetailsModel::where('delivery_id',$data->id)->sum('delivery_tips');
                    return $tips;
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
                ->rawColumns(['action','total_tips'])
                ->toJson();
        }
        return view('admin.delivery.index');
    }

    public function addDelivery(){
        $managers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',5)->get();
        return view('admin.delivery.add',compact('managers'));
    }
    public function saveDelivery(Request $request)
    {
        if(Auth::user()->hasRole('admin')) {
            $parent_id = null;
        }else{
            $parent_id = Auth::user()->id;
        }
        if($request->has('parent_id') && $request->get('parent_id')!=""){
            $parent_id = $request->get('parent_id');
        }
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
        $role_id = 4;
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
        $User->parent_id = $parent_id;
        $User->save();
        $User->attachRole($role_id);
        return redirect()->back()->with('success','Delivery Boy Added Successfully !!!');
    }
    public function editDelivery($id){
        $userById = User::where('id', $id)->first();
        $roles = Role::get();
        $managers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',5)->get();
        return view('admin.delivery.edit', compact('userById','roles','managers'));
    }

    public function updateDelivery(Request $request)
    {
        if(Auth::user()->hasRole('admin')) {
            $parent_id = null;
        }else{
            $parent_id = Auth::user()->id;
        }
        if($request->has('parent_id') && $request->get('parent_id')!=""){
            $parent_id = $request->get('parent_id');
        }
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
        $is_default_delivery = $request->get('is_default_delivery');
        User::where('id',$id)->update([
            'user_name' => $user_name,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'is_default_delivery' => $is_default_delivery,
            'parent_id' => $parent_id
        ]);
        User::where('id','<>',$id)->update([
            'is_default_delivery'=>'No'
        ]);

        return redirect()->back()->with('success', 'Delivery Boy Updated Successfully !!!');
    }

    public function active_inactive_delivery(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            User::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_delivery('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            User::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_delivery('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delDelivery($id)
    {
        User:: where('id', $id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success', 'Delivery Boy Deleted Successfully !!!');
    }

}
