<?php

namespace App\Http\Controllers\Admin\Report;

use App\Model\DiscountModel;
use App\Model\OrderDetailsModel;
use App\Model\OrderModel;
use App\Model\ProductModel;
use App\Model\SupplierProductModel;
use App\Model\TaxModel;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function manageSupplierProduct(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $product_count = ProductModel::rightJoin('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                ->where('supplier_products.status', '<>', 'Deleted')
                ->orderBy('supplier_products.id', 'desc')
                ->select('products.*', 'supplier_products.quantity', 'supplier_products.price', 'supplier_products.discount_value', 'supplier_products.discount_id', 'supplier_products.status as st');
            if ($supplier_id != '') {
                $product_count = $product_count->where('supplier_products.user_id', $supplier_id);
            }
            $product_count = $product_count->count();
        }
        else{
                $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
                $suppliers = User::whereIn('id',$supplier_ids)->get();
                $supplier_id=$request->get('supplier_id');
                $product_count = ProductModel::rightJoin('supplier_products','supplier_products.product_id','=','products.id')
                    ->where('supplier_products.status','<>','Deleted')
                    ->orderBy('supplier_products.id','desc')
                    ->select('products.*','supplier_products.quantity','supplier_products.price','supplier_products.discount_value','supplier_products.discount_id','supplier_products.status as st')
                ;
                if($supplier_id !=''){
                    $product_count=$product_count->where('supplier_products.user_id',$supplier_id);
                }else{
                    $product_count=$product_count->whereIn('supplier_products.user_id',$supplier_ids);
                }
                $product_count=$product_count->count();
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = ProductModel::rightJoin('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                    ->where('supplier_products.status', '<>', 'Deleted')
                    ->orderBy('supplier_products.id', 'desc')
                    ->select('products.*', 'supplier_products.quantity', 'supplier_products.price', 'supplier_products.discount_value', 'supplier_products.discount_id', 'supplier_products.status as st');
                if ($supplier_id != '') {
                    $data = $data->where('supplier_products.user_id', $supplier_id);
                }
            }else{
                $data = ProductModel::rightJoin('supplier_products', 'supplier_products.product_id', '=', 'products.id')
                    ->where('supplier_products.status', '<>', 'Deleted')
                    ->orderBy('supplier_products.id', 'desc')
                    ->select('products.*', 'supplier_products.quantity', 'supplier_products.price', 'supplier_products.discount_value', 'supplier_products.discount_id', 'supplier_products.status as st');
                if ($supplier_id != '') {
                    $data = $data->where('supplier_products.user_id', $supplier_id);
                }else{
                    $data = $data->whereIn('supplier_products.user_id',$supplier_ids);
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('category_name', function ($data) {
                    return ucfirst($data->category->category_name);
                })->addColumn('sub_category_name', function ($data) {
                    return ucfirst($data->sub_category->category_name);
                })
//                ->addColumn('brand_name', function ($data) {
//                    return ucfirst($data->brand->brand_name);
//                })
                ->addColumn('product_name', function ($data) {
                    return ucfirst($data->product_name);
                })
                ->addColumn('product_quantity', function ($data) {
                    return $data->quantity;
                })
                ->addColumn('product_price', function ($data) {
                   return $data->price;
                })
                ->addColumn('gross_price', function ($data) {
                    $discount=DiscountModel::find($data->discount_id);
                    $tax_details = TaxModel::find($data->tax_id);
                    if ($discount['discount_name'] == '%') {
                        $product_discount = (($data->price * $data->discount_value) / 100);
                        $product_discount_price = $data->price - $product_discount;
                        if($tax_details->is_inclusive=='No'){
                            $product_discount_with_tax=$product_discount_price + (($product_discount_price *$tax_details->tax_value)/100);
                        }else{
                            $product_discount_with_tax=$product_discount_price;
                        }

                    }
                    else if ($discount['discount_name'] == 'rs') {
                        $product_discount_price = $data->price - $data->discount_value;
                        if($tax_details->is_inclusive=='No'){
                        $product_discount_with_tax=$product_discount_price + (($product_discount_price *$tax_details->tax_value)/100);
                        }else{
                            $product_discount_with_tax=$product_discount_price;
                        }
                 } else {
                        $product_discount_price = $data->price;
                        if($tax_details->is_inclusive=='No'){
                        $product_discount_with_tax=$product_discount_price + (($product_discount_price *$tax_details->tax_value)/100);
                        }else{
                            $product_discount_with_tax=$product_discount_price;
                        }
                    }
                 return $product_discount_with_tax;
                })
                ->addColumn('discount_type', function ($data) {
                   $discount=DiscountModel::find($data->discount_id);
                    return $discount->discount_name;
                })
                ->addColumn('discount', function ($data) {
                    return $data->discount_value;
                })
                ->addColumn('unit_name', function ($data) {
                    return ucfirst($data->unit->unit_name);
                })

                ->addColumn('tax_name', function ($data) {
                    if($data->tax->is_inclusive=='No'){
                        $tax_type='Exclusive';
                    }else{
                        $tax_type='Inclusive';
                    }
                    return ucfirst($data->tax->tax_name)."(".$tax_type.")";
                })
                ->addColumn('status', function ($data) {
                    return $data->st;
                })
                ->rawColumns(['products.*'])
                ->toJson();
        }
        return view('admin.report.index',compact('suppliers','supplier_id','product_count'));
    }

    public function manageSupplierSale(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::join('order_details', 'order_details.order_id', '=', 'orders.id')
                ->whereNotIn('orders.status', ['Cancel', 'Refunded','Rejected'])
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order =  $total_order->where('order_details.supplier_id', $supplier_id);
            }
            $total_order = $total_order->groupBy('order_details.order_id')->sum('orders.gross_amount');
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->whereNotIn('orders.status', ['Cancel', 'Refunded','Rejected'])
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }else{
                $total_order->whereIn('order_details.supplier_id',$supplier_ids);
            }
            $total_order = $total_order->groupBy('order_details.order_id')->sum('orders.gross_amount');
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->whereNotIn('orders.status', ['Cancel', 'Refunded','Rejected'])->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }
            }else{
                $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->whereNotIn('orders.status', ['Cancel', 'Refunded','Rejected']);
                $data = $data->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }else{
                    $data = $data->whereIn('order_details.supplier_id', $supplier_ids);
                }
            }
            $data = $data->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('total_amount', function ($data) {
                    return $data->total_amount;
                })
                ->addColumn('total_discount', function ($data) {
                    return $data->total_discount;
                })
                ->addColumn('sale_price', function ($data) {
                    return number_format(($data->total_amount - $data->total_discount),2);
                })
                ->addColumn('total_tax', function ($data) {
                    return $data->total_tax;
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
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.report.supplier_sale',compact('suppliers','supplier_id','total_order'));
    }

    public function managePendingOrder(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Processing')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Processing')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }else{
                $total_order->whereIn('order_details.supplier_id', $supplier_ids);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Processing')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }
            }else{
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Processing')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }else{
                    $data = $data->whereIn('order_details.supplier_id', $supplier_ids);
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('total_amount', function ($data) {
                    return $data->total_amount;
                })
                ->addColumn('total_discount', function ($data) {
                    return $data->total_discount;
                })
                ->addColumn('sale_price', function ($data) {
                    return number_format(($data->total_amount - $data->total_discount),2);
                })
                ->addColumn('total_tax', function ($data) {
                    return $data->total_tax;
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
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.report.pending_order',compact('suppliers','supplier_id','total_order'));
    }

    public function manageCancelOrder(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Cancel')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Cancel')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }else{
                $total_order->whereIn('order_details.supplier_id', $supplier_ids);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Cancel')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }
            }else{
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Cancel')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }else{
                    $data = $data->whereIn('order_details.supplier_id', $supplier_ids);
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('total_amount', function ($data) {
                    return $data->total_amount;
                })
                ->addColumn('total_discount', function ($data) {
                    return $data->total_discount;
                })
                ->addColumn('sale_price', function ($data) {
                    return number_format(($data->total_amount - $data->total_discount),2);
                })
                ->addColumn('total_tax', function ($data) {
                    return $data->total_tax;
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
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.report.cancel_order',compact('suppliers','supplier_id','total_order'));
    }

    public function manageDeliveryOrder(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Delivered')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->select('users.*')->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('orders.status', 'Delivered')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }else{
                $total_order->whereIn('order_details.supplier_id', $supplier_ids);
            }
            $total_order = $total_order->sum('orders.gross_amount');
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Delivered')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }
            }else{
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('orders.status', 'Delivered')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*');
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }else{
                    $data = $data->whereIn('order_details.supplier_id', $supplier_ids);
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('total_amount', function ($data) {
                    return $data->total_amount;
                })
                ->addColumn('total_discount', function ($data) {
                    return $data->total_discount;
                })
                ->addColumn('sale_price', function ($data) {
                    return number_format(($data->total_amount - $data->total_discount),2);
                })
                ->addColumn('total_tax', function ($data) {
                    return $data->total_tax;
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
                ->addColumn('total', function ($data) {
                    $total=$data->gross_amount;
                    return $total;
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.report.delivery_order',compact('suppliers','supplier_id','total_order'));
    }

    public function manageRejectOrder(Request $request){
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.status', 'Rejected')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }
            $total_order = $total_order->sum('order_details.gross_price');
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->select('users.*')->get();
            $supplier_id = $request->get('supplier_id');
            $total_order = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                ->where('order_details.status', 'Rejected')
                ->orderBy('orders.id', 'desc');
            if ($supplier_id != '') {
                $total_order->where('order_details.supplier_id', $supplier_id);
            }else{
                $total_order->whereIn('order_details.supplier_id', $supplier_ids);
            }
            $total_order = $total_order->sum('order_details.gross_price');
        }
        if(request()->ajax()) {
            if(Auth::user()->hasRole('admin')) {
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('order_details.status', 'Rejected')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*', DB::raw('sum(order_details.gross_price) as price'));
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }
            }else{
                $data = OrderModel::rightJoin('order_details', 'order_details.order_id', '=', 'orders.id')
                    ->where('order_details.status', 'Rejected')
                    ->orderBy('orders.id', 'desc')
                    ->groupBy('order_details.order_id')
                    ->select('orders.*', DB::raw('sum(order_details.gross_price) as price'));
                if ($supplier_id != '') {
                    $data = $data->where('order_details.supplier_id', $supplier_id);
                }else{
                    $data = $data->whereIn('order_details.supplier_id', $supplier_ids);
                }
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })
                ->addColumn('user_name', function ($data) {
                    $user_name=User::where('id',$data->user_id)->value('user_name');
                    return $user_name;
                })
                ->addColumn('amount', function ($data) {
                    return $data->price;
                })
                ->rawColumns(['orders.*'])
                ->toJson();
        }
        return view('admin.report.reject_order',compact('suppliers','supplier_id','total_order'));
    }

    public function manageDayEndReport(Request $request){
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
        $supplier_id=$request->get('supplier_id');
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',3)->get();
            if($supplier_id=='') {
                $delivered_supplier_id = OrderDetailsModel::where('status', 'delivered')
                    ->distinct()
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                        $query->whereDate('created_at', '<=', $end_date);
                    })
                    ->pluck('supplier_id')
                    ->toArray();
            }else{
                $delivered_supplier = OrderDetailsModel::where('status', 'delivered')->where('supplier_id',$supplier_id)
                    ->where(function ($query) use ($start_date,$end_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                        $query->whereDate('created_at', '<=',$end_date);
                    })
                    ->count();
                if($delivered_supplier>0){
                    $delivered_supplier_id=['0'=>$supplier_id];
                }else{
                    $delivered_supplier_id=[];
                }
            }
            $data=OrderDetailsModel::select(DB::raw('DATE(orders.created_at) as date'),DB::raw('sum(order_details.gross_price) as total'),'order_details.supplier_id','payments.payment_method')
                ->join('orders','orders.id','=','order_details.order_id')
                ->join('payments','payments.id','=','orders.payment_id')
                ->where(function ($query) use ($start_date,$end_date) {
                    $query->whereDate('orders.datetime', '>=', $start_date);
                    $query->whereDate('orders.datetime', '<=',$end_date);
                })
                ->where('orders.status','Delivered')
                ->where('order_details.status','delivered')
                ->where('payments.payment_status','Completed')
                ->groupBy('payments.payment_method')
                ->groupBy('order_details.supplier_id');
            $data=$data->get();
            $supplier_data=[];
            foreach ($data as $row){
                $supplier_data[$row['supplier_id']][$row['payment_method']]=$row['total'];
            }
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->select('users.*')->get();
            if($supplier_id=='') {
                $delivered_supplier_id = OrderDetailsModel::whereIn('supplier_id',$supplier_ids)->where('status', 'delivered')
                    ->distinct()
                    ->where(function ($query) use ($start_date, $end_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                        $query->whereDate('created_at', '<=', $end_date);
                    })
                    ->pluck('supplier_id')
                    ->toArray();
            }else{
                $delivered_supplier = OrderDetailsModel::where('status','delivered')->where('supplier_id',$supplier_id)
                    ->where(function ($query) use ($start_date,$end_date) {
                        $query->whereDate('created_at', '>=', $start_date);
                        $query->whereDate('created_at', '<=',$end_date);
                    })
                    ->count();
                if($delivered_supplier>0){
                    $delivered_supplier_id=['0'=>$supplier_id];
                }else{
                    $delivered_supplier_id=[];
                }
            }
            $data=OrderDetailsModel::select(DB::raw('DATE(orders.created_at) as date'),DB::raw('sum(order_details.gross_price) as total'),'order_details.supplier_id','payments.payment_method')
                ->join('orders','orders.id','=','order_details.order_id')
                ->join('payments','payments.id','=','orders.payment_id')
                ->where(function ($query) use ($start_date,$end_date) {
                    $query->whereDate('orders.datetime', '>=', $start_date);
                    $query->whereDate('orders.datetime', '<=',$end_date);
                })
                ->where('orders.status','Delivered')
                ->where('order_details.status','delivered')
                ->where('payments.payment_status','Completed')
                ->groupBy('payments.payment_method')
                ->groupBy('order_details.supplier_id');
            $data=$data->get();
            $supplier_data=[];
            foreach ($data as $row){
                $supplier_data[$row['supplier_id']][$row['payment_method']]=$row['total'];
            }
        }
        if($supplier_id!=""){
            $supplier_details = User::where("id",$supplier_id)->first();
        }else{
            $supplier_details = [];
        }

        return view('admin.report.day_end_report',compact('suppliers','supplier_details','supplier_id','start_date','end_date','supplier_data','delivered_supplier_id'));
    }

    public function manageDeliveryBoyReport(Request $request){
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
        if(Auth::user()->hasRole('admin')) {
            $suppliers = User::join('role_user','role_user.user_id','=','users.id')->select('users.*')->where('users.status','<>','Deleted')->where('users.id','<>',1)->where('role_user.role_id',3)->get();
        }else{
            $supplier_ids = User::where('parent_id',Auth::user()->id)->where('status','<>','Deleted')->whereHas('roles',function ($query){ $query->where('roles.id',3); })->pluck('id')->toArray();
            $suppliers = User::whereIn('id',$supplier_ids)->select('users.*')->get();
        }
        $supplier_id=$request->get('supplier_id');
       if(request()->ajax()) {
            $data=OrderModel::select('orders.*',DB::raw('DATE(orders.created_at) as date'),DB::raw('sum(order_details.gross_price) as total'))
                ->join('order_details','order_details.order_id','=','orders.id')
                ->join('payments','payments.id','=','orders.payment_id')
                ->where(function ($query) use ($start_date,$end_date) {
                    $query->whereDate('datetime', '>=', $start_date);
                    $query->whereDate('datetime', '<=',$end_date);
                })
                ->where('orders.status','Delivered')
                ->where('order_details.status','Delivered')
                ->where('payments.payment_status','Completed')
                ->where('payments.payment_method','cod');
           if(Auth::user()->hasRole('manager')) {
               $supplier_ids = User::where('parent_id', Auth::user()->id)->where('status', '<>', 'Deleted')->whereHas('roles', function ($query) {
                   $query->where('roles.id', 3);
               })->pluck('id')->toArray();
               if($supplier_id !=''){
                   $data=$data->where('order_details.supplier_id',$supplier_id);
               }else{
                   $data=$data->whereIn('order_details.supplier_id',$supplier_ids);
               }
               $data=$data->groupBy('date')->get();
           }else{
               if($supplier_id !=''){
                   $data=$data->where('order_details.supplier_id',$supplier_id);
               }
               $data=$data->groupBy('date')->get();
           }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('date', function ($data) {
                    return $data->date;
                })->addColumn('cash', function ($data) {
                    return $data->total;
                })
                ->addColumn('action', function ($data) {
                    $url_view = route('admin::DeliveryBoyReportDetails', ['id' => $data->date]);
                    $edit = '<a href="' . $url_view . '" class="btn btn-xs btn btn-info" title="View Details"><span class="fa fa-eye"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.report.delivery_boy_report',compact('suppliers','supplier_id','start_date','end_date'));
    }

    public function DeliveryBoyReportDetails(Request $request,$date){
        if(Auth::user()->hasRole('admin')) {
            $supplier_ids = OrderDetailsModel::whereDate('created_at', $date)
                ->where('status', 'Delivered')
                ->distinct()
                ->pluck('order_details.supplier_id')
                ->toArray();
        }else{
            $supplier_idsset = User::where('parent_id', Auth::user()->id)->where('status', '<>', 'Deleted')->whereHas('roles', function ($query) {
                $query->where('roles.id', 3);
            })->pluck('id')->toArray();
            $supplier_ids = OrderDetailsModel::whereDate('created_at', $date)
                ->where('status', 'Delivered')
                ->whereIn('order_details.supplier_id',$supplier_idsset)
                ->distinct()
                ->pluck('order_details.supplier_id')
                ->toArray();
        }
        $suppliers=User::whereIn('id',$supplier_ids)->get();
        $supplier_id=$request->get('supplier_id');
        if(request()->ajax()) {
            $data = OrderModel::select('orders.*',DB::raw('DATE(orders.created_at) as date')
                ,DB::raw('sum(order_details.price * order_details.qty) as price')
                ,DB::raw('sum(order_details.discount_value) as discountt')
                ,DB::raw('sum(order_details.tax_value) as taxx')
                ,DB::raw('sum(order_details.gross_price) as total')
            )
                ->rightJoin('order_details','order_details.order_id','=','orders.id')
                ->join('payments','payments.id','=','orders.payment_id')
                ->where('orders.status','Delivered')
                ->where('order_details.status','Delivered')
                ->whereDate('orders.created_at',$date)
                ->orderBy('orders.id','desc');
                if($supplier_id !=''){
                    $data=$data->where('order_details.supplier_id',$supplier_id);
                }
            $data=$data->groupBy('order_details.order_id')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order_id;
                })->addColumn('transaction_id', function ($data) {
                    return $data->transaction_id;
                })
                ->addColumn('total_amount', function ($data) {
                    return $data->price;
                })
                ->addColumn('total_discount', function ($data) {
                    return $data->discountt;
                })
                ->addColumn('sale_price', function ($data) {
                    return number_format(($data->price - $data->discountt),2);
                })
//                ->addColumn('total_tax', function ($data) {
//                    return $data->taxx;
//                })
                ->addColumn('gross_amount', function ($data) {
                    return $data->total;
                })
                ->addColumn('datetime', function ($data) {
                    return substr($data->datetime,0,10);
                })
                ->addColumn('status', function ($data) {
                    return $data->status;
                })

                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.report.delivery_boy_report_details',compact('date','suppliers','supplier_id'));
    }
}
