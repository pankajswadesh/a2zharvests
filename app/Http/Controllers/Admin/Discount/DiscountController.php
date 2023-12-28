<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Model\DiscountModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = DiscountModel::select('*')->where('status','<>','Deleted')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('discount_name', function ($data) {
                    return ucfirst($data->discount_name);
                })
                ->addColumn('action', function ($data) {
                    $url_update = route('admin::editDiscount', ['id' => $data->id]);
                    $url_delete = "'".route('admin::delDiscount', ['id' => $data->id])."'";
                    $edit='<span id="status'.$data->id.'">';
                    if($data->status=='Active'){
                        $edit.='<a href="javascript:active_inactive_discount('.$data->id.','.$data->status.');" class="btn btn-xs btn-success"><span class="glyphicon glyphicon-ok-circle"></span> </a>&emsp;';
                    } else{
                        $edit.='<a href="javascript:active_inactive_discount('.$data->id.','.$data->status.');" class="btn btn-xs btn-warning" ><span class="glyphicon glyphicon-ban-circle"></span> </a>&emsp;';
                    }
                    $edit.='</span>';
                   /* $edit .= '<a href="' . $url_update . '" class="fancybox fancybox.iframe btn btn-xs btn btn-primary"><span class="glyphicon glyphicon-edit"></span></a>&emsp;
                                        <a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                  */
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.discount.index');
    }

    public function addDiscount()
    {
        return view('admin.discount.add');
    }

    public function saveDiscount(Request $request)
    {
        $msg = [
            'discount_name.required' => 'Enter Discount Name.',
        ];
        $this->validate($request, [
            'discount_name' => 'required',
        ], $msg);
        $brand_name = $request->get('discount_name');
        $url = str_slug($request->get('discount_name'));
        try {
            DiscountModel::create([
                'discount_name'=>$brand_name,
                'url'=>$url,
                'status'=>'Active',
            ]);
            return redirect()->back()->with('success','Discount Added Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Discount Not addded.');
        }
    }

    public function editDiscount($id)
    {
        $discountById = DiscountModel::find($id);
        return view('admin.discount.edit', compact('discountById'));
    }

    public function updateDiscount(Request $request)
    {
        $msg = [
            'discount_name.required' => 'Enter Discount Name.',
        ];
        $this->validate($request, [
            'discount_name' => 'required',
        ], $msg);

        $id = $request->get('id');
        $discount_nmae = $request->get('discount_name');
        try {
            DiscountModel:: where('id', $id)->update([
                'discount_name' => $discount_nmae,
            ]);

            return redirect()->back()->with('success', 'Discount Updated Successfully !!!');
        }catch(Exception $e) {
            return redirect()->back()->with('error','Discount Not Updated.');
        }
    }

    public function delDiscount($id)
    {
        DiscountModel::where('id',$id)->update([
            'status'=>'Deleted'
        ]);
        return redirect()->back()->with('success','Discount Deleted Successfully !!!');
    }

    public function active_inactive_discount(Request $request){
        $id = $request->get('id');
        $status = $request->get('status');
        if($status=='Active'){
            DiscountModel::where('id',$id)->update([
                'status' => 'Inactive',
            ]);
            $st='Inactive';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-warning" onclick="active_inactive_discount('.$id.','.$st.')"><span class="glyphicon glyphicon-ok-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }
        else{
            DiscountModel::where('id',$id)->update([
                'status' => 'Active',
            ]);
            $st='Active';
            $html='<a href="javascript:void(0);" class="btn btn-xs btn btn-success" onclick="active_inactive_discount('.$id.','.$st.')"><span class="glyphicon glyphicon-ban-circle"></span></a>&emsp;';
            return json_encode(array('id'=>$id,'html'=>$html));
        }

    }


}
