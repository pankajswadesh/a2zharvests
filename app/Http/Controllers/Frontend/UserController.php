<?php

namespace App\Http\Controllers\Frontend;

use App\Model\OrderModel;
use App\Model\ShippingModel;
use App\Model\TransactionDetailsModel;
use App\repo\Response;
use App\User;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Model\PaytmUser;

class UserController extends Controller
{
    public function my_account()
    {
        $user_details = User::where('id', \Auth::user()->id)->first();
        date_default_timezone_set("Asia/Kolkata");
        $current_date = date('Y-m-d');
        $created_at = date('Y-m-d', strtotime($current_date . '-3 month'));
        $orders = OrderModel::whereDate('created_at', '>=', $created_at)
            ->where('user_id', \Auth::user()->id)
            ->whereNotIn('status', ['Cancel', 'Refunded'])
            ->orderBy('id', 'desc')
            ->get();
        $data = [];
        foreach ($orders as $order) {
            $item_details = DB::table('order_details')->join('users', 'users.id', '=', 'order_details.delivery_id')
                ->where('order_details.order_id', $order->id)
                ->select('order_details.*', 'users.user_name as delivery_name', 'users.email as delivery_email', 'users.phone as delivery_phone', 'users.location as delivery_location')
                ->get();
            array_push($data, [
                'order_details' => $order,
                'item_details' => $item_details,
                'shipping_details' => $order->shipping,
                'payment_details' => $order->payment,
            ]);
        }
        $shipping_details = ShippingModel::where('user_id', \Auth::user()->id)->first();
        return view('frontend.pages.user.my_account', compact('user_details', 'data', 'shipping_details'));
    }
    public function update_profile(Request $request)
    {
        $msg = [
            'user_name.required' => 'User Name is required.',
            'phone.required' => 'Phone No. is required.',
            'email.unique' => 'Email already exist.'
        ];
        $validator = Validator::make($request->all(), [
            'user_name' => "required",
            'phone' => "required",
            'email' => "required|unique:users,id," . \Auth::user()->id,
        ], $msg);
        if ($validator->passes()) {
            $data = $request->except('_token');
            User::where('id', \Auth::user()->id)->update($data);
            return array('status' => 'success', 'msg' => 'Profile data updated successfully.');
        } else {
            $msg = $validator->errors()->first();
            return array('status' => 'error', 'msg' => $msg);
        }
    }
    public function update_shipping(Request $request)
    {
        $msg = [
            'name.required' => 'Name is required.',
            'email.unique' => 'Email already exist.',
            'phone_no.required' => 'Phone No. is required.',
            'address.required' => 'Phone No. is required.',
            'pincode.required' => 'Phone No. is required.',
            'landmark.required' => 'Phone No. is required.'
        ];
        $validator = Validator::make($request->all(), [
            'name' => "required",
            'phone_no' => "required",
            'email' => "required",
            'address' => "required",
            'pincode' => "required",
            'landmark' => "required"
        ], $msg);
        if ($validator->passes()) {
            $data = $request->except('_token');
            $check = ShippingModel::where('user_id', \Auth::user()->id)->count();
            if ($check == 0) {
                $data["user_id"] = \Auth::user()->id;
                ShippingModel::create($data);
            } else {
                ShippingModel::where('user_id', \Auth::user()->id)->update($data);
            }
            return array('status' => 'success', 'msg' => 'Shipping data updated successfully.');
        } else {
            $msg = $validator->errors()->first();
            return array('status' => 'error', 'msg' => $msg);
        }
    }
    public function update_password(Request $request)
    {
        $msg = [
            'old_password.required' => 'Enter your old password.',
            'new_password.required' => 'Enter your new password.',
            'cnf_password.required' => 'Confirm your new password.',
        ];
        $validator = Validator::make($request->all(), [
            'old_password' => "required",
            'new_password' => "required",
            'cnf_password' => "required|same:new_password"
        ], $msg);
        if ($validator->passes()) {
            $pass = User::where('id', \Auth::user()->id)->value('password');
            if (Hash::check($request->old_password, $pass)) {
                User::where('id', \Auth::user()->id)->update([
                    'password' => bcrypt($request->new_password)
                ]);
                return array('status' => 'success', 'msg' => 'Password updated successfully.');
            } else {
                return array('status' => 'error', 'msg' => 'Old password is incorrect.');
            }
        } else {
            $msg = $validator->errors()->first();
            return array('status' => 'error', 'msg' => $msg);
        }
    }
    public function pay_to_wallet(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required'
        ]);
        try {
            $user_id = Auth::user()->id;
            $paytm_order_id=time() . $user_id;
            /* Paytm temp entry start */
            $PaytmUser = new PaytmUser();
            $PaytmUser->user_id = $user_id;
            $PaytmUser->paytm_order_id = $paytm_order_id;
            $PaytmUser->save();
            /* Paytm temp entry End */

            $amount = $request->amount;
            $userData = User::where('id', $user_id)->first();
            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' =>$paytm_order_id,
                'user' => $user_id,
                'mobile_number' => $userData['phone']??'1234567890',
                'email' => $userData['email']??'info@a2zharvests.com',
                'amount' => $amount,
                'callback_url' => route('add_amount')
            ]);
            $response =  $payment->receive();
            return $response;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('error_status', $msg);
        }
    }
    public function add_amount(Request $request)
    {
        DB::beginTransaction();
        try {
            $transaction = PaytmWallet::with('receive');
            $response = $transaction->response();
            $msg = 'There is an issue on online payment.';
            if ($transaction->isSuccessful()) {
                $transaction_id = $response['TXNID'];
                $actual_amount = $response['TXNAMOUNT'];
                $paytm_order_id =$transaction->getOrderId();
                $user_id=PaytmUser::where('paytm_order_id',$paytm_order_id)->value('user_id');
                if($user_id){
                    TransactionDetailsModel::create([
                        'user_id' => $user_id,
                        'transaction_id' => $transaction_id,
                        'amount' => $actual_amount,
                        'transaction_type' => 'Online',
                        'status' => 'Success',
                    ]);
                    $user = User::find($user_id);
                    $user->increment('wallet_amount', $actual_amount);
                    DB::commit();
                    $msg = "Rs " . $actual_amount . ' Added To Your Wallet.';
                    return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('success_status', $msg);
                }else{
                    DB::rollback();
                    $msg = 'Oh! Your payment failed! Please try again!';
                    return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('error_status', $msg);
                }
            } else if ($transaction->isFailed()) {
                DB::rollback();
                $msg = 'Oh! Your payment failed!';
                return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('error_status', $msg);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            DB::rollback();
            return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('error_status', $msg);
        }
    }
}
