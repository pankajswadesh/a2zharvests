<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\RoleUser;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Session\Session;

class AdminController extends Controller
{
    public function index(){
        return view('admin.login');
    }

    public function Check_login(Request $request)
    {
        $msg = [
            'email.required' => 'Enter Your Email',
            'password.required' => 'Enter Your Password',
        ];
        $this->validate($request, [
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:3'

        ], $msg);

        $email = $request->get('email');
        $pass = $request->get('password');
        $uid = User::where('email', $email)->orWhere('phone', $email)->value('id');
        $count = User::where('email', $email)->orWhere('phone', $email)->count();
        if($count==1) {
            $role_id = RoleUser::where('user_id', $uid)->value('role_id');
            $role = Role::where('id', $role_id)->value('name');
            if ($role == 'admin' || $role == 'manager' || $role == 'supplier') {
                if (Auth::attempt(array('email' => $email, 'password' => $pass, 'status' => 'Active'), true)) {
                    return redirect(route('admin::dashboard'));
                } else {
                    return redirect()->back()->with('error', 'Login Failed !!! Please check Your Email and Password.');
                }
            }else{
                return redirect()->back()->with('error', 'Login Failed !!! You are not a admin or supplier.');
            }
        }else{
            return redirect()->back()->with('error', 'Login Failed !!! Your email is not registered.');
        }
    }

    public function logout(Request $request){
        if(Auth::user()->hasRole('supplier')){
            $url = 'supplier';
            Auth::logout();
            return redirect(route('supplier'))->with('logout','Logout Successfully !!!');
        }else{
            Auth::logout();
            return redirect(route('admin'))->with('logout','Logout Successfully !!!');
        }
    }

}
