<?php

namespace App\Http\Controllers\Admin\Supplier;

use App\Model\OrderDetailsModel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupplierEarningController extends Controller
{
    public function index(Request $request)
    {
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
        if($request->get('supplier_id')!=''){
            $supplier_id=$request->get('supplier_id');
        }else{
            $supplier_id='';
        }
        $suppliers = User::join('role_user', 'role_user.user_id', '=', 'users.id')->select('users.*')->where('users.status', '<>', 'Deleted')->where('users.id', '<>', 1)->where('role_user.role_id', 3)->where('plan_type','Commision')->get();
        if(request()->ajax()) {
            if(\Auth::user()->hasRole('supplier')){
                $data=OrderDetailsModel::where('supplier_id',\Auth::user()->id)->where('status','Delivered') ->where(function ($query) use ($start_date, $end_date) {
                    $query->whereDate('created_at', '>=', $start_date);
                    $query->whereDate('created_at', '<=', $end_date);
                })->get();
            }else{
                $data=OrderDetailsModel::where('status','Delivered')->where(function ($query) use ($start_date, $end_date) {
                    $query->whereDate('created_at', '>=', $start_date);
                    $query->whereDate('created_at', '<=', $end_date);
                });
                if($supplier_id!=''){
                    $data->where('supplier_id',$supplier_id);
                }
                $data=$data->get();
            }

            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('order_id', function ($data) {
                    return $data->order->order_id;
                })
                ->addColumn('supplier_name', function ($data) {
                    return $data->supplier->user_name;
                })
                ->addColumn('total_price', function ($data) {
                    return $data->gross_price;
                })
                ->addColumn('total_commision', function ($data) {
                    return  (($data->gross_price * $data->supplier->plan_value)/100);
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.supplier.earning',compact('start_date','end_date','supplier_id','suppliers'));

    }
}
