<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\api\v1\Notification\NotificationController;
use App\Model\CartModel;
use App\Mail\OrderMail;
use App\Model\DeliverySettingModel;
use App\Model\DiscountModel;
use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\PaymentModel;
use App\Model\SettingModel;
use App\Model\ShippingModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\TaxValueModel;
use App\repo\datavalue;
use App\repo\Response;
use App\User;
use DB;
use Exception;
use Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Model\PaytmUser;
use App\repo\Paytm;

class OrderController extends Controller
{
    public function place_order(Request $request)
    {
        $msg = [
            'payment_method.required' => 'Select Payment Method',
        ];
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $user_delivery_date = $request->get('deliveryDate');
            $user_delivery_time = $request->get('deliverySlot');
            $availability = SettingModel::where('key', 'Booking Available Per Slot')->value('value');
            $bookedCount = OrderModel::where("user_delivery_date", $user_delivery_date)->where("user_delivery_time", $user_delivery_time)->whereYear('datetime', '=', date('Y'))->count();
            if (($availability <= $bookedCount) && $user_delivery_date != "") {
                $msg = 'Selected Slot Not Available...';
                return array('status' => 'errror', 'msg' => $msg);
            }
            $cart_count = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->count();
            if ($cart_count > 0) {
                try {
                    $s = new datavalue();
                    $unique_id = $s->getUniqueCode('ORDER');
                    $transaction_id = $s->getUniqueCode('TXN');
                    $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');
                    if ($request->get('payment_method') == 'cod') {
                        $shipping_id = ShippingModel::where('user_id', Auth::user()->id)->latest()->value('id');
                        $payment = PaymentModel::create([
                            'payment_method' => 'cod',
                            'payment_status' => 'Pending',
                            'payment_date_time' => NOW(),
                        ]);
                        $order = OrderModel::create([
                            'order_id' => $unique_id,
                            'user_id' => Auth::user()->id,
                            'delivery_id' => $delivery_id,
                            'payment_id' => $payment->id,
                            'shipping_id' => $shipping_id,
                            'transaction_id' => $transaction_id,
                            'datetime' => NOW(),
                            'user_delivery_date' => $request->get('deliveryDate'),
                            'user_delivery_time' => $request->get('deliverySlot'),
                            'status' => 'Processing',
                        ]);
                        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->get();
                        $delivery_charge_details = DeliverySettingModel::first();
                        $total_amount = 0;
                        $gross_amount = 0;
                        $total_discount = 0;
                        $total_tax = 0;
                        $notify_supplier_ids = [];
                        foreach ($cart_details as $c_details) {
                            $supplier_product = SupplierProductModel::where('product_id', $c_details->product_id)->where('user_id', $c_details->supplier_id)->where('status', 'Active')->first();
                            array_push($notify_supplier_ids, $c_details->supplier_id);
                            $total_amount = $total_amount + ($c_details->quantity * $supplier_product->price);
                            $product_total_price = $c_details->quantity * $supplier_product->price;
                            $discount_details = DiscountModel::find($supplier_product->discount_id);
                            $discount['id'] = $discount_details->id;
                            $discount['discount_name'] = $discount_details->discount_name;
                            $discount['discount_value'] = $supplier_product->discount_value;
                            $tax_details = TaxModel::find($c_details->product->tax_id);
                            $tax1['tax_id'] = $tax_details->id;
                            $tax1['tax_name'] = $tax_details->tax_name;
                            $tax1['tax_total_value'] = $tax_details->tax_value;
                            $tax1['is_inclusive'] = $tax_details->is_inclusive;
                            $tax1['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
                            $product_discount = 0;
                            if ($discount_details['discount_name'] == '%') {
                                $product_discount = (($product_total_price * $supplier_product->discount_value) / 100);
                                $product_discount_price = ceil($product_total_price - $product_discount) * $c_details->quantity;
                            } else if ($discount_details['discount_name'] == 'rs') {
                                $product_discount = $supplier_product->discount_value;
                                $product_discount_price = ceil($product_total_price - $product_discount) * $c_details->quantity;
                            } else {
                                // $product_discount = 0;
                                $product_discount_price = $product_total_price * $c_details->quantity;
                            }
                            $total_discount = $total_discount + $product_discount;
                            if ($tax_details->is_inclusive == 'No') {
                                $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                                $gross_amount = $gross_amount + ceil($product_with_tax_price);
                                $total_tax = $total_tax + ((ceil($product_discount_price) * $tax_details['tax_value']) / 100);
                            } else {
                                $product_with_tax_price = ceil($product_discount_price);
                                $gross_amount = $gross_amount + $product_with_tax_price;
                            }
                            $latitude = User::where('id', $c_details->supplier_id)->value('latitude');
                            $longitude = User::where('id', $c_details->supplier_id)->value('longitude');
                            $del_id = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                                ->where('users.status', '<>', 'Deleted')
                                ->where('role_user.role_id', 4)
                                ->inRandomOrder()
                                ->pluck('id')
                                ->toArray();
                            $distance = SettingModel::where('id', 2)->value('value');
                            $user_ids = User::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
                                ->having('distance', '<', $distance)
                                ->orderBy('distance')
                                ->where('id', '<>', $c_details->supplier_id)
                                ->whereIn('id', $del_id)
                                ->where('status', 'Active')
                                ->pluck('id')
                                ->toArray();
                            if (count($user_ids) > 0) {
                                //  $user_ids=$user_ids;
                                $delivery_id = $user_ids[0];
                            } else {
                                $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');
                            }
                            OrderDetailsModel::create([
                                'order_id' => $order->id,
                                'supplier_id' => $c_details->supplier_id,
                                'delivery_id' => $delivery_id,
                                'payment_method' => 'cod',
                                'product_id' => $c_details->product_id,
                                'product_name' => $c_details->product->product_name,
                                'price' => $supplier_product->price,
                                'gross_price' => $product_with_tax_price,
                                'qty' => $c_details->quantity,
                                'supplier_quantity' => $supplier_product->quantity,
                                'unit' => $c_details->unit,
                                'tax_value' => $tax_details['tax_value'],
                                'tax' => json_encode($tax1),
                                'discount_value' => $product_discount,
                                'discount' => json_encode($discount),
                            ]);
                        }
                        if ($delivery_charge_details["max_amount"] > $gross_amount) {
                            $delivery_charge = $delivery_charge_details["delivery_charge"];
                        } else {
                            $delivery_charge = 0;
                        }
                        $use_wallet = 'No';
                        $wallet_amount = 0;
                        if ($request->get('use_wallet') == "true") {
                            $wallet_amount = User::where('id', Auth::user()->id)->value('wallet_amount');
                            User::where('id', Auth::user()->id)->update([
                                'wallet_amount' => '0'
                            ]);
                            $use_wallet = 'Yes';
                        }
                        OrderModel::where("id", $order->id)->update([
                            'total_amount' => $total_amount,
                            'total_discount' => $total_discount,
                            'total_tax' => $total_tax,
                            'delivery_charge' => $delivery_charge,
                            'gross_amount' => $gross_amount + $delivery_charge,
                            'use_wallet' => $use_wallet,
                            'wallet_amount' => $wallet_amount,
                        ]);
                        CartModel::where('user_id', Auth::user()->id)->delete();
                        $type = 'Order';
                        $notify = new NotificationController();
                        $msg = 'You got a new order.';
                        // $notify->sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                        $data = ['order_id' => $order->id];
                        $msg = 'Order Created Successfully.';
                        return array('status' => 'success', 'msg' => $msg, 'data' => $data);
                    } elseif ($request->get('payment_method') == 'wallet') {
                        $shipping_id = ShippingModel::where('user_id', Auth::user()->id)->latest()->value('id');
                        $payment = PaymentModel::create([
                            'payment_method' => 'wallet',
                            'payment_status' => 'Completed',
                            'payment_date_time' => NOW(),
                        ]);
                        $order = OrderModel::create([
                            'order_id' => $unique_id,
                            'user_id' => Auth::user()->id,
                            'delivery_id' => $delivery_id,
                            'payment_id' => $payment->id,
                            'shipping_id' => $shipping_id,
                            'transaction_id' => $transaction_id,
                            'datetime' => NOW(),
                            'user_delivery_date' => $request->get('deliveryDate'),
                            'user_delivery_time' => $request->get('deliverySlot'),
                            'status' => 'Processing',
                        ]);
                        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->get();
                        $delivery_charge_details = DeliverySettingModel::first();
                        $total_amount = 0;
                        $tax_total = 0;
                        $total_discount = 0;
                        $total_tax = 0;
                        $gross_amount = 0;
                        $notify_supplier_ids = [];
                        foreach ($cart_details as $c_details) {
                            $supplier_product = SupplierProductModel::where('product_id', $c_details->product_id)->where('user_id', $c_details->supplier_id)->where('status', 'Active')->first();
                            array_push($notify_supplier_ids, $c_details->supplier_id);
                            $total_amount = $total_amount + ($c_details->quantity * $supplier_product->price);
                            $product_total_price = $c_details->quantity * $supplier_product->price;
                            $discount_details = DiscountModel::find($supplier_product->discount_id);
                            $discount['id'] = $discount_details->id;
                            $discount['discount_name'] = $discount_details->discount_name;
                            $discount['discount_value'] = $supplier_product->discount_value;
                            $tax_details = TaxModel::find($c_details->product->tax_id);
                            $tax1['tax_id'] = $tax_details->id;
                            $tax1['tax_name'] = $tax_details->tax_name;
                            $tax1['tax_total_value'] = $tax_details->tax_value;
                            $tax1['is_inclusive'] = $tax_details->is_inclusive;
                            $tax1['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
                            if ($discount_details['discount_name'] == '%') {
                                $product_discount = (($product_total_price * $supplier_product->discount_value) / 100);
                                $product_discount_price = ceil($product_total_price - $product_discount) * $c_details->quantity;
                            } else if ($discount_details['discount_name'] == 'rs') {
                                $product_discount = $supplier_product->discount_value;
                                $product_discount_price = ceil($product_total_price - $product_discount) * $c_details->quantity;
                            } else {
                                // $product_discount = 0;
                                $product_discount_price = $product_total_price * $c_details->quantity;
                            }
                            $total_discount = $total_discount + $product_discount;
                            if ($tax_details->is_inclusive == 'No') {
                                $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                                $gross_amount = $gross_amount + ceil($product_with_tax_price);
                                $total_tax = $total_tax + ((ceil($product_discount_price) * $tax_details['tax_value']) / 100);
                            } else {
                                $product_with_tax_price = ceil($product_discount_price);
                                $gross_amount = $gross_amount + $product_with_tax_price;
                            }
                            $latitude = User::where('id', $c_details->supplier_id)->value('latitude');
                            $longitude = User::where('id', $c_details->supplier_id)->value('longitude');
                            $del_id = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                                ->where('users.status', '<>', 'Deleted')
                                ->where('role_user.role_id', 4)
                                ->inRandomOrder()
                                ->pluck('id')
                                ->toArray();
                            $distance = SettingModel::where('id', 2)->value('value');
                            $user_ids = User::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
                                ->having('distance', '<', $distance)
                                ->orderBy('distance')
                                ->where('id', '<>', $c_details->supplier_id)
                                ->whereIn('id', $del_id)
                                ->where('status', 'Active')
                                ->pluck('id')
                                ->toArray();
                            if (count($user_ids) > 0) {
                                //  $user_ids=$user_ids;
                                $delivery_id = $user_ids[0];
                            } else {
                                $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');
                            }
                            OrderDetailsModel::create([
                                'order_id' => $order->id,
                                'supplier_id' => $c_details->supplier_id,
                                'delivery_id' => $delivery_id,
                                'payment_method' => 'wallet',
                                'product_id' => $c_details->product_id,
                                'product_name' => $c_details->product->product_name,
                                'price' => $supplier_product->price,
                                'gross_price' => $product_with_tax_price,
                                'qty' => $c_details->quantity,
                                'supplier_quantity' => $supplier_product->quantity,
                                'unit' => $c_details->unit,
                                'tax_value' => $tax_details['tax_value'],
                                'tax' => json_encode($tax1),
                                'discount_value' => $product_discount,
                                'discount' => json_encode($discount),
                            ]);
                        }
                        $gross_amount = $total_amount + $tax_total;
                        if ($delivery_charge_details["max_amount"] > $gross_amount) {
                            $delivery_charge = $delivery_charge_details["delivery_charge"];
                        } else {
                            $delivery_charge = 0;
                        }
                        $use_wallet = 'No';
                        $wallet_amount = 0;
                        if ($request->get('use_wallet') == "true") {
                            $user = User::find(Auth::user()->id);
                            $user->decrement('wallet_amount', $gross_amount);
                            $use_wallet = 'Yes';
                            $wallet_amount = $gross_amount;
                        }
                        OrderModel::where("id", $order->id)->update([
                            'total_amount' => $total_amount,
                            'delivery_charge' => $delivery_charge,
                            'gross_amount' => $gross_amount + $delivery_charge,
                            'use_wallet' => $use_wallet,
                            'wallet_amount' => $wallet_amount,
                            'total_discount' => $total_discount,
                            'total_tax' => $tax_total,
                        ]);
                        CartModel::where('user_id', Auth::user()->id)->delete();
                        $type = 'Order';
                        $notify = new NotificationController();
                        $msg = 'You got a new order.';
                        // $notify->sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                        $data = ['order_id' => $order->id];
                        $msg = 'Order Created Successfully.';
                        return array('status' => 'success', 'msg' => $msg, 'data' => $data);
                    } else {
                        $data = [];
                        $msg = 'Check Your Payment Method';
                        return array('status' => 'error', 'msg' => $msg, 'data' => $data);
                    }
                } catch (Exception $e) {
                    $data = [];
                    $msg = $e->getMessage();
                    return array('status' => 'error', 'msg' => $msg, 'data' => $data);
                }
            } else {
                $data = [];
                $msg = 'You have no item in your cart.';
                return array('status' => 'errror', 'msg' => $msg, 'data' => $data);
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return array('status' => 'error', 'msg' => $msg, 'data' => $data);
        }
    }
    public function online_place_order(Request $request)
    {
        $s_datavalue = new datavalue();
        $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');

        $transaction = PaytmWallet::with('receive');
        $paytm_response = $transaction->response();

        if ($transaction->isSuccessful()) {
            $paytm_order_id = $transaction->getOrderId();
            $transaction_id = $paytm_response['TXNID'];
            $user_data = PaytmUser::where('paytm_order_id', $paytm_order_id)->first();
            if ($user_data) {
                $user_id=$user_data['user_id'];
                $deliveryDate=$user_data['deliveryDate'];
                $deliverySlot=$user_data['deliverySlot'];
                $unique_id = $s_datavalue->getUniqueCode('ORDER');
                $shipping_id = ShippingModel::where('user_id', $user_id)->latest()->value('id');
                $payment = PaymentModel::create([
                    'payment_method' => 'online',
                    'payment_status' => 'Completed',
                    'payment_date_time' => NOW(),
                ]);
                $order = OrderModel::create([
                    'order_id' => $unique_id,
                    'user_id' => $user_id,
                    'delivery_id' => $delivery_id,
                    'payment_id' => $payment->id,
                    'shipping_id' => $shipping_id,
                    'transaction_id' => $transaction_id,
                    'datetime' => NOW(),
                    'user_delivery_date' => $deliveryDate,
                    'user_delivery_time' => $deliverySlot,
                    'status' => 'Processing',
                ]);
                $cart_details = CartModel::where('user_id', $user_id)->whereHas('active_supplier_product')->whereHas('is_supplier_active')->get();
                $delivery_charge_details = DeliverySettingModel::first();
                $total_amount = 0;
                $tax_total = 0;
                $total_discount = 0;
                $total_tax = 0;
                $gross_amount = 0;
                $notify_supplier_ids = [];
                foreach ($cart_details as $c_details) {
                    $supplier_product = SupplierProductModel::where('product_id', $c_details->product_id)->where('user_id', $c_details->supplier_id)->where('status', 'Active')->first();
                    array_push($notify_supplier_ids, $c_details->supplier_id);
                    $total_amount = $total_amount + ($c_details->quantity * $supplier_product->price);
                    $product_total_price = $c_details->quantity * $supplier_product->price;
                    $discount_details = DiscountModel::find($supplier_product->discount_id);
                    $discount['id'] = $discount_details->id;
                    $discount['discount_name'] = $discount_details->discount_name;
                    $discount['discount_value'] = $supplier_product->discount_value;
                    $tax_details = TaxModel::find($c_details->product->tax_id);
                    $tax1['tax_id'] = $tax_details->id;
                    $tax1['tax_name'] = $tax_details->tax_name;
                    $tax1['tax_total_value'] = $tax_details->tax_value;
                    $tax1['is_inclusive'] = $tax_details->is_inclusive;
                    $tax1['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
                    $product_discount = 0;
                    if ($discount_details['discount_name'] == '%') {
                        $product_discount = (($product_total_price * $supplier_product->discount_value) / 100);
                        $product_discount_price = $product_total_price - $product_discount;
                    } else if ($discount_details['discount_name'] == 'rs') {
                        $product_discount = ($c_details->quantity * $supplier_product->discount_value);
                        $product_discount_price = $product_total_price - $product_discount;
                    } else {
                        $product_discount_price = $product_total_price;
                    }
                    $total_discount = $total_discount + $product_discount;
                    if ($tax_details->is_inclusive == 'No') {
                        $product_with_tax_price = $product_discount_price + (($product_discount_price * $tax_details['tax_value']) / 100);
                        $gross_amount = $gross_amount + $product_with_tax_price;
                        $total_tax = $total_tax + (($product_discount_price * $tax_details['tax_value']) / 100);
                    } else {
                        $product_with_tax_price = $product_discount_price;
                        $gross_amount = $gross_amount + $product_with_tax_price;
                    }
                    $latitude = User::where('id', $c_details->supplier_id)->value('latitude');
                    $longitude = User::where('id', $c_details->supplier_id)->value('longitude');
                    $del_id = User::join('role_user', 'role_user.user_id', '=', 'users.id')
                        ->where('users.status', '<>', 'Deleted')
                        ->where('role_user.role_id', 4)
                        ->inRandomOrder()
                        ->pluck('id')
                        ->toArray();
                    $distance = SettingModel::where('id', 2)->value('value');
                    $user_ids = User::select(DB::raw('*, ( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
                        ->having('distance', '<', $distance)
                        ->orderBy('distance')
                        ->where('id', '<>', $c_details->supplier_id)
                        ->whereIn('id', $del_id)
                        ->where('status', 'Active')
                        ->pluck('id')
                        ->toArray();
                    if (count($user_ids) > 0) {
                        $delivery_id = $user_ids[0];
                    } else {
                        $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');
                    }
                    OrderDetailsModel::create([
                        'order_id' => $order->id,
                        'supplier_id' => $c_details->supplier_id,
                        'delivery_id' => $delivery_id,
                        'payment_method' => 'online',
                        'product_id' => $c_details->product_id,
                        'product_name' => $c_details->product->product_name,
                        'price' => $supplier_product->price,
                        'gross_price' => $product_with_tax_price,
                        'qty' => $c_details->quantity,
                        'supplier_quantity' => $supplier_product->quantity,
                        'unit' => $c_details->unit,
                        'tax_value' => $tax_details['tax_value'],
                        'tax' => json_encode($tax1),
                        'discount_value' => $product_discount,
                        'discount' => json_encode($discount),
                    ]);
                }
                $gross_amount = $total_amount + $tax_total;
                if ($delivery_charge_details["max_amount"] > $gross_amount) {
                    $delivery_charge = $delivery_charge_details["delivery_charge"];
                } else {
                    $delivery_charge = 0;
                }
                $use_wallet = 'No';
                $wallet_amount = 0;
                if ($request->get('use_wallet') == "true") {
                    $wallet_amount = User::where('id', $user_id)->value('wallet_amount');
                    User::where('id', $user_id)->update([
                        'wallet_amount' => '0'
                    ]);
                    $use_wallet = 'Yes';
                }
                OrderModel::where("id", $order->id)->update([
                    'total_amount' => $total_amount,
                    'delivery_charge' => $delivery_charge,
                    'gross_amount' => $gross_amount + $delivery_charge,
                    'use_wallet' => $use_wallet,
                    'wallet_amount' => $wallet_amount,
                    'total_discount' => $total_discount,
                    'total_tax' => $tax_total,
                ]);
                CartModel::where('user_id', $user_id)->delete();
                $type = 'Order';
                $notify = new NotificationController();
                $msg = 'You Got a new order.';
                $notify->sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                $msg = 'Order Created Successfully.';
                return redirect(route('orderConfirmation', ['order_id' => $order->id]))->with('success_status', $msg);
            } else {
                $msg = 'Payment faild! Try again!';
                return redirect()->back()->with('error', $msg);
            }
        } else {
            $msg = 'Order Created Failed.';
            return redirect()->back()->with('error', $msg);
        }
    }

    public function pay_order_payment(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required'
        ]);
        try {
            $user_id = Auth::user()->id;
            $paytm_order_id = time() . $user_id;
            /* Paytm temp entry start */
            $PaytmUser = new PaytmUser();
            $PaytmUser->user_id = $user_id;
            $PaytmUser->paytm_order_id = $paytm_order_id;
            $PaytmUser->deliveryDate =$request->deliveryDate;
            $PaytmUser->deliverySlot = $request->deliverySlot;
            $PaytmUser->save();
            /* Paytm temp entry End */
            $amount = $request->amount;
            $userData = User::where('id', $user_id)->first();
            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' => $paytm_order_id,
                'user' => $user_id,
                'mobile_number' => $userData['phone'] ?? '1234567890',
                'email' => $userData['email'] ?? 'info@a2zharvests.com',
                'amount' => $amount,
                'callback_url' => route('online_place_order')
            ]);
            $response =  $payment->receive();
            return $response;
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return redirect(route('user::my_account'))->withInput(['tab' => 'wallet'])->with('error_status', $msg);
        }
    }
    public function orderConfirmation($order_id = '')
    {
        if ($order_id) {
            $order = OrderModel::where("id", $order_id)->first();
            Mail::to($order->shipping->email)->send(new OrderMail($order_id));
            return view('frontend.pages.order-confirmation', compact('order_id', 'order'));
        } else {
            $order_id = '';
            $order = [];
            return view('frontend.pages.order-confirmation', compact('order_id', 'order'));
        }
    }
}
