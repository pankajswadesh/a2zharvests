<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\ProductModel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }
    public function dashboard(){

        if(Auth::user()->hasRole('admin')){
            $users=User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',2)->count();
            $products = ProductModel::select('*')->where('status','<>','Deleted')->orderBy('id','desc')->with('category')->with('sub_category')->with('brand')->count();
            $product_count = ProductModel::rightJoin('supplier_products','supplier_products.product_id','=','products.id')
                ->where('supplier_products.status','<>','Deleted')
                ->orderBy('supplier_products.id','desc')
                ->select('products.*','supplier_products.quantity','supplier_products.price','supplier_products.discount_value','supplier_products.discount_id','supplier_products.status as st')
                ->count();
            $orders = OrderModel::count();
        }else{
            $users=User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',2)->count();
            $products = ProductModel::select('*')->where('status','<>','Deleted')->orderBy('id','desc')->with('category')->with('sub_category')->with('brand')->count();
            $product_count = ProductModel::rightJoin('supplier_products','supplier_products.product_id','=','products.id')
                ->where('supplier_products.status','<>','Deleted')
                ->where('supplier_products.user_id',Auth::user()->id)
                ->orderBy('supplier_products.id','desc')
                ->select('products.*','supplier_products.quantity','supplier_products.price','supplier_products.discount_value','supplier_products.discount_id','supplier_products.status as st')
                ->count();
            $orders = OrderDetailsModel::where('supplier_id',Auth::user()->id)->groupBy('order_id')->count();
        }

        return view('admin.dashboard.index',compact('users','products','product_count','orders'));
    }

    public function changePassForm()
    {
        return view('admin.password.changePassword');
    }

    public function ChangePass(Request $request)
    {
        $msg = [
            'old_pass.required' => 'Enter Your Old Password',
            'new_pass.required' => 'Enter Your New Password',
            'confirm_pass.required' => 'Enter Your Confirm Pasword',
        ];
        $this->validate($request, [
            'old_pass' => 'required',
            'new_pass' => 'required',
            'confirm_pass' => 'required',
        ], $msg);
        $old_pass=$request->old_pass;
        $new_pass=$request->new_pass;
        $confirm_pass=$request->confirm_pass;
        $id=Auth::user()->id;
        $pass=User::where('id',$id)->value('password');
        if(Hash::check($old_pass,$pass)){
            if($new_pass==$confirm_pass){
                $password=Hash::make($new_pass);
                $changePass=User::where('id',$id)->update([
                    'password' => $password,
                ]);
                if($changePass==true){
                    return redirect()->back()->with('success',"Password Updated Sucessfully !!!" );
                }
            }
            else{
                return redirect()->back()->with('error',"New Password and Confirm Password are Not Matched !!!" );
            }
        }
        else{
            return redirect()->back()->with('error',"Old Password Not Matched !!!" );
        }

    }

    public function profile($id){
        $userById=User::where('id',$id)->first();

        return view('admin.profile.index',compact('userById'));
    }

    public function updateProfile(Request $request){
        $msg = [
            'user_name.required' => 'Enter Your User Name',
            'email.required' => 'Enter Your Email',
            'phone.required' => 'Enter Your Phone No',
        ];
        $this->validate($request, [
            'user_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($request->get('id'))],
            'phone' => ['required', Rule::unique('users')->ignore($request->get('id'))],
        ], $msg);

        $id = $request->get('id');
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $phone = $request->get('phone');
        User:: where('id',$id)->update([
            'user_name' => $user_name,
            'email' => $email,
            'phone' => $phone,
        ]);
        return redirect()->back()->with('success', 'Profile Updated Successfully !!!');
    }
}
