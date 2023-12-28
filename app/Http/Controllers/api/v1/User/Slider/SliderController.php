<?php

namespace App\Http\Controllers\api\v1\User\Slider;

use App\Model\ImageBannerModel;
use App\Model\SliderModel;
use App\Model\TextBannerModel;
use App\repo\Response;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SliderController extends Controller
{
    public function get_sliders(){
        try {
            $data=SliderModel::where('status','Active')->get();
            $msg = '';
            return Response::Success($data, $msg);

        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $msg);
        }
    }

    public function get_text_banner(){
        try {
            $data=TextBannerModel::where('status','Active')->get();
            $msg = '';
            return Response::Success($data, $msg);

        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $msg);
        }
    }

    public function get_image_banner(){
        try {
            $data=ImageBannerModel::where('status','Active')->where('type','Banner')->get();
            $msg = '';
            return Response::Success($data, $msg);

        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $msg);
        }
    }

    public function get_image_advertisement(){
        try {
            $data=ImageBannerModel::where('status','Active')->where('type','Advertisement')->get();
            $msg = '';
            return Response::Success($data, $msg);

        }catch (Exception $e) {
            $data = [];
            $msg = 'Server Error.';
            return Response::Error($data, $msg);
        }
    }
}
