<?php

namespace App\Http\Controllers\api\v1\User\Order;

use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use App\Http\Controllers\api\v1\Notification\NotificationController;
use App\Model\CartModel;
use App\Model\CategoryModel;
use App\Model\DeliverySettingModel;
use App\Model\DeliverySlotsModel;
use App\Model\DiscountModel;
use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\PaymentModel;
use App\Model\ProductImageModel;
use App\Model\ProductModel;
use App\Model\PromocodesModel;
use App\Model\SettingModel;
use App\Model\ShippingModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\Model\TaxValueModel;
use App\Model\TransactionDetailsModel;
use App\repo\datavalue;
use App\repo\Response;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;

class OrderController extends Controller
{
    public function save_shipping(Request $request)
    {
        $msg = [
            'name.required' => 'Enter Your Name',
            'email.required' => 'Enter Your Email.',
            'phone_no.required' => 'Enter Your Phone No',
            'address.required' => 'Enter Your Address.',
            'pincode.required' => 'Enter Pincode.'
        ];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone_no' => 'required',
            'address' => 'required',
            'pincode' => 'required'
        ], $msg);
        if ($validator->passes()) {
            try {
                $address = $request->get('address');
                $latLng = datavalue::get_lat_long($request->get('pincode'));
                $latitude =   $latLng['lat'];
                $longitude =   $latLng['lng'];
                if ($latitude == "") {
                    $data = $latLng;
                    $msg = 'Pin code is not valid.';
                    return Response::Error($data, $msg);
                } else {
                    $user_ids = datavalue::getNearbySupplier($latitude, $longitude);
                    $cart_suppliers = CartModel::where("user_id", Auth::user()->id)->pluck("supplier_id")->toArray();
                    $count = count(array_diff($cart_suppliers, $user_ids));
                    if ($count > 0) {
                        $data = [];
                        $msg = 'Cart products is not available for this pin code.';
                        return Response::Error($data, $msg);
                    }
                }
                $shipping = ShippingModel::create([
                    'user_id' => Auth::user()->id,
                    'name' => $request->get('name'),
                    'email' => $request->get('email'),
                    'phone_no' => $request->get('phone_no'),
                    'address' => $request->get('address'),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'pincode' => $request->get('pincode'),
                    'landmark' => $request->get('landmark'),
                    'city' => $request->get('city'),
                    'state' => $request->get('state'),
                ]);
                $data = $shipping;
                $msg = 'Shipping Details Saved.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Shipping Details Not Saved.';
                return Response::Error($data, $e->getMessage());
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function get_prev_shipping_details(Request $request)
    {
        try {
            $shipping_details = ShippingModel::where('user_id', Auth::user()->id)->latest()->first();
            if ($shipping_details != null) {
                $data = $shipping_details;
                $msg = '';
                return Response::Success($data, $msg);
            } else {
                $data = [];
                $msg = 'Shipping Details Not Found.';
                return Response::Error($data, $msg);
            }
        } catch (Exception $e) {
            $data = [];
            $msg = 'Shipping Details Not Found.';
            return Response::Error($data, $msg);
        }
    }

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
            $status = DeliverySlotsModel::where("slot_name", $user_delivery_time)->value("status");
            if ((($availability <= $bookedCount) && $user_delivery_date != "") || $status == "Inactive") {
                $msg = 'Selected Slot Not Available...';
                $data = [];
                return Response::Error($data, $msg);
            }
            $cart_count = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->count();
            if ($cart_count > 0) {
                try {
                    $applied_promo_code = $request->applied_promo_code;
                    $cashback_data = $request->cashback_data;
                    $s = new datavalue();
                    $unique_id = $s->getUniqueCode('ORDER');
                    $transaction_id = $s->getUniqueCode('TXN');
                    $delivery_id = User::where('is_default_delivery', '=', 'Yes')->value('id');
                    if ($request->get('payment_method') == 'cod') {
                        if ($request->get('createShipping') == "true") {
                            $shipping = ShippingModel::create([
                                'user_id' => Auth::user()->id,
                                'name' => Auth::user()->user_name,
                                'email' => Auth::user()->email,
                                'phone_no' => Auth::user()->phone,
                                'address' => Auth::user()->location,
                                'latitude' => Auth::user()->latitude,
                                'longitude' => Auth::user()->longitude,
                            ]);
                            $shipping_id = $shipping->id;
                        } else {
                            $shipping_id =  $request->get('shipping_id');
                        }
                        $payment = PaymentModel::create([
                            'payment_method' => 'cod',
                            'payment_status' => 'Pending',
                            'payment_date_time' => NOW(),
                        ]);
                        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                        $is_delivery_charge = 1;
                        $order = OrderModel::create([
                            'order_id' => $unique_id,
                            'user_id' => Auth::user()->id,
                            'delivery_id' => $delivery_id,
                            'payment_id' => $payment->id,
                            'shipping_id' => $shipping_id,
                            'transaction_id' => $transaction_id,
                            'cashback_status' => "Processing",
                            'datetime' => NOW(),
                            'user_delivery_date' => $request->get('deliveryDate'),
                            'user_delivery_time' => $request->get('deliverySlot'),
                            'status' => 'Processing',
                        ]);
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
                            $product_total_price = $supplier_product->price;
                            $discount_details = DiscountModel::find($supplier_product->discount_id);
                            $discount['id'] = $discount_details->id;
                            $discount['discount_name'] = $discount_details->discount_name;
                            $discount['discount_value'] = $supplier_product->discount_value;
                            $tax_details = TaxModel::find($c_details->product->tax_id);
                            $tax['tax_id'] = $tax_details->id;
                            $tax['tax_name'] = $tax_details->tax_name;
                            $tax['tax_total_value'] = $tax_details->tax_value;
                            $tax['is_inclusive'] = $tax_details->is_inclusive;
                            $tax['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
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
                                $gross_amount = $gross_amount + $product_with_tax_price;
                                $total_tax = $total_tax + (($product_discount_price * $tax_details['tax_value']) / 100);
                            } else {
                                $product_with_tax_price = $product_discount_price;
                                $gross_amount = $gross_amount + ceil($product_with_tax_price);
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
                                'tax' => json_encode($tax),
                                'discount_value' => $product_discount,
                                'discount' => json_encode($discount),
                            ]);
                        }
                        if ($is_delivery_charge > 0 && $delivery_charge_details["max_amount"] > $gross_amount) {
                            $delivery_charge = $delivery_charge_details["delivery_charge"];
                        } else {
                            $delivery_charge = 0;
                        }
                        $use_wallet = 'No';
                        $wallet_amount = 0;
                        if ($request->get('use_wallet')) {
                            $wallet_amount = User::where('id', Auth::user()->id)->value('wallet_amount');
                            User::where('id', Auth::user()->id)->update([
                                'wallet_amount' => '0'
                            ]);
                            $use_wallet = 'Yes';
                        }
                        if ($applied_promo_code == "") {
                            $promo_discount = 0;
                        } else {
                            $promo_data = PromocodesModel::where("promo_code", $applied_promo_code)->first();
                            $promo_discount = ($gross_amount / 100) * $promo_data["discount_percent"];
                            if ($promo_discount > $promo_data["discount_upto"]) {
                                $promo_discount = $promo_data["discount_upto"];
                            }
                        }
                        OrderModel::where("id", $order->id)->update([
                            'total_amount' => $total_amount,
                            'delivery_charge' => $delivery_charge,
                            'gross_amount' => $gross_amount + $delivery_charge - $promo_discount,
                            'use_wallet' => $use_wallet,
                            'wallet_amount' => $wallet_amount,
                            'total_discount' => $total_discount,
                            'total_tax' => $total_tax,
                            'applied_promo_code' => $applied_promo_code,
                            'promo_discount' => $promo_discount,
                            'cashback_amount' => 0,
                        ]);
                        CartModel::where('user_id', Auth::user()->id)->delete();
                        $type = 'Order';
                        $msg = 'You got a new order.';
                        NotificationController::sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                        $data = [];
                        $msg = 'Order Created Successfully.';
                        return Response::Success($data, $msg);
                    } elseif ($request->get('payment_method') == 'wallet') {
                        if ($request->get('createShipping')) {
                            $shipping = ShippingModel::create([
                                'user_id' => Auth::user()->id,
                                'name' => Auth::user()->user_name,
                                'email' => Auth::user()->email,
                                'phone_no' => Auth::user()->phone,
                                'address' => Auth::user()->location,
                                'latitude' => Auth::user()->latitude,
                                'longitude' => Auth::user()->longitude,
                            ]);
                            $shipping_id = $shipping->id;
                        } else {
                            $shipping_id =  $request->get('shipping_id');
                        }
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
                            'cashback_status' => "Processing",
                            'datetime' => NOW(),
                            'user_delivery_date' => $request->get('deliveryDate'),
                            'user_delivery_time' => $request->get('deliverySlot'),
                            'status' => 'Processing',
                        ]);
                        $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                        $is_delivery_charge = 1;
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
                            $product_total_price = $supplier_product->price;
                            $discount_details = DiscountModel::find($supplier_product->discount_id);
                            $discount['id'] = $discount_details->id;
                            $discount['discount_name'] = $discount_details->discount_name;
                            $discount['discount_value'] = $supplier_product->discount_value;
                            $tax_details = TaxModel::find($c_details->product->tax_id);
                            $tax['tax_id'] = $tax_details->id;
                            $tax['tax_name'] = $tax_details->tax_name;
                            $tax['tax_total_value'] = $tax_details->tax_value;
                            $tax['is_inclusive'] = $tax_details->is_inclusive;
                            $tax['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
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
                                'payment_method' => 'wallet',
                                'product_id' => $c_details->product_id,
                                'product_name' => $c_details->product->product_name,
                                'price' => $supplier_product->price,
                                'gross_price' => $product_with_tax_price,
                                'qty' => $c_details->quantity,
                                'supplier_quantity' => $supplier_product->quantity,
                                'unit' => $c_details->unit,
                                'tax_value' => $tax_details['tax_value'],
                                'tax' => json_encode($tax),
                                'discount_value' => $product_discount,
                                'discount' => json_encode($discount),
                            ]);
                        }
                        if ($is_delivery_charge > 0 && $delivery_charge_details["max_amount"] > $gross_amount) {
                            $delivery_charge = $delivery_charge_details["delivery_charge"];
                        } else {
                            $delivery_charge = 0;
                        }
                        $use_wallet = 'No';
                        $wallet_amount = 0;
                        if ($request->get('use_wallet')) {
                            $wallet_amount = User::where('id', Auth::user()->id)->value('wallet_amount');
                            User::where('id', Auth::user()->id)->update([
                                'wallet_amount' => '0'
                            ]);
                            $use_wallet = 'Yes';
                        }
                        if ($applied_promo_code == "") {
                            $promo_discount = 0;
                        } else {
                            $promo_data = PromocodesModel::where("promo_code", $applied_promo_code)->first();
                            $promo_discount = ($gross_amount / 100) * $promo_data["discount_percent"];
                            if ($promo_discount > $promo_data["discount_upto"]) {
                                $promo_discount = $promo_data["discount_upto"];
                            }
                        }
                        OrderModel::where("id", $order->id)->update([
                            'total_amount' => $total_amount,
                            'delivery_charge' => $delivery_charge,
                            'gross_amount' => $gross_amount + $delivery_charge - $promo_discount,
                            'use_wallet' => $use_wallet,
                            'wallet_amount' => $wallet_amount,
                            'total_discount' => $total_discount,
                            'total_tax' => $total_tax,
                            'applied_promo_code' => $applied_promo_code,
                            'promo_discount' => $promo_discount,
                            //                            'cashback_amount' => $cashback_data["amount"],
                            'cashback_amount' => 0,
                        ]);
                        CartModel::where('user_id', Auth::user()->id)->delete();
                        $type = 'Order';
                        $msg = 'You got a new order.';
                        NotificationController::sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                        $data = [];
                        $msg = 'Order Created Successfully.';
                        return Response::Success($data, $msg);
                    } else if ($request->get('payment_method') == 'online') {
                        $paytm_order_id = $request->get('paytm_order_id');
                        $paytm_status = PaytmWallet::with('status');
                        $paytm_status->prepare(['order' => $paytm_order_id]);
                        $paytm_status->check();

                        if ($paytm_status->isSuccessful()) {
                            $transaction_id = $paytm_status->getTransactionId();
                            if ($request->get('createShipping')) {
                                $shipping = ShippingModel::create([
                                    'user_id' => Auth::user()->id,
                                    'name' => Auth::user()->user_name,
                                    'email' => Auth::user()->email,
                                    'phone_no' => Auth::user()->phone,
                                    'address' => Auth::user()->location,
                                    'latitude' => Auth::user()->latitude,
                                    'longitude' => Auth::user()->longitude,
                                ]);
                                $shipping_id = $shipping->id;
                            } else {
                                $shipping_id =  $request->get('shipping_id');
                            }
                            $payment = PaymentModel::create([
                                'payment_method' => 'online',
                                'payment_status' => 'Completed',
                                'payment_date_time' => NOW(),
                            ]);

                            $cart_details = CartModel::where('user_id', Auth::user()->id)->whereHas('active_supplier_product')->get();
                            $is_delivery_charge = 1;
                            $order = OrderModel::create([
                                'order_id' => $unique_id,
                                'user_id' => Auth::user()->id,
                                'delivery_id' => $delivery_id,
                                'payment_id' => $payment->id,
                                'shipping_id' => $shipping_id,
                                'transaction_id' => $transaction_id,
                                'cashback_status' => "Processing",
                                'datetime' => NOW(),
                                'user_delivery_date' => $request->get('deliveryDate'),
                                'user_delivery_time' => $request->get('deliverySlot'),
                                'status' => 'Processing',
                            ]);
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
                                $product_total_price = $supplier_product->price;
                                $discount_details = DiscountModel::find($supplier_product->discount_id);
                                $discount['id'] = $discount_details->id;
                                $discount['discount_name'] = $discount_details->discount_name;
                                $discount['discount_value'] = $supplier_product->discount_value;
                                $tax_details = TaxModel::find($c_details->product->tax_id);
                                $tax['tax_id'] = $tax_details->id;
                                $tax['tax_name'] = $tax_details->tax_name;
                                $tax['tax_total_value'] = $tax_details->tax_value;
                                $tax['is_inclusive'] = $tax_details->is_inclusive;
                                $tax['tax_values'] = TaxValueModel::where('tax_id', $tax_details->id)->get();
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
                                    //  $user_ids=$user_ids;
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
                                    'tax' => json_encode($tax),
                                    'discount_value' => $product_discount,
                                    'discount' => json_encode($discount),
                                ]);
                            }
                            if ($is_delivery_charge > 0 && $delivery_charge_details["max_amount"] > $gross_amount) {
                                $delivery_charge = $delivery_charge_details["delivery_charge"];
                            } else {
                                $delivery_charge = 0;
                            }
                            $use_wallet = 'No';
                            $wallet_amount = 0;
                            if ($request->get('use_wallet')) {
                                $wallet_amount = User::where('id', Auth::user()->id)->value('wallet_amount');
                                User::where('id', Auth::user()->id)->update([
                                    'wallet_amount' => '0'
                                ]);
                                $use_wallet = 'Yes';
                            }
                            if ($applied_promo_code == "") {
                                $promo_discount = 0;
                            } else {
                                $promo_data = PromocodesModel::where("promo_code", $applied_promo_code)->first();
                                $promo_discount = ($gross_amount / 100) * $promo_data["discount_percent"];
                                if ($promo_discount > $promo_data["discount_upto"]) {
                                    $promo_discount = $promo_data["discount_upto"];
                                }
                            }
                            OrderModel::where("id", $order->id)->update([
                                'total_amount' => $total_amount,
                                'delivery_charge' => $delivery_charge,
                                'gross_amount' => $gross_amount + $delivery_charge - $promo_discount,
                                'use_wallet' => $use_wallet,
                                'wallet_amount' => $wallet_amount,
                                'total_discount' => $total_discount,
                                'total_tax' => $total_tax,
                                'applied_promo_code' => $applied_promo_code,
                                'promo_discount' => $promo_discount,
                                //                                'cashback_amount' => $cashback_data["amount"],
                                'cashback_amount' => 0,
                            ]);
                            CartModel::where('user_id', Auth::user()->id)->delete();
                            $type = 'Order';
                            $msg = 'You got a new order.';
                            NotificationController::sendNotification($type, $notify_supplier_ids, $msg, $order->id, 'supplier');
                            $data = [];
                            $msg = 'Order Created Successfully.';
                            return Response::Success($data, $msg);
                        } else if ($paytm_status->isFailed()) {
                            $responseMag = $paytm_status->getResponseMessage();
                            $data = $paytm_order_id;
                            $msg = 'Order Created Failed not captured.';
                            return Response::Error($responseMag, $msg);
                        }
                    } else {
                        $data = [];
                        $msg = 'Check Your Payment Method';
                        return Response::Error($data, $msg);
                    }
                } catch (Exception $e) {
                    $data = ["exception error", $request->get('transaction_id')];
                    $msg = $e->getMessage();
                    return Response::Error($data, $msg);
                }
            } else {
                $data = [];
                $msg = 'You have no item in your cart.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }
    public function get_user_order_history(Request $request)
    {
        $user_id = Auth::user()->id;
        try {
            date_default_timezone_set("Asia/Kolkata");
            $current_date = date('Y-m-d');
            $created_at = date('Y-m-d', strtotime($current_date . '-3 month'));
            $orders = OrderModel::whereDate('created_at', '>=', $created_at)
                ->where('user_id', $user_id)
                ->whereNotIn('status', ['Cancel', 'Refunded'])
                ->orderBy('id', 'desc')
                ->paginate(10);
            $data = [
                'count' => count($orders),
                'pagination' => [
                    'current_page' => $orders->toArray()['current_page'],
                    'first_page_url' => $orders->toArray()['first_page_url'],
                    'from' => $orders->toArray()['from'],
                    'last_page' => $orders->toArray()['last_page'],
                    'last_page_url' => $orders->toArray()['last_page_url'],
                    'next_page_url' => $orders->toArray()['next_page_url'],
                    'path' => $orders->toArray()['path'],
                    'per_page' => $orders->toArray()['per_page'],
                    'prev_page_url' => $orders->toArray()['prev_page_url'],
                    'to' => $orders->toArray()['to'],
                    'total' => $orders->toArray()['total'],
                ],
            ];
            foreach ($orders as $order) {
                $item_details = DB::table('order_details')->join('users', 'users.id', '=', 'order_details.delivery_id')
                    ->where('order_details.order_id', $order->id)
                    ->select('order_details.*', 'users.user_name as delivery_name', 'users.email as delivery_email', 'users.phone as delivery_phone', 'users.location as delivery_location')
                    ->get();
                array_push($data, [
                    // 'delivery_boy_details'=>$order->delivery,
                    'order_details' => $order,
                    'item_details' => $item_details,
                    'shipping_details' => $order->shipping,
                    'payment_details' => $order->payment,
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg = 'No Order Found';
            return Response::Error($data, $msg);
        }
    }

    public function user_cancel_order_item(Request $request)
    {
        $msg = [
            'item_id.required' => 'Item Id is required.',
            'order_id.required' => 'Order Id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'order_id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $item_id = $request->get('item_id');
                $order_id = $request->get('order_id');
                $order_details = OrderModel::where('id', $order_id)->first();
                $item_status_check = OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->value('status');
                $item_details = OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->first();
                if ($item_status_check != 'Cancel') {
                    OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->update([
                        'status' => 'Cancel'
                    ]);
                    $gross_price = OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->value('gross_price');
                    OrderModel::find($order_id)->decrement('gross_amount', $gross_price);
                    $cancel_item_check = OrderDetailsModel::where('order_id', $order_id)->where('status', 'Cancel')->count();
                    $total_item_check = OrderDetailsModel::where('order_id', $order_id)->count();
                    $user = User::find(Auth::user()->id);
                    if ($item_details["payment_method"] != "cod") {
                        TransactionDetailsModel::create([
                            'user_id' => Auth::user()->id,
                            'transaction_id' => 'refund_' . $order_id . '_' . $item_id,
                            'amount' => $gross_price,
                            'transaction_type' => 'Refund',
                            'status' => 'Success',
                        ]);
                        $user->increment('wallet_amount', $gross_price);
                    } else {
                        if ($order_details["use_wallet"] == "Yes") {
                            if ($order_details["wallet_amount"] <= $gross_price) {
                                $gross_price = $order_details["wallet_amount"];
                            }
                            TransactionDetailsModel::create([
                                'user_id' => Auth::user()->id,
                                'transaction_id' => 'refund_' . $order_id . '_' . $item_id,
                                'amount' => $gross_price,
                                'transaction_type' => 'Refund',
                                'status' => 'Success',
                            ]);
                            OrderModel::where('id', $order_id)->decrement('wallet_amount', $gross_price);
                            $user->increment('wallet_amount', $gross_price);
                        }
                    }
                    if ($cancel_item_check == $total_item_check) {
                        $delivery_charge = OrderModel::where('id', $order_id)->value("delivery_charge");
                        if ($delivery_charge > 0 && $item_details["payment_method"] != "cod") {
                            TransactionDetailsModel::create([
                                'user_id' => Auth::user()->id,
                                'transaction_id' => 'refund_' . $order_id . '_' . $item_id,
                                'amount' => $delivery_charge,
                                'transaction_type' => 'Refund',
                                'status' => 'Success',
                            ]);
                            $user->increment('wallet_amount', $delivery_charge);
                        }
                        OrderModel::where('id', $order_id)->update([
                            'delivery_charge' => 0,
                            'gross_amount' => 0,
                            'status' => 'Cancel'
                        ]);
                    }
                    $data = [];
                    $msg = 'Order Item Cancel Successfully.';
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg = 'Order Item already Canceled';
                    return Response::Error($data, $msg);
                }
            } catch (Exception $e) {
                $data = [];
                $msg = 'Order Item Cancel failed';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function user_cancel_order(Request $request)
    {
        $msg = [
            'id.required' => 'Order Id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $order_id = $request->get('id');
            try {
                $order_status_check = OrderModel::where('id', $order_id)->where('user_id', Auth::user()->id)->value('status');
                if ($order_status_check != 'Cancel') {
                    OrderModel::where('id', $order_id)->update([
                        'status' => 'Cancel'
                    ]);
                    $data = [];
                    $msg = 'Order Cancel Successfully.';
                    return Response::Success($data, $msg);
                } else {
                    $data = [];
                    $msg = 'Order Already Canceled.';
                    return Response::Success($data, $msg);
                }
            } catch (Exception $e) {
                $data = [];
                $msg = 'Order Cancel failed';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

    public function delivery_boy_review_rateing(Request $request)
    {
        $msg = [
            'item_id.required' => 'Item Id required.',
            'delivery_id.required' => 'Item Id required.',
        ];
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'delivery_id' => 'required',
        ], $msg);
        if ($validator->passes()) {

            $item_id = $request->get('item_id');
            $delivery_id = $request->get('delivery_id');
            $count_check = OrderDetailsModel::where('id', $item_id)->where('delivery_id', $delivery_id)->value('star');
            if ($count_check == 0) {
                OrderDetailsModel::where('id', $item_id)->where('delivery_id', $delivery_id)->update([
                    'star' => $request->rating,
                    'review' => $request->comment,
                ]);
                $count = OrderDetailsModel::where('delivery_id', $delivery_id)->where('star', '<>', 0)->count();
                $sum = OrderDetailsModel::where('delivery_id', $delivery_id)->where('star', '<>', 0)->sum('star');
                $avg = $sum / $count;
                User::where('id', $delivery_id)->update([
                    'star' => ceil($avg)
                ]);
                $data = [];
                $msg = 'Review Submitted Successfully. !!!';
                return Response::Success($data, $msg);
            } else {
                $data = [];
                $msg = 'Review Already Submitted. !!!';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }
    public function delivery_boy_tips(Request $request)
    {
        $msg = [
            'item_id.required' => 'Item Id required.',
            'delivery_id.required' => 'Item Id required.',
            'amount.required' => 'Amount is required.',
            'paytm_order_id.required' => 'Order Id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'item_id' => 'required',
            'delivery_id' => 'required',
            'amount' => 'required',
            'paytm_order_id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            $paytm_order_id = $request->get('paytm_order_id');
            $status = PaytmWallet::with('status');
            $status->prepare(['order' => $paytm_order_id]);
            $status->check();

            if ($status->isSuccessful()) {
                $item_id = $request->get('item_id');
                $delivery_id = $request->get('delivery_id');
                OrderDetailsModel::where('id', $item_id)->where('delivery_id', $delivery_id)->update([
                    'delivery_tips' => $request->amount
                ]);
                $data = [];
                $msg = 'Tips Sent To Delivery. !!!';
                return Response::Success($data, $msg);
            } else {
                $data = $paytm_order_id;
                $msg = 'Payment not done.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }
}
