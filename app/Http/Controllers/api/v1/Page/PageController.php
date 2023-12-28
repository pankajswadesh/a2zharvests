<?php

namespace App\Http\Controllers\api\v1\Page;

use App\Model\AboutUsModel;
use App\Model\ContactUsModel;
use App\Model\SettingModel;
use App\repo\datavalue;
use App\repo\Response;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    public function contactUsPage(){
        try {
            $contact_page=ContactUsModel::first();
            $data=$contact_page;
            $msg=  '';
            return Response::Success($data,$msg);
        }catch (Exception $e){
            $data=[];
            $msg=  'Contact Us Page Not Found.';
            return Response::Error($data,$msg);
        }
    }

    public function aboutUsPage(){
        try {
            $about_page=AboutUsModel::first();
            $data=$about_page;
            $msg=  '';
            return Response::Success($data,$msg);
        }catch (Exception $e){
            $data=[];
            $msg=  'About Us Page Not Found.';
            return Response::Error($data,$msg);
        }
    }
    public function getDeliveryVersion(){
        try{
            $data = SettingModel::where('key',"Delivery App Version")->value("value");
            $msg = 'Version Fetched Successfully.';
            return Response::Success($data, $msg);
        }catch (Exception $e){
            $data = [];
            $msg = 'Version Not Fetched Successfully.';
            return Response::Error($data, $msg);
        }
    }
    public function getCustomerVersion(){
        try{
            $data = SettingModel::where('key',"Customer App Version")->value("value");
            $msg = 'Version Fetched Successfully.';
            return Response::Success($data, $msg);
        }catch (Exception $e){
            $data = [];
            $msg = 'Version Not Fetched Successfully.';
            return Response::Error($data, $msg);
        }
    }
    public function getSupplierVersion(){
        try{
            $data = SettingModel::where('key',"Supplier App Version")->value("value");
            $msg = 'Version Fetched Successfully.';
            return Response::Success($data, $msg);
        }catch (Exception $e){
            $data = [];
            $msg = 'Version Not Fetched Successfully.';
            return Response::Error($data, $msg);
        }
    }
}
