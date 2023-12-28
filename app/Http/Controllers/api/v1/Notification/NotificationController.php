<?php

namespace App\Http\Controllers\api\v1\Notification;

use App\Model\OrderModel;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public static function sendNotification($type,$notify_ids,$msg,$order_id=null,$user_type){
        $device_ids=User::whereIn('id',$notify_ids)->pluck('device_id')->toArray();
        $body=$msg;
        $content = array(
            "en" => $body
        );
        $order = OrderModel::find($order_id);
        $title = "Order Id - ".$order->order_id;
        if($user_type=='delivery'){
            $app_id=config('custom.delivery_app_id');
        }elseif($user_type=='supplier'){
            $app_id=config('custom.supplier_app_id');
        }else{
            $app_id=config('custom.customer_app_id');
        }
        $headings = array('en'=>ucwords($title));
        $fields = array(
            'app_id' => $app_id,
            'include_player_ids' => $device_ids,
            'headings' => $headings,
            'contents' => $content,
            'small_icon'=>'ic_stat_onesignal_default',
            'large_icon' => "https://a2zharvests.com/frontendtheme/images/logo.png",
            /* 'existing_android_channel_id '=>"3274cd5e-c028-418e-b385-3728fc63370e", */
        );
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;accept: application/json; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
