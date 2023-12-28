<?php

namespace App\Http\Controllers\Admin\Coupon;

use App\Model\CashbackSettingsModel;
use App\Model\PromocodesModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function CashBack()
    {
        if(request()->ajax()) {
            $data = CashbackSettingsModel::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editCashBack', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delCashBack', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:update_status('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:update_status('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;
                                        ';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.cashback.index');
    }

    public function addCashBack()
    {
        return view('admin.cashback.add');
    }

    public function saveCashBack(Request $request)
    {
        $msg = [
            'min_amount.required' => 'Enter Minimum Amount.',
            'cashback_percent.required' => 'Enter CashBack Percentage',
            'cashback_upto.required' => 'Enter CashBack Upto'
        ];
        $this->validate($request, [
            'min_amount' => 'required',
            'cashback_percent'=>'required',
            'cashback_upto'=>'required'
        ], $msg);
        $data = $request->except('_token');
        CashbackSettingsModel::create($data);
        return redirect()->back()->with('success','CashBack Added Successfully !!!');
    }

    public function editCashBack($id){
        $cashBackById = CashbackSettingsModel::where('id', $id)->first();
        return view('admin.cashback.edit', compact('cashBackById'));
    }

    public function updateCashBack(Request $request,$id)
    {
        $msg = [
            'min_amount.required' => 'Enter Minimum Amount.',
            'cashback_percent.required' => 'Enter CashBack Percentage',
            'cashback_upto.required' => 'Enter CashBack Upto'
        ];
        $this->validate($request, [
            'min_amount' => 'required',
            'cashback_percent'=>'required',
            'cashback_upto'=>'required'
        ], $msg);
        $data = $request->except('_token');
        CashbackSettingsModel::where('id',$id)->update($data);
        return redirect()->back()->with('success', 'CashBack Updated Successfully !!!');
    }

    public function updateCashBackStatus(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            CashbackSettingsModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="update_status('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            CashbackSettingsModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="update_status('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function deleteCashBack($id)
    {
        CashbackSettingsModel::where('id',$id)->delete();
        return redirect()->back()->with('success', 'CashBack Deleted Successfully !!!');
    }

    public function PromoCode()
    {
        if(request()->ajax()) {
            $data = PromocodesModel::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editPromoCode', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delPromoCode', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:update_status('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:update_status('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;
                                        ';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.promo_code.index');
    }

    public function addPromoCode()
    {
        return view('admin.promo_code.add');
    }

    public function savePromoCode(Request $request)
    {
        $msg = [
            'promo_code.required' => 'Enter Promo Code.',
            'min_amount.required' => 'Enter Minimum Amount.',
            'discount_percent.required' => 'Enter Discount Percentage.',
            'discount_upto.required' => 'Enter Discount Upto.',
            'for.required' => 'Select Promo Codes For.'
        ];
        $this->validate($request, [
            'promo_code' => 'required',
            'min_amount' => 'required',
            'discount_percent' => 'required',
            'discount_upto' => 'required',
            'for'=>'required'
        ], $msg);
        $data = $request->except('_token');
        PromocodesModel::create($data);
        return redirect()->back()->with('success','Promo Code Added Successfully !!!');
    }

    public function editPromoCode($id){
        $cashBackById = PromocodesModel::where('id', $id)->first();
        return view('admin.promo_code.edit', compact('cashBackById'));
    }

    public function updatePromoCode(Request $request,$id)
    {
        $msg = [
            'promo_code.required' => 'Enter Promo Code.',
            'min_amount.required' => 'Enter Minimum Amount.',
            'discount_percent.required' => 'Enter Discount Percentage.',
            'discount_upto.required' => 'Enter Discount Upto.',
            'for.required' => 'Select Promo Codes For.'
        ];
        $this->validate($request, [
            'promo_code' => 'required',
            'min_amount' => 'required',
            'discount_percent' => 'required',
            'discount_upto' => 'required',
            'for'=>'required'
        ], $msg);
        $data = $request->except('_token');
        PromocodesModel::where('id',$id)->update($data);
        return redirect()->back()->with('success', 'Promo Code Updated Successfully !!!');
    }

    public function updatePromoCodeStatus(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            PromocodesModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="update_status('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            PromocodesModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="update_status('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function deletePromoCode($id)
    {
        PromocodesModel::where('id',$id)->delete();
        return redirect()->back()->with('success', 'Promo Code Deleted Successfully !!!');
    }
}
