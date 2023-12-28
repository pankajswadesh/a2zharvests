<?php

namespace App\Http\Controllers\Admin\Seller;

use App\Model\BecomeSellerModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = BecomeSellerModel::select('*')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_delete = "'".route('admin::delBecomeSeller', ['id' => $data->id])."'";
                    $edit = '<a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.become_seller.index');
    }
    public function delete($id){
        BecomeSellerModel::where('id',$id)->delete();
        return redirect()->back()->with('success','Data deleted successfully.');
    }
}
