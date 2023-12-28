<?php

namespace App\Http\Controllers\api\v1\Paytm;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\repo\Paytm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\repo\Response;
use Exception;

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
                $amount = $request->get('amount');
                $callback = route('add_amount');

                $customer['custId'] = $user_data['id'];
                $customer['mobile'] = $user_data['phone']??'1234567890';
                $customer['email'] = $user_data['email']??'info@a2zharvests.com';
                $customer['firstName'] = $user_data['user_name'];
                $customer['lastName'] = null;
                $newOrderId = time() . $user_data['id'];
                $callback = 'https://securegw-stage.paytm.in/theia/paytmCallback?ORDER_ID=' . $newOrderId;

                $pay_paytm = new Paytm();

                $transaction_token = $pay_paytm->getTransactionToken($amount, $customer, $newOrderId, $callback);

                $data = $transaction_token;
                $msg = 'Initiate Transaction Successfully!';
                return Response::Success($data, $msg);
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
