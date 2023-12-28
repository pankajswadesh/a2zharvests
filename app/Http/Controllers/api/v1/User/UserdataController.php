<?php

namespace App\Http\Controllers\api\v1\User;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Model\BankDetailsModel;
use App\Model\CartModel;
use App\Model\DeliverySlotsModel;
use App\Model\OrderModel;
use App\Model\SettingModel;
use App\Model\ShippingModel;
use App\Model\ShopDetailsModel;
use App\Model\TransactionDetailsModel;
use App\Model\UserLoginHistoryModel;
use App\repo\datavalue;
use App\repo\Response;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Razorpay\Api\Api;

class UserdataController extends Controller
{

    public function get_user_data(Request $request)
    {
        try {
            $data = Auth::user();
            $msg =  '';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg =  'You are not logged in.';
            return Response::Error($data, $msg);
        }
    }
    public function update_location(Request $request)
    {
        $user_id = Auth::user()->id;
        $msg = [
            'latitude.required' => 'Please select address from suggestion or current location.',
            'longitude.required' => 'Please select address from suggestion or current location.',
            'location.required' => 'Address is required.',
        ];
        $validator = Validator::make($request->all(), [
            'latitude' => "required",
            'longitude' => "required",
            'location' => "required",
        ], $msg);
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        $location = $request->get('location');
        if ($validator->passes()) {
            try {
                User::where('id', $user_id)->update([
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $location
                ]);
                CartModel::where('user_id', Auth::user()->id)->delete();
                $data = User::find($user_id);
                $msg = 'Location Updated Successfully & previous cart is empty.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }

    public function update_profile(Request $request)
    {
        $user_id = Auth::user()->id;
        $msg = [
            'user_name.required' => 'User Name is required.',
            'email.required' => 'Email is required.',
            'email.unique' => 'Email already exist.',
            'location.required' => 'Enter your address.',
        ];
        $validator = Validator::make($request->all(), [
            'user_name' => "required",
            'email' => "required|unique:users,email," . $user_id,
            'location' => "required",
        ], $msg);
        $user_name = $request->get('user_name');
        $email = $request->get('email');
        $image = $request->get('image');
        $location = $request->get('location');
        $profile_image = Auth::user()->image_url;
        $prevLocation = Auth::user()->location;
        $latitude = $request->get('latitude');
        $longitude = $request->get('longitude');
        if (($location != $prevLocation) && $latitude == "") {
            $latLng = datavalue::get_lat_long($location);
            if ($latLng['lat'] == '') {
                $data = [];
                $msg = 'Address is not valid try again.';
                return Response::Error($data, $msg);
            } else {
                $latitude =   $latLng['lat'];
                $longitude =   $latLng['lng'];
            }
        }
        if ($validator->passes()) {
            try {
                if ($image != '') {
                    $image_upload = new datavalue();
                    $image_name = $image_upload->upload_pic($image, 'profile');
                    if ($image_name['status'] == 'success') {
                        $file = public_path() . '/images/profile/' . $profile_image;
                        if ($profile_image != 'avatar.png' && file_exists($file)) {
                            unlink($file);
                        }
                        $imageName = $image_name['file_name'];
                    } else {
                        $data = [];
                        $msg = 'Image Not Upload Failed.';
                        return Response::Error($data, $msg);
                    }
                } else {
                    $imageName = $profile_image;
                }
                User::where('id', $user_id)->update([
                    'user_name' => $user_name,
                    'email' => $email,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'location' => $location,
                    'image_url' => $imageName,
                ]);
                $msg = 'Profile  Updated Successfully';
                if ($prevLocation != $location) {
                    CartModel::where('user_id', Auth::user()->id)->delete();
                    $msg .= ' & previous cart is empty';
                }
                $data = User::find($user_id);
                $msg .= '.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Profile Not Updated.';
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }

    public function update_password(Request $request)
    {
        $msg = [
            'old_pass.required' => 'Enter Your Old Password',
            'new_pass.required' => 'Enter Your New Password',
            'confirm_pass.required' => 'Enter Your Confirm Pasword',
        ];
        $validator = Validator::make($request->all(), [
            'old_pass' => 'required',
            'new_pass' => 'required',
            'confirm_pass' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $user_id = Auth::user()->id;
                $old_pass = $request->old_pass;
                $new_pass = $request->new_pass;
                $confirm_pass = $request->confirm_pass;
                $pass = Auth::user()->password;
                if (Hash::check($old_pass, $pass)) {
                    if ($new_pass == $confirm_pass) {
                        $password = Hash::make($new_pass);
                        User::where('id', $user_id)->update([
                            'password' => $password,
                        ]);
                        $data = [];
                        $msg =  'Password Updated Sucessfully.';
                        return Response::Success($data, $msg);
                    } else {
                        $data = [];
                        $msg =  'New Password and Confirm Password are Not Matched.';
                        return Response::Error($data, $msg);
                    }
                } else {
                    $data = [];
                    $msg =  'Old Password Not Matched.';
                    return Response::Error($data, $msg);
                }
            } catch (Exception $e) {
                $data = [];
                $msg =  'Password Not Updated.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors()->first();
            $msg = 'Password Not Updated.';
            return Response::Error($data, $msg);
        }
    }

    public function get_supplier_shop_details()
    {
        try {
            $user_id = Auth::user()->id;
            $shop_details = ShopDetailsModel::where('user_id', $user_id)->first();
            $data = array(
                'business_name' => $shop_details->business_name,
                'address' => $shop_details->user->location,
                'latitude' => $shop_details->user->latitude,
                'longitude' => $shop_details->user->longitude,
                'email' => $shop_details->user->email,
                'phone' => $shop_details->user->phone,
                'business_id' => $shop_details->business_id,
                'gst_no' => $shop_details->gst_no,
                'fsssi_no' => $shop_details->fsssi_no,
                'start_time' => $shop_details->start_time,
                'end_time' => $shop_details->end_time,
                'alt_phone_no' => $shop_details->alt_phone_no,
            );
            $msg =  '';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg =  'You are not logged in.';
            return Response::Error($data, $msg);
        }
    }

    public function get_supplier_bank_details()
    {
        try {
            $user_id = Auth::user()->id;
            $data = BankDetailsModel::where('user_id', $user_id)->first();
            $msg =  '';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg =  'You are not logged in.';
            return Response::Error($data, $msg);
        }
    }

    public function update_supplier_shop_details(Request $request)
    {
        $user_id = Auth::user()->id;
        $msg = [
            'email.required' => 'Email is required.',
            'phone.required' => 'Phone No is required.',
            'location.required' => 'Enter Your Location.',
            'latitude.required' => 'Latitude is required.',
            'longitude.required' => 'Longitude is required.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => "required|unique:users,email,$user_id,id",
            'phone' => "required|unique:users,phone,$user_id,id",
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required'
        ], $msg);
        if ($validator->passes()) {
            try {
                User::where('id', $user_id)->update([
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'location' => $request->location,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
                ShopDetailsModel::where('user_id', $user_id)->update([
                    'business_name' => $request->business_name,
                    'business_id' => $request->business_id,
                    'gst_no' => $request->gst_no,
                    'fsssi_no' => $request->fsssi_no,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'alt_phone_no' => $request->alt_phone_no,
                ]);
                $data = [];
                $msg = 'Supplier Shop Details Updated Successfully.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Supplier Shop Details Not Updated .';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error.';
            return Response::Error($data, $msg);
            // return response()->json(['error' => $validator->errors()]);
        }
    }

    public function update_supplier_bank_details(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            BankDetailsModel::where('user_id', $user_id)->update([
                'holder_name' => $request->holder_name,
                'account_no' => $request->account_no,
                'branch_name' => $request->branch_name,
                'ifsc_code' => $request->ifsc_code,
            ]);
            $data = [];
            $msg = 'Supplier Bank Details Updated Successfully.';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg = 'Supplier Bank Details Not Updated .';
            return Response::Error($data, $msg);
        }
    }

    public function user_logout(Request $request)
    {
        try {
            $check_login_history = UserLoginHistoryModel::where('user_id', Auth::user()->id)->count();
            if ($check_login_history == 0) {
                UserLoginHistoryModel::create([
                    'user_id' => Auth::user()->id,
                    'logout_time' => NOW(),
                ]);
            } else {
                UserLoginHistoryModel::where('user_id', Auth::user()->id)->update([
                    'logout_time' => NOW(),
                ]);
            }

            $data = [];
            $msg = 'Logout Successfully.';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg = 'Not Logout .';
            return Response::Error($data, $msg);
        }
    }

    public function add_money_to_wallet(Request $request)
    {
        $user_id = Auth::user()->id;
        $msg = [
            'paytm_order_id.required' => 'Order Id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'paytm_order_id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {
                $paytm_order_id = $request->get('paytm_order_id');
                $status = PaytmWallet::with('status');
                $status->prepare(['order' => $paytm_order_id]);
                $status->check();

                if ($status->isSuccessful()) {
                    $transaction_id = $status->getTransactionId();
                    $actual_amount = $status['TXNAMOUNT'];
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
                    $data = [];
                    $msg = "Rs " . $actual_amount . ' Added To Your Wallet.';
                    return Response::Success($data, $msg);
                }
            } catch (Exception $e) {
                $data = [];
                $msg = 'Money Not captured .';
                DB::rollback();
                return Response::Error($data, $msg);
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }
    public function getNext7Days()
    {
        $today = Carbon::today();
        $data["data"][0]["date"] = $today->format('d M D');
        $data["data"][0]["date_save"] = $today->format('d M Y');
        for ($i = 1; $i < 7; $i++) {
            $now = $today->addDay();
            $data["data"][$i]["date"] = $now->format('d M D');
            $data["data"][$i]["date_save"] = $today->format('d M Y');
        }
        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
        if (!empty($shipping_details)) {
            $latitude = Auth::user()->latitude;
            $longitude = Auth::user()->longitude;
        } else {
            $latitude = $shipping_details->latitude;
            $longitude = $shipping_details->longitude;
        }
        $data["showSlot"] = datavalue::checkAvailability($latitude, $longitude);
        return Response::Success($data, '');
    }
    public function checkSlot(Request $request)
    {
        $user_delivery_date = $request->get('deliveryDate');
        $user_delivery_time = $request->get('deliverySlot');
        $availability = SettingModel::where('key', 'Booking Available Per Slot')->value('value');
        $bookedCount = OrderModel::where("user_delivery_date", $user_delivery_date)->where("user_delivery_time", $user_delivery_time)->whereYear('datetime', '=', date('Y'))->count();
        $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
        $user_ids = datavalue::getNearbySupplier($shipping_details->latitude, $shipping_details->longitude);
        $cart_suppliers = CartModel::where("user_id", Auth::user()->id)->pluck("supplier_id")->toArray();
        $count = count(array_diff($cart_suppliers, $user_ids));
        if ($count > 0) {
            $data = [];
            $msg = 'Cart products is not available for this pin code.';
            return Response::Error($data, $msg);
        }
        $status = DeliverySlotsModel::where("slot_name", $user_delivery_time)->value("status");
        if ((($availability <= $bookedCount) && $user_delivery_date != "") || $status == "Inactive") {
            return Response::Error([], 'Selected Slot Not Available.');
        } else {
            return Response::Success([], 'Selected Slot Available.');
        }
    }
}
