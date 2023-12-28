<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Model\BankDetailsModel;
use App\Model\ShopDetailsModel;
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

class SupplierController extends Controller
{
    public function index()
    {

        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            }else{
                $data = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.parent_id',Auth::user()->id)->where('role_user.role_id', 3)->get();
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
                ->addColumn('vendor_commision', function ($data) {
                    return $data->vendor_commision;
                })
                ->addColumn('manager_name', function ($data) {
                    if($data->parent_id==null){
                        return 'Not Set';
                    }else{
                        return User::where('id',$data->parent_id)->value('user_name');
                    }
                })
                ->addColumn('available_distance', function ($data) {
                    return $data->available_distance." Km";
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
                ->rawColumns(['action','available_distance'])
                ->toJson();
        }
        return view('admin.supplier.index');

    }

    public function addSupplier()
    {
        $managers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',5)->get();
        return view('admin.supplier.add',compact('managers'));
    }

    public function saveSupplier(Request $request)
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
            'vendor_commision.required' => 'Enter Vendor Commission.',
            'available_distance.required' => 'Enter distance for available.',
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email'=>'required|email|unique:users',
            'phone'=>'required',
            'location'=>'required',
            'vendor_commision'=>'required',
            'available_distance'=>'required',
        ], $msg);
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $location = $request->get('location');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $vendor_commision = $request->get('vendor_commision');
        $available_distance = $request->get('available_distance');
        $sms=new datavalue();
        $referal_code=$sms->getUniqueCode('REF');
        $role_id = 3;
        $User = new User();
        $User->referal_code = $referal_code;
        $User->user_name = $user_name;
        $User->api_token = sha1(time());
        $User->email = $email;
        $User->phone = $phone;
        $User->location = $location;
        $User->latitude = $latitude;
        $User->longitude = $longitude;
        $User->vendor_commision = $vendor_commision;
        $User->password = bcrypt('123456');
        $User->status = 'Active';
        $User->parent_id = $parent_id;
        $User->available_distance = $available_distance;
        $User->save();
        $User->attachRole($role_id);
        ShopDetailsModel::create([
            'user_id'=>$User->id
        ]);
        BankDetailsModel::create([
            'user_id'=>$User->id
        ]);
        return redirect()->back()->with('success','Supplier Added Successfully !!!');
    }

    public function editSupplier($id){
        $userById = User::where('id', $id)->first();
        $managers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('role_user.role_id',5)->get();
        return view('admin.supplier.edit', compact('userById','managers'));
    }

    public function updateSupplier(Request $request)
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
            'vendor_commision.required' => 'Enter Vendor Commission.',
            'available_distance.required' => 'Enter distance for available.',
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($request->get('id'))],
            'phone' => 'required',
            'location'=>'required',
            'vendor_commision'=>'required',
            'available_distance'=>'required',
        ], $msg);

        $id = $request->get('id');
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $location = $request->get('location');
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $vendor_commision = $request->get('vendor_commision');
        $available_distance = $request->get('available_distance');
        User::where('id',$id)->update([
            'user_name' => $user_name,
            'email' => $email,
            'phone' => $phone,
            'location' => $location,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'vendor_commision' => $vendor_commision,
            'available_distance' =>$available_distance,
            'parent_id' => $parent_id
        ]);

        return redirect()->back()->with('success', 'Supplier Updated Successfully !!!');
    }


    public function active_inactive_supplier(Request $request){
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


    public function delSupplier($id)
    {
        User::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success', 'Supplier  Deleted Successfully !!!');
    }

    public function viewSupplier($id)
    {
        $shopDetails = ShopDetailsModel::where('user_id',$id)->first();
        $bankDetails = BankDetailsModel::where('user_id',$id)->first();
        return view('admin.supplier.view',compact('shopDetails','bankDetails'));
    }
}
