<?php

namespace App\Http\Controllers\Admin\Tax;

use App\Model\TaxModel;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = TaxModel::select('*')->where('status','<>','Deleted');
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('tax_name', function ($data) {
                    return $data->tax_name;
                })
                ->addColumn('is_inclusive', function ($data) {
                    return $data->is_inclusive;
                })
                ->addColumn('tax_value', function ($data) {
                    return $data->tax_value;
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editTax', ['id' => $data->id]);
                    $url_tax_value = route('admin::manageTaxValue', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delTax', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_tax('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_tax('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                    $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                    $edit.='<a href="' . $url_tax_value . '" class="btn btn-xs btn btn-primary">Tax Value</a>';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.tax.index');
    }

    public function addTax(){
        return view('admin.tax.add');
    }
    public function saveTax(Request $request)
    {
        $msg = [
            'tax_name.required' => 'Enter Tax Name.',
            'is_inclusive.required' => 'Choose Is Inclusive.',
        ];
        $this->validate($request, [
            'tax_name' => 'required',
            'is_inclusive' => 'required',
        ], $msg);
        $tax_name = $request->get('tax_name');
        $is_inclusive = $request->get('is_inclusive');
        try {
            TaxModel::create([
                'tax_name'=>$tax_name,
                'is_inclusive'=>$is_inclusive,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Tax Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Tax Not addded.');
        }
    }
    public function editTax($id){
        $taxById = TaxModel::find($id);
        return view('admin.tax.edit', compact('taxById'));
    }

    public function updateTax(Request $request)
    {
        $msg = [
            'tax_name.required' => 'Enter Tax Name.',
            'is_inclusive.required' => 'Choose Is Inclusive.',
        ];
        $this->validate($request, [
            'tax_name' => 'required',
            'is_inclusive' => 'required',
        ], $msg);

        $id = $request->get('id');
        $tax_name = $request->get('tax_name');
        $is_inclusive = $request->get('is_inclusive');
        try {
            TaxModel:: where('id', $id)->update([
                'tax_name' => $tax_name,
                'is_inclusive' => $is_inclusive,
            ]);

            return redirect()->back()->with('success', 'Tax Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Tax Not Updated.');
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
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_tax('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            TaxModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_tax('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }

    public function delTax($id){
        TaxModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Tax Deleted Successfully !!!');
    }
}
