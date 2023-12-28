<?php

namespace App\Http\Controllers\Admin\Order;

use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\PaymentModel;
use App\Model\ProductModel;
use App\Model\ShippingModel;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }
    public function index(Request $request){
        if($request->get('start_date')!=''){
            $start_date=$request->get('start_date');
        }else{
            $start_date=date('Y-m-d');
        }
        if($request->get('end_date')!=''){
            $end_date=$request->get('end_date');
        }else{
            $end_date=date('Y-m-d');
        }
        if(request()->ajax()) {
            $start_date=$request->get('start_date');
            $end_date=$request->get('end_date');
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::select('*')
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('datetime', '>=', $start_date);
                        $query->whereDate('datetime', '<=', $end_date);
                    })
                    ->orderBy('id', 'desc')->get();
            }else{
                $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
                $order_ids = OrderDetailsModel::whereIn('supplier_id',$supplier_ids)->groupBy('order_id')->pluck('order_id')->toArray();
                $data = OrderModel::whereIn('id',$order_ids)->select('*')
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('datetime', '>=', $start_date);
                        $query->whereDate('datetime', '<=', $end_date);
                    })
                    ->orderBy('id', 'desc')->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('gross_amount', function ($data) {
                    return $data->gross_amount;
                })
                ->addColumn('datetime', function ($data) {
                    return substr($data->datetime,0,10);
                })
                ->addColumn('status', function ($data) {
                    return $data->status;
                })
                ->addColumn('payment_method', function ($data) {
                    return ucwords($data->payment->payment_method);
                })
                ->addColumn('payment_status', function ($data) {
                    return ucwords($data->payment->payment_status);
                })
                ->addColumn('action', function ($data) {
                    $url_view = route('admin::viewOrderDetails', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delOrder', ['id' => $data->id])."'";
                 //   $delivery = route('admin::viewOrderDelivery', ['id' => $data->id]);
                  //  $edit = '<a href="' . $delivery . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary" title="Delivery Boy"><span class="glyphicon glyphicon-user"></span></a>&emsp;
                    $edit = '<a href="' . $url_view . '" class="btn btn-xs btn btn-info" title="View Details"><span class="fa fa-eye"></span></a>&emsp;'.
                    '<a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                     return $edit;
                })
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.order.index',compact('start_date','end_date'));
    }
    public function manageOutsideOrder(Request $request){
        if($request->get('start_date')!=''){
            $start_date=$request->get('start_date');
        }else{
            $start_date=date('Y-m-d');
        }
        if($request->get('end_date')!=''){
            $end_date=$request->get('end_date');
        }else{
            $end_date=date('Y-m-d');
        }
        if(request()->ajax()) {
            $start_date=$request->get('start_date');
            $end_date=$request->get('end_date');
            $shippingIds = ShippingModel::where("address","like","%Jammu%")->pluck("id")->toArray();
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::select('*')
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('datetime', '>=', $start_date);
                        $query->whereDate('datetime', '<=', $end_date);
                    })->whereNotIn("shipping_id",$shippingIds)
                    ->orderBy('id', 'desc')->get();
            }else{
                $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
                $order_ids = OrderDetailsModel::whereIn('supplier_id',$supplier_ids)->groupBy('order_id')->pluck('order_id')->toArray();
                $data = OrderModel::whereIn('id',$order_ids)->select('*')
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('datetime', '>=', $start_date);
                        $query->whereDate('datetime', '<=', $end_date);
                    })->whereNotIn("shipping_id",$shippingIds)
                    ->orderBy('id', 'desc')->get();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('gross_amount', function ($data) {
                    return $data->gross_amount;
                })
                ->addColumn('datetime', function ($data) {
                    return substr($data->datetime,0,10);
                })
                ->addColumn('status', function ($data) {
                    return $data->status;
                })
                ->addColumn('payment_method', function ($data) {
                    return ucwords($data->payment->payment_method);
                })
                ->addColumn('payment_status', function ($data) {
                    return ucwords($data->payment->payment_status);
                })
                ->addColumn('action', function ($data) {
                    $url_view = route('admin::viewOrderDetails', ['id' => $data->id]);
                    $url_tracking = route('admin::viewTrackingDetails', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delOrder', ['id' => $data->id])."'";
                    //   $delivery = route('admin::viewOrderDelivery', ['id' => $data->id]);
                    //  $edit = '<a href="' . $delivery . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary" title="Delivery Boy"><span class="glyphicon glyphicon-user"></span></a>&emsp;
                    $edit = '<a href="' . $url_view . '" class="btn btn-xs btn btn-info" title="View Details"><span class="fa fa-eye"></span></a>&emsp;';
                    $edit .= '<a href="' . $url_tracking . '" class="btn btn-xs btn btn-primary fancybox fancybox.iframe" title="Tracking Details">Tracking Id</a>&emsp;'.
                        '<a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                    return $edit;
                })
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.order.index_outside',compact('start_date','end_date'));
    }
    public function viewTrackingDetails($order_id){
        $order=OrderModel::find($order_id);
        return view('admin.order.tracking',compact('order'));
    }
    public function updateTrackingDetails(Request $request,$order_id){
        $msg = [
            'tracking_id.required' => 'Enter delivery tracking id.',
        ];
        $this->validate($request, [
            'tracking_id' => 'required',
        ], $msg);
        OrderModel::where("id",$order_id)->update([
            'tracking_id' => $request->get('tracking_id')
        ]);
        return redirect()->back()->with("success","Tracking id updated successfully.");
    }

    public function viewOrderDetails($order_id){
        $order_details=OrderDetailsModel::where('order_id',$order_id)->get();
        $order=OrderModel::find($order_id);
        $payment_details=$order->payment;
        $shipping_details=$order->shipping;
        return view('admin.order.view',compact('order_details','payment_details','shipping_details','order'));
    }

    public function viewOrderDelivery($order_id){
        $deliveries = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',4)->get();
        $order_delivery_id=OrderDetailsModel::where('id',$order_id)->value('delivery_id');
        return view('admin.order.order_delivery',compact('deliveries','order_delivery_id','order_id'));
    }

    public function updateOrderDelivery(Request $request){
        $msg = [
            'order_id.required' => 'Enter Your Order Id.',
            'delivery_id.required' => 'Select Delivery Boy.',
        ];
        $this->validate($request, [
            'order_id' => 'required',
            'delivery_id' => 'required',
        ], $msg);
        $order_id=$request->get('order_id');
        $delivery_id=$request->get('delivery_id');
        try {
            OrderDetailsModel::where('id', $order_id)->update([
                'delivery_id' => $delivery_id
            ]);
            return redirect()->back()->with('success', 'Delivery Boy Updated Successfully !!!');
        }catch (Exception $e){
            return redirect()->back()->with('error', 'Delivery Boy Not Updated !!!');
        }
    }
    public function delOrder($order_id){
        OrderDetailsModel::where("order_id",$order_id)->delete();
        OrderModel::where('id',$order_id)->delete();
        return redirect()->back()->with('success','Order Deleted Successfully !!!');
    }
}
