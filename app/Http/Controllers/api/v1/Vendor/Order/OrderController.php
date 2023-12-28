<?php

namespace App\Http\Controllers\api\v1\Vendor\Order;

use App\Http\Controllers\api\v1\Notification\NotificationController;
use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\TransactionDetailsModel;
use App\repo\Response;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Order;

class OrderController extends Controller
{
    public function get_vendor_order_history(){
        $supplier_id=Auth::user()->id;
        try {
            date_default_timezone_set("Asia/Kolkata");
            // return date('Y-m-d');
            $current_date=date('Y-m-d');
            $created_at=date('Y-m-d', strtotime($current_date .'-3 month'));
            $orders=OrderModel::join('order_details','order_details.order_id','=','orders.id')
                ->whereDate('orders.created_at','>=', $created_at)
                ->where('order_details.supplier_id',$supplier_id)
                ->whereNotIn('orders.status',['Cancel','Refunded'])
                ->groupBy('order_details.order_id')
                ->orderBy('orders.id','desc')
                ->select('orders.*')
                ->paginate(5);
            $data=[
                'count'=>count($orders),
                'pagination'=>[
                    'current_page'=>$orders->toArray()['current_page'],
                    'first_page_url'=>$orders->toArray()['first_page_url'],
                    'from'=>$orders->toArray()['from'],
                    'last_page'=>$orders->toArray()['last_page'],
                    'last_page_url'=>$orders->toArray()['last_page_url'],
                    'next_page_url'=>$orders->toArray()['next_page_url'],
                    'path'=>$orders->toArray()['path'],
                    'per_page'=>$orders->toArray()['per_page'],
                    'prev_page_url'=>$orders->toArray()['prev_page_url'],
                    'to'=>$orders->toArray()['to'],
                    'total'=>$orders->toArray()['total'],
                ],
            ];
            foreach ($orders as $order){
                $item_details=DB::table('order_details')->join('users','users.id','=','order_details.delivery_id')
                    ->where('order_details.order_id',$order->id)
                    ->where('supplier_id',$supplier_id)
                    ->select('order_details.*','users.user_name as delivery_name','users.email as delivery_email','users.phone as delivery_phone','users.location as delivery_location','users.latitude as delivery_latitude','users.longitude as delivery_longitude')
                    ->get();
                array_push($data,['details'=>[
                    'order_details'=>$order,
                    'item_details'=>$item_details,
                    'shipping_details'=>$order->shipping,
                    'payment_details'=>$order->payment,
                ]
                ]);
            }
            $msg = '';
            return Response::Success($data, $msg);
        }catch (Exception $e) {
            $data = [];
            $msg = 'No Order Found';
            return Response::Error($data, $msg);
        }
    }

    public function vendor_accept_order_item(Request $request){
        $msg = [
            'order_id.required' => 'Order Id is required.',
            'item_id.required' => 'Item Id is required.',
            'tentative_delivery_date.required' => 'Tentative Date is required.',
        ];
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'item_id' => 'required',
            'tentative_delivery_date' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $order_id = $request->get('order_id');
                $item_id = $request->get('item_id');
                $tentative_delivery_date = $request->get('tentative_delivery_date');
                $supplier_id = Auth::user()->id;
                OrderDetailsModel::whereIn('id', $item_id)->update([
                    'tentative_delivery_date' => $tentative_delivery_date,
                    'status' => 'Accepted'
                ]);
                $sms_msg1=[];
                $sms_msg2=[];
                $user_id[]=OrderModel::where('id',$order_id)->value('user_id');
                $notify_delivery='';
                for($i=0;$i<count($item_id);$i++){
                    $order_details=OrderDetailsModel::where('id', $item_id[$i])->first();
                    $product_name=$order_details["product_name"];
                    $type='Accept Order';
                    $notify_delivery=$order_details["delivery_id"];
                    array_push($sms_msg1,$product_name);
                    array_push($sms_msg2,$product_name);
                }
                $notify_delivery_id[] = $notify_delivery;
                $message1 = implode(",",$sms_msg1)." is Accepted.";
                $message2 = implode(",",$sms_msg2)." is Accepted and delivered at $tentative_delivery_date";
                NotificationController::sendNotification($type,$notify_delivery_id,$message2,$order_id,'delivery');
                $response = NotificationController::sendNotification($type,$user_id,$message1,$order_id,'user');
                $data = [$message1,$message2,$response];
                $msg = "Item is Accepted.";
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Item Not Accepted.';
                return Response::Error($data, $msg);
            }
        }else{
            $data = $validator->errors();
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }

    public function vendor_reject_order_item(Request $request){
        $msg = [
            'order_id.required' => 'Order Id is required.',
            'item_id.required' => 'Item Id is required.',
        ];
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'item_id' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $order_id = $request->get('order_id');
                $item_id = $request->get('item_id');
                $tentative_delivery_date = date('Y-m-d');
                OrderDetailsModel::whereIn('id', $item_id)->update([
                    'tentative_delivery_date' => $tentative_delivery_date,
                    'status' => 'Rejected'
                ]);
                $msg=[];
                $user_id[] = OrderModel::where('id', $order_id)->value('user_id');
                $order_details = OrderModel::where('id', $order_id)->first();
                $notify_delivery='';
                $user = User::find($order_details["user_id"]);
                for($i=0;$i<count($item_id);$i++) {
                    $item_details=OrderDetailsModel::where('id', $item_id[$i])->first();
                    $gross_price = $item_details['gross_price'];
                    OrderModel::find($order_id)->decrement('gross_amount', $gross_price);
                    $rejected_item_check = OrderDetailsModel::where('order_id', $order_id)->where('status', 'Rejected')->count();
                    $total_item_check = OrderDetailsModel::where('order_id', $order_id)->count();
                    $gross_price=$item_details['gross_price'];
                    OrderModel::find($order_id)->decrement('gross_amount', $gross_price);
                    if($item_details["payment_method"]!="cod"){
                        TransactionDetailsModel::create([
                            'user_id'=>$order_details["user_id"],
                            'transaction_id'=>'refund_'.$order_id.'_'.$item_id[$i],
                            'amount'=>$gross_price,
                            'transaction_type'=>'Refund',
                            'status'=>'Success',
                        ]);
                        $user->increment('wallet_amount',$gross_price);
                    }else{
                        if($order_details["use_wallet"]=="Yes"){
                            if($order_details["wallet_amount"]<=$gross_price) {
                                $gross_price = $order_details["wallet_amount"];
                            }
                            TransactionDetailsModel::create([
                                'user_id'=>$order_details["user_id"],
                                'transaction_id'=>'refund_'.$order_id.'_'.$item_id,
                                'amount'=>$gross_price,
                                'transaction_type'=>'Refund',
                                'status'=>'Success',
                            ]);
                            OrderModel::where('id',$order_id)->decrement('wallet_amount',$gross_price);
                            $user->increment('wallet_amount',$gross_price);
                        }
                    }
                    if ($rejected_item_check == $total_item_check) {
                        $delivery_charge = OrderModel::where('id',$order_id)->value("delivery_charge");
                        if($delivery_charge>0 && $item_details["payment_method"]!="cod"){
                            TransactionDetailsModel::create([
                                'user_id'=>$order_details["user_id"],
                                'transaction_id'=>'refund_'.$order_id.'_'.$item_id,
                                'amount'=>$delivery_charge,
                                'transaction_type'=>'Refund',
                                'status'=>'Success',
                            ]);
                            $user->increment('wallet_amount',$delivery_charge);
                        }
                        OrderModel::where('id',$order_id)->update([
                            'delivery_charge' =>0,
                            'gross_amount'=>0,
                            'status' => 'Rejected'
                        ]);
                    }
                    $product_name = $order_details['product_name'];
                    array_push($msg,$product_name);
                    $type = 'Reject Order';
                    $notify_delivery = $order_details['delivery_id'];
                }
                $notify_delivery_id[] = $notify_delivery;
                $message1 = implode(",",$msg)." is Rejected.";
                $notify = new NotificationController();
                $notify->sendNotification($type, $notify_delivery_id, $message1, $order_id, 'delivery');
                $notify->sendNotification($type, $user_id, $message1, $order_id, 'user');
                $data = [];
                $msg = "Item is Rejected.";
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Item is Not Rejected.';
                return Response::Error($data, $msg);
            }
        }else{
            $data = $validator->errors();
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }

    public function get_vendor_earning_amount(Request $request){
        $supplier_id=Auth::user()->id;
        try {
            $total_amount= DB::table('order_details')->whereDate('created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->whereDate('created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'))
                ->where('status','Delivered')
                ->where('supplier_id',$supplier_id)
                ->selectRaw("sum(gross_price) as total")
                ->get();
            $order_details= DB::table('order_details')->whereDate('created_at', '>=',  Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->whereDate('created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'))
                ->where('status','Delivered')
                ->where('supplier_id',$supplier_id)
                ->select(DB::raw('DATE(created_at) as date'),'payment_method',DB::raw('sum(gross_price) as total'))
                ->groupBy('date')
                ->groupBy('payment_method')
                ->get();
            $day_array=[];
            $data2=[];
            $total=$total_amount;
            $cod_total=0;
            $online_total=0;
            $wallet_total=0;
            foreach ($order_details as $details){
                $day=date('l', strtotime($details->date));
                if (!in_array($day,$day_array)) {
                    array_push($day_array,$day);
                    $cod_total=0;
                    $online_total=0;
                    $wallet_total=0;
                }
                if($details->payment_method=='cod') {
                    $cod_total=$cod_total+$details->total;
                    $data2[$day]['date'] = $details->date;
                    $data2[$day]['cod'] = $details;
                    $data2[$day]['online'] = ['total'=>$online_total];
                    $data2[$day]['wallet'] = ['total'=>$wallet_total];
                }elseif($details->payment_method=='online'){
                    $online_total=$online_total+$details->total;
                    $data2[$day]['date'] = $details->date;
                    $data2[$day]['online'] = $details;
                    $data2[$day]['cod'] = ['total'=>$cod_total];
                    $data2[$day]['wallet'] = ['total'=>$wallet_total];
                }elseif($details->payment_method=='wallet'){
                    $wallet_total=$wallet_total+$details->total;
                    $data2[$day]['date'] = $details->date;
                    $data2[$day]['wallet'] = $details;
                    $data2[$day]['cod'] = ['total'=>$cod_total];
                    $data2[$day]['online'] = ['total'=>$online_total];
                }
            }
            $data= $data2;
            $msg = '';
            return Response::Success(['days'=>$data,'total'=>$total], $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = $e;
            return Response::Error($data, $msg);
        }
    }
}
