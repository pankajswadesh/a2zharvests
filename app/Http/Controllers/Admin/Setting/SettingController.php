<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Model\DeliverySettingModel;
use App\Model\DeliverySlotsModel;
use App\Model\SettingModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = SettingModel::select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('key', function ($data) {
                    return $data->key;
                })
                ->addColumn('value', function ($data) {
                    return $data->value;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editSetting', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';

                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.setting.index');
    }

    public function editSetting($id){
        $settingById = SettingModel::find($id);
        return view('admin.setting.edit', compact('settingById'));
    }

    public function updateSetting(Request $request)
    {
        $msg = [
            'value.required' => 'Enter Value.',
        ];
        $this->validate($request, [
            'value' => 'required',
        ], $msg);

        $id = $request->get('id');
        $value = $request->get('value');
        try {
            SettingModel:: where('id', $id)->update([
                'value' => $value,
            ]);

            return redirect()->back()->with('success', 'Value Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Value Not Updated.');
        }
    }
    public function manageDelivery(){
        if(request()->ajax()) {
            $data = DeliverySettingModel::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('max_amount', function ($data) {
                    return $data->max_amount;
                })
                ->addColumn('delivery_charge', function ($data) {
                    return $data->delivery_charge;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editDeliverySetting', ['id' => $data->id]);
                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.delivery_setting.index');
    }
    public function editDelivery($id){
        $settingById = DeliverySettingModel::find($id);
        return view('admin.delivery_setting.edit', compact('settingById'));
    }
    public function updateDelivery(Request $request)
    {
        $msg = [
            'max_amount.required' => 'Enter Maximum Amount.',
            'delivery_charge.required' => 'Enter Delivery Charge.',
        ];
        $this->validate($request, [
            'max_amount' => 'required',
            'delivery_charge' => 'required',
        ], $msg);

        $id = $request->get('id');
        $max_amount = $request->get('max_amount');
        $delivery_charge = $request->get('delivery_charge');
        try {
            DeliverySettingModel:: where('id', $id)->update([
                'max_amount' => $max_amount,
                'delivery_charge' => $delivery_charge,
            ]);
            return redirect()->back()->with('success', 'Delivery Charge Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Delivery Charge Not Updated.');
        }
    }

    public function manageDeliverySlot(){
        if(request()->ajax()) {
            $data = DeliverySlotsModel::get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:status('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:status('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.delivery_slots.index');
    }
    public function deliverySlotStatus(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            DeliverySlotsModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="status('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            DeliverySlotsModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="status('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
    }
}
