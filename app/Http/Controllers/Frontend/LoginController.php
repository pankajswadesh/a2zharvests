<?php

namespace App\Http\Controllers\Frontend;

use App\Model\SettingModel;
use App\Model\TransactionDetailsModel;
use App\Model\UserLoginHistoryModel;
use App\Model\UserOtpModel;
use App\repo\datavalue;
use App\repo\Response;
use App\Role;
use App\RoleUser;
use App\User;
use Auth;
use Crypt;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class LoginController extends Controller
{

  
    
    public function user_signup(Request $request)
    {
     
        $msg = [
            'phone.required' => 'Enter your phone no.'
        ];
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
        ], $msg);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $sms = new datavalue();
                $user_referal_code = $sms->getUniqueCode('REF');
                $referal_code = $request->referal_code;
                if ($referal_code != '') {
                    $refer_by_id = User::where('referal_code', $referal_code)->value('id');
                    if ($refer_by_id != '') {
                        $refered_by = $refer_by_id;
                    } else {
                        $data = [];
                        $msg = 'Invalid Referral Code.';
                        return array('status' => 'error', 'data' => $data, 'msg' => $msg);
                    }
                } else {
                    $refered_by = 1;
                }
                $role_id = 2;
                $phone = $request->phone;
                $user = User::where('phone', $phone)->first();
               
                $otp=mt_rand(100000, 999999);
                //  $otp = 123456; 
                if (empty($user)) {
                    $user = new User();
                    $user->referal_code = $user_referal_code;
                    $user->phone = $phone;
                    $user->password = bcrypt(rand(111111, 999999));
                    $user->api_token = sha1(time());
                    $user->status = 'Inactive';
                    $user->refered_by = $refered_by;
                    $user->save();
                    $user->attachRole($role_id);
                }
                $check_otp =  UserOtpModel::where('token', $user->api_token)->count();
                if ($check_otp == 0) {
                    UserOtpModel::create([
                        'token' => $user->api_token,
                        'otp' => $otp,
                    ]);
                } else {
                    UserOtpModel::where('token', $user->api_token)->update([
                        'otp' => $otp,
                    ]);
                }
                $sms_status = $sms->send_sms($phone,$otp);
               
                    $sms_status['status'] = 'success'; 
                if ($sms_status['status'] == 'success') {
                    $data = $user;
                    $msg = 'Successfully Registered.Please Verify your Account by otp.';
                    DB::commit();
                    return array('status' => 'success', 'data' => $data, 'msg' => $msg);
                } else {
                    $data = [];
                    $msg = 'Registration Failed.';
                    DB::rollback();
                    return array('status' => 'error', 'data' => $data, 'msg' => $msg);
                }
            } catch (Exception $e) {
                $data = [];
                $msg = 'Registration Failed.';
                DB::rollback();
                return array('status' => 'error', 'data' => $data, 'msg' => $msg);
            }
        } else {
            $data = [];
            $msg =  $validator->errors()->first();
            return array('status' => 'error', 'data' => $data, 'msg' => $msg);
        }
    }
    public function verify_account(Request $request)
    {
        $token = $request->get('token');
        $otp = $request->get('otp');
        $msg = [
            //'token.required' => 'Enter Your Token.',
            'otp.required' => 'Enter Your Otp.',
        ];
        $validator = Validator::make($request->all(), [
            //'token' => 'required',
            'otp' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $user_otp = UserOtpModel::where('token', $token)->value('otp');
            if ($otp == $user_otp) {
                if (\Session::has('location')) {
                    $location = \Session::get('location');
                    
                    $latitude = $location["latitude"];
                    $longitude = $location["longitude"];
                    $location = $location["address"];
                    User::where('api_token', $token)->update([
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'location' => $location,
                        'status' => 'Active'
                    ]);
                }
                $sms = new datavalue();
                $refer_by_id = User::where('api_token', $token)->value('refered_by');
                if ($refer_by_id != 1) {
                    $refer_for_id = User::where('api_token', $token)->value('id');
                    $check = TransactionDetailsModel::where('refer_for_id', $refer_for_id)->count();
                    if ($check == 0) {
                        $transaction_id = $sms->getUniqueCode('WALLET');
                        $referal_amount = SettingModel::where('id', 1)->value('value');
                        TransactionDetailsModel::create([
                            'user_id' => $refer_by_id,
                            'refer_for_id' => $refer_for_id,
                            'transaction_id' => $transaction_id,
                            'amount' => $referal_amount,
                            'transaction_type' => 'Referral',
                            'status' => 'Success',
                        ]);
                        $wallet = User::find($refer_by_id);
                        $wallet->increment('wallet_amount', $referal_amount);
                    }
                }
                $updateotp = mt_rand(100000, 999999);
                // $updateotp = '123456';
                UserOtpModel::where('token', $token)->update([
                    'otp' => $updateotp,
                ]);
                $data = [];
                $user_id =  User::where('api_token', $token)->value("id");
                $logeinUser = User::where('id',$user_id)->first();
                $role_id = RoleUser::where('user_id', $user_id)->value('role_id');
                $role = Role::where('id', $role_id)->value('name');
                if ($role == 'user') {
                     Auth::loginUsingId($user_id,true);
                   //Auth::login($logeinUser);
                    $msg = 'Your have successfully verify and loggedIn.';
                    return array('status' => 'success', 'data' => $data, 'msg' => $msg);
                } else {
                    return array('status' => 'error', 'data' => $data, 'msg' => 'Phone no. not exist as user.');
                }
                
            } else {
                $data = [];
                $msg = 'Otp Not Matched.';
                return array('status' => 'error', 'data' => $data, 'msg' => $msg);
            }
        } else {
            $data = [];
            $msg =  $validator->errors()->first();
            return array('status' => 'error', 'data' => $data, 'msg' => $msg);
        }
    }
    public function user_logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
