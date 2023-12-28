<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Model\BecomeSellerModel;
use App\Model\ContactUsMessagesModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['AdminMiddleWare']);
    }

    public function index()
    {
        if(request()->ajax()) {
            $data = ContactUsMessagesModel::select('*')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $url_delete = "'".route('admin::delContactMessage', ['id' => $data->id])."'";
                    $edit = '<a data-toggle="modal" data-target="#confirmDelete" class="btn btn-xs btn btn-danger" onclick="getDeleteRoute(' . $url_delete . ')"><span class="glyphicon glyphicon-trash"></span></a>&emsp;';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('admin.contact_messages.index');
    }
    public function delete($id){
        ContactUsMessagesModel::where('id',$id)->delete();
        return redirect()->back()->with('success','Data deleted successfully.');
    }
}
