<?php

namespace App\Http\Controllers\api\v1\Delivery\Order;

use App\Http\Controllers\api\v1\Notification\NotificationController;
use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\PaymentModel;
use App\repo\Response;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Order;

class OrderController extends Controller
{
    public function get_delivery_pending_order(Request $request){
        $delivery_id=Auth::user()->id;
        try {
            date_default_timezone_set("Asia/Kolkata");
            $current_date=date('Y-m-d');
            $created_at=date('Y-m-d', strtotime($current_date .'-3 month'));
//            $orders=OrderModel::whereDate('created_at','>=', $created_at)
//                ->where('delivery_id',$delivery_id)
//                ->where('status','Processing')
//                ->orderBy('id','desc')
//                ->paginate(10);

            $orders=OrderModel::join('order_details','order_details.order_id','=','orders.id')
                ->whereDate('orders.created_at','>=', $created_at)
                ->where('order_details.delivery_id',$delivery_id)
                ->where('order_details.status','Accepted')
                ->orderBy('orders.id','desc')
                ->groupBy('order_details.order_id')
                ->select('orders.*')
                ->paginate(10);
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
                $item_details=DB::table('order_details')->join('users','users.id','=','order_details.supplier_id')
                    ->where('order_details.order_id',$order->id)
                    ->where('order_details.delivery_id',$delivery_id)
                    ->where('order_details.status','Accepted')
                    ->select('order_details.*','users.user_name as vendor_name','users.email as vendor_email','users.phone as vendor_phone','users.location as vendor_location','users.latitude as vendor_latitude','users.longitude as vendor_longitude')
                    ->get();
                array_push($data,[
                    'order_details'=>$order,
                    'item_details'=>$item_details,
                    'shipping_details'=>$order->shipping,
                    'payment_details'=>$order->payment,
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

    public function update_delivery_pending_order_item_status(Request $request){
        $delivery_id = Auth::user()->id;
        try {
            $data1=[];
            $data=$request->all();
            $order_id= $data[0]['order_id'];
            $tentative_customer_delivery_date= $data[0]['tentative_customer_delivery_date'];
            $product_name_msg=[];
            for ($i = 0; $i < count($data[0]['item']); $i++) {
               $item_id=$data[0]['item'][$i]['item_id'];
               OrderDetailsModel::where('id',$item_id)->where('order_id',$order_id)->where('delivery_id',$delivery_id)->update([
                   'status'=>'Picked Up',
                   'tentative_customer_delivery_date'=>$tentative_customer_delivery_date,
               ]);
                $product_name=OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->where('delivery_id',$delivery_id)->value('product_name');
                array_push($product_name_msg,$product_name);
            }
            $msg=implode(',',$product_name_msg);
            if(count($product_name_msg)==1){
                $msg .=" is Picked Up.";
            }else{
                $msg .=" are Picked Up.";
            }
            $type='Picked Up Order';
            $user_id[]=OrderModel::where('id',$order_id)->value('user_id');
            NotificationController::sendNotification($type,$user_id,$msg,$order_id,'user');
            $pending_item_check=OrderDetailsModel::where('order_id',$order_id)->whereIn('status',['Rejected','Picked Up'])->count();
            $total_item_check=OrderDetailsModel::where('order_id',$order_id)->whereNotIn('status',['Delivered','Cancel','Refunded'])->count();
            if($pending_item_check==$total_item_check){
                OrderModel::where('id',$order_id)->update([
                    'status'=>'Picked Up'
                ]);
            }
            $msg = 'Item Pickup Successfully.';
            return Response::Success($data1, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Item Not Pickup.';
            return Response::Error($data, $msg);
        }
    }

    public function get_delivery_pick_up_order(Request $request){
        $delivery_id=Auth::user()->id;
        try {
            date_default_timezone_set("Asia/Kolkata");
            $current_date=date('Y-m-d');
            $created_at=date('Y-m-d', strtotime($current_date .'-3 month'));
            $orders=OrderModel::join('order_details','order_details.order_id','=','orders.id')->whereDate('orders.created_at','>=', $created_at)
                ->where('order_details.delivery_id',$delivery_id)
                ->where('order_details.status','Picked Up')
                ->orderBy('orders.id','desc')
                ->groupBy('order_details.order_id')
                ->select('orders.*')
                ->paginate(10);
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
                $item_details=DB::table('order_details')->join('users','users.id','=','order_details.supplier_id')
                    ->where('order_details.order_id',$order->id)
                    ->where('order_details.delivery_id',$delivery_id)
                    ->where('order_details.status','Picked Up')
                    ->select('order_details.*','users.user_name as vendor_name','users.email as vendor_email','users.phone as vendor_phone','users.location as vendor_location')
                    ->get();
                array_push($data,[
                    'order_details'=>$order,
                    'item_details'=>$item_details,
                    'shipping_details'=>$order->shipping,
                    'payment_details'=>$order->payment,
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

    public function update_delivery_pick_up_order_item_status(Request $request){
        $delivery_id = Auth::user()->id;
        try {
            $data1=[];
            $data=$request->all();
            $order_id= $data[0]['order_id'];
            $product_name_msg=[];
            for ($i = 0; $i < count($data[0]['item']); $i++) {
                $item_id=$data[0]['item'][$i]['item_id'];
                OrderDetailsModel::where('id',$item_id)->where('order_id',$order_id)->where('delivery_id',$delivery_id)->update([
                    'status'=>'Delivered'
                ]);
                $product_name=OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->where('delivery_id',$delivery_id)->value('product_name');
                array_push($product_name_msg,$product_name);
            }
            $type='Delivered Order';
            $msg=implode(',',$product_name_msg);
            if(count($product_name_msg)==1){
                $msg .=" is Delivered.";
            }else{
                $msg .=" are Delivered.";
            }
            $user_id[]=OrderModel::where('id',$order_id)->value('user_id');
            NotificationController::sendNotification($type,$user_id,$msg,$order_id,'user');
            $pending_item_check=OrderDetailsModel::where('order_id',$order_id)->whereIn('status',['Rejected','Delivered'])->count();
            $total_item_check=OrderDetailsModel::where('order_id',$order_id)->whereNotIn('status',['Picked Up','Cancel','Refunded'])->count();

            $payment_id=OrderModel::where('id',$order_id)->value('payment_id');
            if($pending_item_check==$total_item_check){
                OrderModel::where('id',$order_id)->update([
                    'status'=>'Delivered'
                ]);
                $check_payment_method=PaymentModel::where('id',$payment_id)->value('payment_method');
                if($check_payment_method=='cod'){
                    PaymentModel::where('id',$payment_id)->update([
                        'payment_status'=>'Completed'
                    ]);
                }

            }

            $msg = 'Item Delivered Successfully.';
            return Response::Success($data1, $msg);
        }catch(Exception $e) {
            $data = [];
            $msg = 'Item Not Delivered.';
            return Response::Error($data, $msg);
        }
    }

    public function get_delivery_order_history(Request $request){
        $delivery_id=Auth::user()->id;
        try {
            date_default_timezone_set("Asia/Kolkata");
            $current_date=date('Y-m-d');
            $created_at=date('Y-m-d', strtotime($current_date .'-3 month'));
//            $orders=OrderModel::whereDate('created_at','>=', $created_at)
//                ->where('delivery_id',$delivery_id)
//              //  ->where('status','Delivered')
//                ->orderBy('id','desc')
//                ->paginate(10);
            $orders=OrderModel::join('order_details','order_details.order_id','=','orders.id')->whereDate('orders.created_at','>=', $created_at)
                ->where('order_details.delivery_id',$delivery_id)
                ->orderBy('orders.id','desc')
                ->groupBy('order_details.order_id')
                ->select('orders.*')
                ->paginate(10);
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
                $item_details=DB::table('order_details')->join('users','users.id','=','order_details.supplier_id')
                    ->where('order_details.order_id',$order->id)
                    ->where('order_details.delivery_id',$delivery_id)
                    ->select('order_details.*','users.user_name as vendor_name','users.email as vendor_email','users.phone as vendor_phone','users.location as vendor_location','users.latitude as vendor_latitude','users.longitude as vendor_longitude')
                    ->get();
                array_push($data,[
                    'order_details'=>$order,
                    'item_details'=>$item_details,
                    'shipping_details'=>$order->shipping,
                    'payment_details'=>$order->payment,
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

    public function get_delivery_earning_amount(Request $request){
        $delivery_id=Auth::user()->id;
        try {
            $total_amount= OrderDetailsModel::whereDate('created_at', '>=',Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->whereDate('created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'))
                ->whereNotIn('status',['Cancel','Refunded'])
                ->where('payment_method','cod')
                ->where('delivery_id',$delivery_id)
                ->sum('gross_price');

            $order_details= DB::table('order_details')->whereDate('created_at', '>=', Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->whereDate('created_at', '<=', Carbon::now()->endOfWeek()->format('Y-m-d'))
                ->whereNotIn('status',['Cancel','Refunded'])
                ->where('payment_method','cod')
                ->where('delivery_id',$delivery_id)
                ->select(DB::raw('DATE(created_at) as date'),'payment_method',DB::raw('sum(gross_price) as total'))
                ->groupBy('date')
                ->get();
            $day_array=[];
            $data2=[];
            $total=$total_amount;
              foreach ($order_details as $details){
                  $day=date('l', strtotime($details->date));
                      if (!in_array($day,$day_array)) {
                          array_push($day_array,$day);
                      }
                      $data2[$day]=$details;

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

    public function update_customer_delivery_date(Request $request)
    {
        $msg = [
            'order_id.required' => 'Order Id is required.',
            'item_id.required' => 'Item Id is required.',
            'tentative_customer_delivery_date.required' => 'Tentative Customer Delivery Date is required.',
        ];
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'item_id' => 'required',
            'tentative_customer_delivery_date' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $order_id = $request->get('order_id');
                $item_id = $request->get('item_id');
                $tentative_customer_delivery_date = $request->get('tentative_customer_delivery_date');
                $delivery_id = Auth::user()->id;
                OrderDetailsModel::where('id', $item_id)->where('order_id', $order_id)->where('delivery_id', $delivery_id)->update([
                    'tentative_customer_delivery_date' => $tentative_customer_delivery_date,
                ]);
                $data = [];
                $msg = 'Customer Delivery Date Updated Successfully.';
                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = 'Customer Delivery Date Not Updated.';
                return Response::Error($data, $msg);
            }
        } else {
            $data = $validator->errors();
            $msg = 'Validation Error Found.';
            return Response::Error($data, $msg);
        }
    }

}
