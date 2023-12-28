<?php

namespace App\Http\Controllers\Admin\Tax;

use App\Model\TaxModel;
use App\Model\TaxValueModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TaxValueController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index($id)
    {
        if(request()->ajax()) {
            $data = TaxValueModel::select('*')->where('tax_id',$id)->where('status','<>','Deleted');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tax_id', function ($data) {
                    return $data->tax->tax_name;
                })
                ->addColumn('ledger_name', function ($data) {
                    return $data->ledger_name;
                })
                ->addColumn('value', function ($data) {
                    return $data->value;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editTaxValue', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delTaxValue', ['id' => $data->id,'tax_id'=>$data->tax_id])."'";

                    $edit = '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';


                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.tax.value.index',compact('id'));
    }

    public function addTaxValue($tax_id){
        $taxs=TaxModel::where('status','Active')->get();
        return view('admin.tax.value.add',compact('taxs','tax_id'));
    }
    public function saveTaxValue(Request $request)
    {
        $msg = [
            'ledger_name.required' => 'Enter Ledger Name.',
            'value.required' => 'Enter Value.',
        ];
        $this->validate($request, [
            'ledger_name' => 'required',
            'value' => 'required|numeric|min:1',
        ], $msg);
        $tax_id = $request->get('tax_id');
        $ledger_name = $request->get('ledger_name');
        $value = $request->get('value');
        try {
            TaxValueModel::create([
                'tax_id'=>$tax_id,
                'ledger_name'=>$ledger_name,
                'value'=>$value,
                'status'=>'Active',
            ]);
            $total_value=TaxValueModel::where('tax_id',$tax_id)->sum('value');
            TaxModel::where('id',$tax_id)->update([
                'tax_value'=>$total_value
            ]);
            return redirect()->back()->with('success','Tax Value Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Tax Value Not addded.');
        }
    }
    public function editTaxValue($id){
        $taxValueById = TaxValueModel::find($id);
        $taxs=TaxModel::where('status','Active')->get();
        return view('admin.tax.value.edit', compact('taxs','taxValueById'));
    }

    public function updateTaxValue(Request $request)
    {
        $msg = [
            'ledger_name.required' => 'Enter Ledger Name.',
            'value.required' => 'Enter Value.',
        ];
        $this->validate($request, [
            'ledger_name' => 'required',
            'value' => 'required|numeric|min:1',
        ], $msg);

        $id = $request->get('id');
        $tax_id = $request->get('tax_id');
        $ledger_name = $request->get('ledger_name');
        $value = $request->get('value');
        try {
            TaxValueModel:: where('id', $id)->update([
                'ledger_name'=>$ledger_name,
                'value'=>$value,
            ]);

            $total_value=TaxValueModel::where('tax_id',$tax_id)->sum('value');
            TaxModel::where('id',$tax_id)->update([
                'tax_value'=>$total_value
            ]);

            return redirect()->back()->with('success', 'Tax Value Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Tax Value Not Updated.');
        }
    }

    public function active_inactive_tax(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            TaxModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_tax_value('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            TaxModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_tax_value('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delTaxValue($id,$tax_id){
        TaxValueModel::where('id',$id)->update([
            'value'=>0,
            'status'=>'Deleted'
        ]);
        $total_value=TaxValueModel::where('tax_id',$tax_id)->sum('value');
        TaxModel::where('id',$tax_id)->update([
            'tax_value'=>$total_value
        ]);
        return redirect()->back()->with('success','Tax Value Deleted Successfully !!!');
    }
}
