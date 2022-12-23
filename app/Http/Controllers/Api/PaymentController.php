<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Razorpay\Api\Api;
use App\Models\PaymentLaser;

class PaymentController extends Controller
{

    public function razorPay(Request $request)
    {
        $PaymentLaser = array();
        $api_key = "rzp_test_OaqqdS52ezrJuO";
        $api_secret = "SVmdstAcV9FYREMgyTIkF64N";
        $api = new Api($api_key, $api_secret);
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required',
            'amount' => 'required',
            'notes' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            $requestData = array();
            $requestData['receipt'] = $request->order_id;
            $requestData['amount'] = $request->amount * 100;
            $requestData['currency'] = $request->currency;
            $requestData['notes'] = $request->notes;
            $result = $api->order->create($requestData);
            if (isset($result->id)) {
                $PaymentLaser = new PaymentLaser();
                $PaymentLaser->payment_id = $result->id;
                $PaymentLaser->order_id = $request->order_id;
                $PaymentLaser->signature_hash = json_encode($request->notes);
                $PaymentLaser->save();
            }
            return response()->json([
                'status' => TRUE,
                'message' => 'Order Added Successfully!!',
                'data' => $PaymentLaser
            ], 200);
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status' => FALSE,
                'message' => 'Oops Sank! Something Went Terribly Wrong !',
                'data' => $this->object
            ], 500);
        }
    }

    public function getRazorPay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            $PaymentLaser =  PaymentLaser::where(['order_id' => $request->order_id])->first();
            if (!empty($PaymentLaser)) {
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Order get Successfully!!',
                    'data' => $PaymentLaser
                ], 200);
            } else {
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Order Not Found !!',
                    'data'      => $this->object
                ], 200);
            }
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status' => FALSE,
                'message' => 'Oops Sank! Something Went Terribly Wrong !',
                'data' => $this->object
            ], 500);
        }
    }
}
