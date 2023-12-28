<?php

namespace App\Http\Controllers\Admin\Notification;

use App\repo\datavalue;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{

    public function notification(){
        return view('admin.notification.index');
    }

    public function sendNotification(Request $request)
    {
        $msg = [
            'notification_heading.required' => 'Enter Notification Heading.',
            'notification_description.required' => 'Enter Notification Description.',
        ];
        $this->validate($request, [
            'notification_heading' => 'required',
            'notification_description'=>'required',
        ], $msg);
        $notification_heading = ucwords($request->get('notification_heading'));
        $notification_description = $request->get('notification_description');
        try {
            $device_ids=User::whereHas('roles', function($q){
                $q->where('id', '=', '2');
            })->where('device_id','!=','')->pluck('device_id')->toArray();

            $body=$notification_description;
            $content = array(
                "en" => $body
            );
            $headings = array('en'=>ucwords($notification_heading));

            $app_id=config('custom.customer_app_id');
            $fields = array(
                'app_id' => $app_id,
                'include_player_ids' => $device_ids,
                'headings' => $headings,
                'contents' => $content,
                'small_icon'=>'ic_stat_onesignal_default',
                'large_icon' => "https://a2zharvests.com/frontendtheme/images/logo.png",
                /* 'android_channel_id '=>"3274cd5e-c028-418e-b385-3728fc63370e", */
            );
            $fields = json_encode($fields);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $response = curl_exec($ch);
            curl_close($ch);

            return redirect()->back()->with('success','Notification Send Successfully !!!');

        }catch(Exception $e) {
            return redirect()->back()->with('error','Notification Not Send.');
        }
    }



}
