<?php

namespace App\Http\Controllers\api\v1\Registration;

use App\Http\Controllers\api\v1\Notification\NotificationController;
use App\Mail\ForgetPassword;
use App\Mail\UserVerification;
use App\Model\BankDetailsModel;
use App\Model\DeliveryProfileModel;
use App\Model\SettingModel;
use App\Model\ShopDetailsModel;
use App\Model\TransactionDetailsModel;
use App\Model\UserLoginHistoryModel;
use App\Model\UserOtpModel;
use App\repo\datavalue;
use App\repo\Response;
use App\Role;
use App\RoleUser;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;

class RegistrationController extends Controller
{
    // public function signup(Request $request)
    // {
    //     $msg = [
    //         'phone.required' => 'Enter your phone no. .',
    //         'phone.digits' => 'Enter your valid 10 digit phone no. .',
    //         'latitude.required' => 'Enter Your Latitude.',
    //         'longitude.required' => 'Enter Your Longitude.',
    //     ];
    //     $validator = Validator::make($request->all(), [
    //         'phone' => 'required|digits:10',
    //         'latitude' => 'required',
    //         'longitude' => 'required',
    //     ], $msg);
    //     $latitude = $request->latitude;
    //     $longitude = $request->longitude;
    //     if ($validator->passes()) {
    //         DB::beginTransaction();
    //         try {
    //             $sms = new datavalue();
    //             $user_referal_code = $sms->getUniqueCode('REF');
    //             $role_id = 2;
    //             $phone = $request->phone;
    //             $user = User::where('phone', $phone)->first();
    //             if ($phone =='7878787878') {
    //                 $otp = 123456;
    //             } else {
    //                 $otp = mt_rand(100000, 999999);
    //                 //  $otp = 123456;
    //             }
                
    //             if (empty($user)) {
    //                 $referal_code = $request->referal_code;
    //                 if ($referal_code != '') {
    //                     $refer_by_id = User::where('referal_code', $referal_code)->value('id');
    //                     if ($refer_by_id != '') {
    //                         $refered_by = $refer_by_id;
    //                     } else {
    //                         $data = [];
    //                         $msg = 'Invalid Referral Code.';
    //                         return Response::Error($data, $msg);
    //                     }
    //                 } else {
    //                     $refered_by = 1;
    //                 }
    //                 $user = new User();
    //                 $user->referal_code = $user_referal_code;
    //                 $user->phone = $phone;
    //                 $user->latitude = $latitude;
    //                 $user->longitude = $longitude;
    //                 $user->password = bcrypt(rand(111111, 999999));
    //                 $user->api_token = sha1(time());
    //                 $user->device_id = $request->device_id;
    //                 $user->status = 'Inactive';
    //                 $user->refered_by = $refered_by;
    //                 $user->save();
    //                 $user->attachRole($role_id);
    //             } else {
    //                 if ($request->device_id != "" && $request->device_id != null) {
    //                     User::where("id", $user->id)->update([
    //                         'device_id' => $request->device_id
    //                     ]);
    //                 }
    //             }
    //             $check_otp =  UserOtpModel::where('token', $user->api_token)->count();
    //             if ($check_otp == 0) {
    //                 UserOtpModel::create([
    //                     'token' => $user->api_token,
    //                     'otp' => $otp,
    //                 ]);
    //             } else {
    //                 UserOtpModel::where('token', $user->api_token)->update([
    //                     'otp' => $otp,
    //                 ]);
    //             }
    //             if ($phone =='7878787878') {
    //                 $sms_status['status'] ='success';
    //             } else {
    //                 $sms_status = $sms->send_sms($phone, $otp);
    //             }
    //             \Log::info("Generated OTP: $otp");
               
    //             if ($sms_status['status'] == 'success') {
    //              $data = $user;
    //                 $msg = 'Successfully Registered.Please Verify your Account by otp.';
    //                 DB::commit();
    //                 return Response::Success($data, $msg);
    //             } else {
    //                 $data = [];
    //                 $msg = 'Error in send otp.';
    //                 DB::rollback();
    //                 return Response::Error($data, $sms_status["msg"]);
    //             }
    //         } catch (Exception $e) {
    //             $data = [];
    //             $msg = 'Registration Failed.';
    //             DB::rollback();
    //             return Response::Error($data, $e->getMessage());
    //         }
    //     } else {
    //         $data = [];
    //         $msg =  $validator->errors()->first();
    //         return Response::Error($data, $msg);
    //     }
    // }
    
    public function signup(Request $request)
    {
        $msg = [
            'phone.required' => 'Enter your phone no. .',
            'phone.digits' => 'Enter your valid 10 digit phone no. .',
            'latitude.required' => 'Enter Your Latitude.',
            'longitude.required' => 'Enter Your Longitude.',
        ];
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
            'latitude' => 'required',
            'longitude' => 'required',
        ], $msg);
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $sms = new datavalue();
                $user_referal_code = $sms->getUniqueCode('REF');
                $role_id = 2;
                $phone = $request->phone;
                $user = User::where('phone', $phone)->first();
                if ($phone =='7878787878') {
                    $otp = 123456;
                } else {
                    $otp = mt_rand(100000, 999999);
                }
                if (empty($user)) {
                    $referal_code = $request->referal_code;
                    if ($referal_code != '') {
                        $refer_by_id = User::where('referal_code', $referal_code)->value('id');
                        if ($refer_by_id != '') {
                            $refered_by = $refer_by_id;
                        } else {
                            $data = [];
                            $msg = 'Invalid Referral Code.';
                            return Response::Error($data, $msg);
                        }
                    } else {
                        $refered_by = 1;
                    }
                    $user = new User();
                    $user->referal_code = $user_referal_code;
                    $user->phone = $phone;
                    $user->latitude = $latitude;
                    $user->longitude = $longitude;
                    $user->password = bcrypt(rand(111111, 999999));
                    $user->api_token = sha1(time());
                    $user->device_id = $request->device_id;
                    $user->status = 'Inactive';
                    $user->refered_by = $refered_by;
                    $user->save();
                    $user->attachRole($role_id);
                } else {
                    if ($request->device_id != "" && $request->device_id != null) {
                        User::where("id", $user->id)->update([
                            'device_id' => $request->device_id
                        ]);
                    }
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
                if ($phone =='7878787878') {
                    $sms_status['status'] = 'success';
                } else {
                    $sms_status = $sms->send_sms($phone, $otp);
                }
                if ($sms_status['status'] == 'success') {
                    $data = $user;
                    $msg = 'Successfully Registered.Please Verify your Account by otp.';
                    DB::commit();
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg = 'Error in send otp.';
                    DB::rollback();
                    return Response::Error($data, $sms_status["msg"]);
                }
            } catch (Exception $e) {
                $data = [];
                $msg = 'Registration Failed.';
                DB::rollback();
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = [];
            $msg =  $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }
    public function verify_account(Request $request)
    {
        $token = $request->get('token');
        $otp = $request->get('otp');
        $referal_code = $request->get('referal_code');
        $msg = [
            'token.required' => 'Enter Your Token.',
            'otp.required' => 'Enter Your Otp.',
        ];
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'otp' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $user_otp = UserOtpModel::where('token', $token)->value('otp');
                if ($otp == $user_otp) {
                    $sms = new datavalue();
                    $refer_by_id = User::where('referal_code', $referal_code)->value('id');
                    if ($refer_by_id != 1 && $refer_by_id != '') {
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
                    User::where('api_token', $token)->update([
                        'status' => 'Active'
                    ]);
                    UserOtpModel::where('token', $token)->update([
                        'otp' => mt_rand(100000, 999999),
                        // 'otp' => 123456,
                    ]);
                    $data = [];
                    $user_id = User::where('api_token', $token)->value("id");
                    $role_id = RoleUser::where('user_id', $user_id)->value('role_id');
                    $role = Role::where('id', $role_id)->value('name');
                    if ($role == 'user') {
                        Auth::loginUsingId($user_id);
                    } else {
                        $msg = 'Phone no. not exist as user.';
                        return Response::Error($data, $msg);
                    }
                    $data = User::where('api_token', $token)->first();
                    $msg = 'Your have successfully verify and loggedIn.';
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg = 'Otp Not Matched.';
                    return Response::Error($data, $msg);
                }
            } catch (Exception $e) {
                $data = [];
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = [];
            $msg =  $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }

    public function verify($id)
    {
        $uid = Crypt::decrypt($id);

        User::where('id', $uid)->update([
            'status' => 'Active'
        ]);

        $msg = 'Your registration is completed now.';
        return view('admin.mail.index', compact('msg'));
    }

    public function signin(Request $request)
    {
        $msg = [
            'email.required' => 'Email or Phone No is required.',
            'password.required' => 'Password is required.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $email = $request->email;
            $password = $request->password;
            $uid = User::where('email', $email)->orWhere('phone', $email)->value('id');
            if (!empty($uid)) {
                $role_id = RoleUser::where('user_id', $uid)->value('role_id');
                $role = Role::where('id', $role_id)->value('name');
                if ($role == 'delivery') {
                    try {
                        $pass = User::where('id', $uid)->value('password');
                        if (Hash::check($password, $pass)) {
                            if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'Active'])) {
                                if ($request->get('device_id') != '') {
                                    User::where('id', Auth::user()->id)->update([
                                        'device_id' => $request->get('device_id')
                                    ]);
                                }
                                $check_login_history = UserLoginHistoryModel::where('user_id', Auth::user()->id)->count();
                                if ($check_login_history == 0) {
                                    UserLoginHistoryModel::create([
                                        'user_id' => Auth::user()->id,
                                        'login_time' => NOW(),
                                    ]);
                                } else {
                                    UserLoginHistoryModel::where('user_id', Auth::user()->id)->update([
                                        'login_time' => NOW(),
                                    ]);
                                }
                                $data = Auth::loginUsingId(Auth::user()->id);
                                $msg = 'Successfully Logged In.';
                                return Response::Success($data, $msg);
                            } else {
                                $data = [];
                                $msg = 'Invalid Credentials.';
                                return Response::Error($data, $msg);
                            }
                        } else {
                            $data = [];
                            $msg = 'Password Not Matched.';
                            return Response::Error($data, $msg);
                        }
                    } catch (Exception $e) {
                        $data = [];
                        $msg =  'Login Failed.';
                        return Response::Error($data, $msg);
                    }
                }
            } else {
                $data = [];
                $msg = 'Invalid Credentials.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Login Failed.';
            return Response::Error($data, $msg);
        }
    }

    public function forget_password(Request $request)
    {
        $msg = [
            'email.required' => 'Enter Your Email.',
        ];
        $this->validate($request, [
            'email' => 'required|email',
        ], $msg);

        $email = $request->get('email');
        try {
            $check_email = User::where('email', $email)->count();
            if ($check_email == 1) {
                $otp = mt_rand(100000, 999999);
                $user = User::where('email', $email)->first();
                $name = $user->user_name;
                $api_token = User::where('email', $email)->value('api_token');
                if ($api_token != '') {
                    $check_otp = UserOtpModel::where('token', $api_token)->count();
                    if ($check_otp == 0) {
                        UserOtpModel::create([
                            'token' => $api_token,
                            'otp' => $otp,
                        ]);
                    } else {
                        UserOtpModel::where('token', $api_token)->update([
                            'token' => $api_token,
                            'otp' => $otp,
                        ]);
                    }
                    try {
                        Mail::to($email)->send(new ForgetPassword($name, $otp));
                        $data = ['token' => $api_token];
                        $msg = ' Success';
                        return Response::Success($data, $msg);
                    } catch (Exception $e) {
                        $data = [];
                        $msg =  'Failed.';
                        return Response::Error($data, $msg);
                    }
                } else {
                    $data = [];
                    $msg =  ' Token Not Found.';
                    return Response::Error($data, $msg);
                }
            } else {
                $data = [];
                $msg =  ' Email Not valid.';
                return Response::Error($data, $msg);
            }
        } catch (Exception $e) {

            $data = [];
            $msg =  'Failed.';
            return Response::Error($data, $msg);
        }
    }

    public function check_otp(Request $request, $token)
    {
        $otp = $request->get('otp');
        try {
            if ($otp != '') {
                $user_otp = UserOtpModel::where('token', $token)->value('otp');
                if ($user_otp == $otp) {

                    UserOtpModel::where('token', $token)->update([
                        'otp' =>  mt_rand(100000, 999999),
                    ]);

                    $data = ['token' => $token];
                    $msg =  'Otp Matched';
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg =  'Otp Not Matched.';
                    return Response::Error($data, $msg);
                }
            }
        } catch (Exception $e) {
            $data = [];
            $msg =  'Failed.';
            return Response::Error($data, $msg);
        }
    }

    public function reset_password(Request $request)
    {

        $msg = [
            'n_password.required' => 'Enter Your New Password.',
            'c_password.required' => 'Enter Your Confirm Password.',
        ];
        $this->validate($request, [
            'n_password' => 'required',
            'c_password' => 'required',
        ], $msg);
        try {
            $n_password = $request->get('n_password');
            $c_password = $request->get('c_password');
            $token = $request->get('token');
            if ($n_password == $c_password) {
                User::where('api_token', $token)->update([
                    'password' => bcrypt($n_password),
                ]);
                UserOtpModel::where('token', $token)->update([
                    'otp' =>  mt_rand(100000, 999999),
                ]);
                $data = ['token' => $token];
                $msg = 'Password Updated Successfully';
                return Response::Success($data, $msg);
            } else {
                $data = [];
                $msg = 'Failed.';
                return Response::Error($data, $msg);
            }
        } catch (Exception $e) {

            $data = [];
            $msg = 'Failed.';
            return Response::Error($data, $msg);
        }
    }

    public function supplierSignup(Request $request)
    {
        $msg = [
            'user_name.required' => 'User name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already exists.',
            'phone.required' => 'Phone no is required.',
            'phone.unique' => 'Phone no is already exists.',
            'location.required' => 'Enter Your Location.',
            'latitude.required' => 'Enter Your Latitude.',
            'longitude.required' => 'Enter Your Longitude.',
            'password.required' => 'Password is required.',
            'confirm_password.required' => 'Confirm Password is required.',
        ];
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required',
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|same:password',
        ], $msg);
        if ($validator->passes()) {
            try {
                $role_id = 3;
                $sms = new datavalue();
                $referal_code = $sms->getUniqueCode('REF');
                $user_name = $request->user_name;
                $email = $request->email;
                $phone = $request->phone;
                $location = $request->location;
                $latitude = $request->latitude;
                $longitude = $request->longitude;
                $password = $request->password;
                $user = new User();
                $user->referal_code = $referal_code;
                $user->user_name = $user_name;
                $user->email = $email;
                $user->phone = $phone;
                $user->location = $location;
                $user->latitude = $latitude;
                $user->longitude = $longitude;
                $user->password = bcrypt($password);
                $user->api_token = sha1(time());
                $user->status = 'Inactive';
                $user->save();
                $user->attachRole($role_id);
                ShopDetailsModel::create([
                    'user_id' => $user->id
                ]);
                BankDetailsModel::create([
                    'user_id' => $user->id
                ]);
                $data = [];
                $msg = 'You are successfully registered.Please wait for admin approval.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Registration Failed.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
            // return response()->json(['error' => $validator->errors()]);
        }
    }

    public function supplierSignin(Request $request)
    {
        $msg = [
            'email.required' => 'Email or Phone No is required.',
            'password.required' => 'Password is required.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $email = $request->email;
            $password = $request->password;
            $uid = User::where('email', $email)->orWhere('phone', $email)->value('id');
            $user_status = User::where('email', $email)->orWhere('phone', $email)->value('status');
            $role_id = RoleUser::where('user_id', $uid)->value('role_id');
            $role = Role::where('id', $role_id)->value('name');
            if ($role == 'supplier') {
                try {
                    $pass = User::where('id', $uid)->value('password');
                    if (Hash::check($password, $pass)) {
                        if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'Active'])) {
                            if ($request->get('device_id') != '') {
                                User::where('id', Auth::user()->id)->update([
                                    'device_id' => $request->get('device_id')
                                ]);
                            }
                            $check_login_history = UserLoginHistoryModel::where('user_id', Auth::user()->id)->count();
                            if ($check_login_history == 0) {
                                UserLoginHistoryModel::create([
                                    'user_id' => Auth::user()->id,
                                    'login_time' => NOW(),
                                ]);
                            } else {
                                UserLoginHistoryModel::where('user_id', Auth::user()->id)->update([
                                    'login_time' => NOW(),
                                ]);
                            }
                            $data = Auth::loginUsingId(Auth::user()->id);
                            $msg = 'Successfully Logged In.';
                            return Response::Success($data, $msg);
                        } else {
                            $data = [];
                            $msg = 'Your Account was not approved by admin.';
                            return Response::Error($data, $msg);
                        }
                    } else {
                        $data = [];
                        $msg = 'Password Not Matched.';
                        return Response::Error($data, $msg);
                    }
                } catch (Exception $e) {
                    $data = [];
                    $msg =  'Login Failed.';
                    return Response::Error($data, $msg);
                }
            } else {
                $data = [];
                $msg =  'Invalid Credentials.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Login Failed.';
            return Response::Error($data, $msg);
            // return response()->json(['error' => $validator->errors()]);
        }
    }
    // Delivery Login
    public function deliverySignin(Request $request)
    {
        $msg = [
            'email.required' => 'Email or Phone No is required.',
            'password.required' => 'Password is required.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ], $msg);
     
        if ($validator->passes()) {
            $email = $request->email;
            $password = $request->password;
            $uid = User::where('email', $email)->orWhere('phone', $email)->value('id');
           
           $role_id = RoleUser::where('user_id', $uid)->value('role_id');
            $role = Role::where('id', $role_id)->value('name');

            if ($role == 'delivery') {
                try {
                    $pass = User::where('id', $uid)->value('password');
                 
                    if (Hash::check($password, $pass)) {
                        if (Auth::attempt(['email' => $email, 'password' => $password, 'status' => 'Active'])) {
                            if ($request->get('device_id') != '') {
                                User::where('id', Auth::user()->id)->update([
                                    'device_id' => $request->get('device_id')
                                ]);
                            }
                            $check_login_history = UserLoginHistoryModel::where('user_id', Auth::user()->id)->count();
                            if ($check_login_history == 0) {
                                UserLoginHistoryModel::create([
                                    'user_id' => Auth::user()->id,
                                    'login_time' => NOW(),
                                ]);
                            } else {
                                UserLoginHistoryModel::where('user_id', Auth::user()->id)->update([
                                    'login_time' => NOW(),
                                ]);
                            }
                            $data = Auth::loginUsingId(Auth::user()->id);
                            $msg = 'Successfully Logged In.';
                            return Response::Success($data, $msg);
                        } else {
                            $data = [];
                            $msg = 'Invalid Credentials.';
                            return Response::Error($data, $msg);
                        }
                    } else {
                        $data = [];
                        $msg = 'Password Not Matched.';
                        return Response::Error($data, $msg);
                    }
                } catch (Exception $e) {
                    $data = [];
                    $msg =  'Login Failed.';
                    return Response::Error($data, $msg);
                }
            } else {
                $data = [];
                $msg =  'Invalid Credentials.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Login Failed.';
            return Response::Error($data, $msg);
        }
    }



    public function deliveryRegister(Request $request){
        $msg = [
            'name.required' => 'Delivery name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email is already exists.',
            'phone.required' => 'Phone no is required.',
            'phone.unique' => 'Phone no is already exists.',
            'address.required' => 'Enter Your Address.',
            'latitude.required' => 'Enter Your Latitude.',
            'longitude.required' => 'Enter Your Longitude.',
            'vehicle_details.required' => 'Enter Your Vichel Details.',
            'adhaar_card.required' => 'Choose your aadhaar card.',
            'pan_card.required' => 'Choose your pan card.',
            'driving_lisence.required' => 'Choose your driving license.',
            'blue_book.required' => 'Enter Your blue book.',
            'vehicle_type.required' => 'Select Your Vehicle Type.',
            'password.required' => 'Password is required.',
            'cnf_password.required' => 'Confirm Password is required.',
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'vehicle_details' => 'required',
            'adhaar_card' => 'required',
            'pan_card' => 'required',
            'driving_lisence' => 'required',
            'blue_book' => 'required',
            'vehicle_type' => 'required',
            'password' => 'required|string|min:6',
            'cnf_password' => 'required|same:password',
        ], $msg);
        if ($validator->passes()) {
            try {
                $role_id = 4;
                $user_name = $request->name;
                $email = $request->email;
                $phone = $request->phone;
                $location = $request->location;
                $latitude = $request->latitude;
                $longitude = $request->longitude;
                $password = $request->password;
                $address = $request->address;
                $sms=new datavalue();
                $referal_code=$sms->getUniqueCode('REF');
                $adhaar_card=$request->get('adhaar_card');
                $pan_card=$request->get('pan_card');
                $driving_lisence=$request->get('driving_lisence');
                $blue_book=$request->get('blue_book');
                $vehicle_details=$request->get('vehicle_details');
                $vehicle_type=$request->get('vehicle_type');

                $image_upload = new datavalue();
                if($adhaar_card!='') {
                    $image_name = $image_upload->upload_pic($adhaar_card, 'document');
                    if($image_name['status']=='success'){
                        $adhaarImageName=$image_name['file_name'];
                        $adhaarImageName=url("/images/document/$adhaarImageName");
                    }else{
                        $data = [];
                        $msg = 'Image Not Upload Failed.';
                        return Response::Error($data, $msg);
                    }
                }

                if($pan_card!='') {
                    $image_name = $image_upload->upload_pic($pan_card, 'document');
                    if($image_name['status']=='success'){
                        $panImageName=$image_name['file_name'];
                        $panImageName=url("/images/document/$panImageName");
                    }else{
                        $data = [];
                        $msg = 'Image Not Upload Failed.';
                        return Response::Error($data, $msg);
                    }
                }

                if($driving_lisence!='') {
                    $image_name = $image_upload->upload_pic($driving_lisence, 'document');
                    if($image_name['status']=='success'){
                        $licenseImageName=$image_name['file_name'];
                        $licenseImageName=url("/images/document/$licenseImageName");
                    }else{
                        $data = [];
                        $msg = 'Image Not Upload Failed.';
                        return Response::Error($data, $msg);
                    }
                }

                if($blue_book!='') {
                    $image_name = $image_upload->upload_pic($blue_book, 'document');
                    if($image_name['status']=='success'){
                        $bluebookImageName=$image_name['file_name'];
                        $bluebookImageName=url("/images/document/$bluebookImageName");
                    }else{
                        $data = [];
                        $msg = 'Image Not Upload Failed.';
                        return Response::Error($data, $msg);
                    }
                }
                $user = new User();
                $user->user_name = $user_name;
                $user->referal_code = $referal_code;
                $user->email = $email;
                $user->phone = $phone;
                $user->location = $address;
                $user->latitude = $latitude;
                $user->longitude = $longitude;
                $user->password = bcrypt($password);
                $user->api_token = sha1(time());
                $user->status = 'Inactive';
                $user->save();
                $user->attachRole($role_id);
                DeliveryProfileModel::create([
                    'user_id'=>$user->id,
                    'address'=>$address,
                    'vehicle_details'=>$vehicle_details,
                    'adhaar_card'=>$adhaarImageName,
                    'pan_card'=>$panImageName,
                    'driving_lisence'=>$licenseImageName,
                    'blue_book'=>$bluebookImageName,
                    'vehicle_type'=>$vehicle_type,
                ]);
                $data = [];
                $msg = 'You are successfully registered.Please wait for admin approval.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Registration Failed.';
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
            // return response()->json(['error' => $validator->errors()]);
        }
    }
}
