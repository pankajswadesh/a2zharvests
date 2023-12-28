<?php

namespace App\Http\Controllers\api\v1\Paytm;


use App\repo\PaytmChecksum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\repo\Paytm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\repo\Response;
use Exception;


//use paytm\paytmchecksum\PaytmChecksum;

class PaymentController extends Controller
{
    public function initiate_transaction(Request $request)
    {

        $msg = [
            'amount.required' => 'amount is required.',
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ], $msg);
        if ($validator->passes()) {
            try {
                $user_data = Auth::user();
//                $amount = $request->get('amount');
//                $callback = route('add_amount');
//
                $customer['custId'] = $user_data['id'];
                $customer['mobile'] = $user_data['phone']??'1234567890';
                $customer['email'] = $user_data['email']??'info@a2zharvests.com';
                $customer['firstName'] = $user_data['user_name'];
                $customer['lastName'] = null;
//                $newOrderId = time() . $user_data['id'];
//                $callback = 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=' . $newOrderId;
//
//                $pay_paytm = new Paytm();
//                $transaction_token = $pay_paytm->getTransactionToken($amount, $customer, $newOrderId, $callback);
//                $data = $transaction_token;
//                $msg = 'Initiate Transaction Successfully!';


                $orderId = time() . $user_data['id'];
                $amount = 200;




                $paytmParams = array();

                $paytmParams["body"] = array(
                    "requestType"   => "Payment",
                    "mid"           =>'ZpsqDd42488117746297',
                    "websiteName"   => "WEBSTAGING",
                    "orderId"       => $orderId,
                    "callbackUrl"   => "https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=$orderId>",
                    "txnAmount"     => array(
                        "value"     => intval($amount),
                        "currency"  => "INR",
                    ),
                    'userInfo'         => [
                        'custId'    => $customer['custId'], // Mandatory
                        'mobile'    => $customer['mobile'] ?? null, // Optional
                        'email'     => $customer['email'] ?? null, // Optional
                        'firstName' => $customer['firstName'] ?? null, // Optional
                        'lastName'  => $customer['lastName'] ?? null, // Optional
                    ],
                );

                $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "MKMo2%0SvLS_5z4%");

                $paytmParams["head"] = array(
                    "signature"    => $checksum
                );

                $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

                $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=ZpsqDd42488117746297&orderId=$orderId";



                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                $response = curl_exec($ch);

dd($response);
//                return Response::Success($data, $msg);
            } catch (Exception $e) {
                $data = [];
                $msg = $e->getMessage();
                return Response::Error($data, $msg);
            }
        } else {
            $data = [];
            $msg = $validator->errors()->first();
            return Response::Error($data, $msg);
        }
    }
    public function callback_transaction(Request $request)
    {
        try {
            $data = [];
            $msg = 'Callback';
            return Response::Success($data, $msg);
        } catch (Exception $e) {
            $data = [];
            $msg = $e->getMessage();
            return Response::Error($data, $msg);
        }
    }
}
