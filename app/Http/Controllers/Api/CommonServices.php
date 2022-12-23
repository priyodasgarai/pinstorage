<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Hash;
use Mail;
use App\Models\Review;
use App\Models\Delivery;
use App\Models\BannerModel;
use App\Models\CategoryModel;
use App\Models\ProductDesigns;
use App\Models\ProductDesignAddons;
use App\Models\ProductDesignImages;
use App\Models\ProductDesignAddonsImages;
use App\Models\Contact;
use App\Models\Size;
use App\Models\ContactForm;
use App\Models\AppSettings;
use App\Models\Cms;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\UserAddress;
use Razorpay\Api\Api;
use App\Models\PaymentLaser;
use App\Models\SearchData;
use App\Models\DeliveryAgentPincode;

class CommonServices extends Controller
{
    public function __construct()
    {
        $this->object = new \stdClass();
    }

    /*
    function: Delivery Service list
    Author : Somnath Bhunia
    */
    public function razorPay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'receipt' => 'required',
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
            $result = array();
            $api_key = "rzp_test_OaqqdS52ezrJuO";
            $api_secret = "SVmdstAcV9FYREMgyTIkF64N";
            $api = new Api($api_key, $api_secret);
            $requestData = array();
            $requestData['receipt'] = $request->receipt;
            $requestData['amount'] = $request->amount;
            $requestData['currency'] = $request->currency;
            $requestData['notes'] = $request->notes;
            //dd($requestData);
            //array('receipt' => '123', 'amount' => 100, 'currency' => 'INR', 'notes'=> array('key1'=> 'value3','key2'=> 'value2'))
            $result[] = $api->order->create($requestData);
            dd($result);
            return response()->json([
                'status'    => TRUE,
                'message'   => 'Notification send!!',
                'data'      => $result
            ], 200);
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status'    => FALSE,
                'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                'data'      => $this->object
            ], 500);
        }
    }
    public function getRazorPay(Request $request)
    {
        $api_key = "rzp_test_OaqqdS52ezrJuO";
        $api_secret = "SVmdstAcV9FYREMgyTIkF64N";
        $api = new Api($api_key, $api_secret);
        $paymentId = 'order_Kb72mAFqHPHtCg';
        $data = $api->payment->fetch($paymentId); //$api->order->fetch($request->orderId);
        dd($data);
    }
    public function sendNotification(Request $request)
    {
        //  dd(1);
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'user_id' => 'required',
            'title' => 'required',
            'message' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            $user = User::where(['id' => $request->user_id])->first();
            $device_token = $user->device_token;
            $this->object = send_notification($device_token,  $request->title, $request->message);
            return response()->json([
                'status'    => TRUE,
                'message'   => 'Notification send!!',
                'data'      => $this->object
            ], 200);
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status'    => FALSE,
                'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                'data'      => $this->object
            ], 500);
        }
    }
    public function deliveryServiceList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = Delivery::where('status', '=', '1')
                    //	->paginate(10);
                    ->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }

    public function bannerList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'banner_type'       => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = BannerModel::selectRaw('banner_action_id,banner_action_type,banner_title,banner_sub_title,banner_description,concat("' . asset('uploads/bannerImages') . '/",image) as image');
                $this->data['details']->where('status', 1);
                if (!empty($request->input('banner_for'))) {
                    $this->data['details']->where('banner_for', $request->input('banner_for'));
                }
                $this->data['details']->where('type', $request->input('banner_type'));
                $total_row =  $this->data['details']->count();
                $result = $this->data['details']
                    // ->paginate(10);
                    ->get();
                //  dd($result);
                if ($total_row > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $result
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function forget_pass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'                   => 'required',
            'source'                => 'required',
            'phone'                 => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        } elseif (!$this->validateAppkey($request->key)) {

            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid Key !',
                'data' => $this->object
            ], 401);
        } else {
            $userDetails = User::select('id', 'name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('phone', $request->phone)->first();
            //pr($userDetails);
            if (empty($userDetails)) {
                return response()->json([
                    'status' => FALSE,
                    'message' => "Phone number doesn't exists in our database",
                    'data' => $this->object
                ], 400);
            } else {
                /*$otp = rand(1001, 9999);*/
                $otp = "1234";
                /* \DB::enableQueryLog();*/
                User::where('id', $userDetails->id)->update([
                    'otp' => $otp
                ]);
                //pr(DB::getQueryLog());
                $mailDetails = [
                    'otp'       => $otp,
                    'subject'   => 'OTP to change password',
                    'html'      => 'emails.forgot-otp-email',
                    'userName'  => $userDetails->name,
                ];
                /*****************Send WP Message******************/
                $data = array();
                $data['mobile'] = $request->phone;
                $msg = "Dear {$userDetails->name}, The {$otp} to reset your Wah Tailor password on {$request->phone} & {$request->email} is. This OTP will expire in 5 minutes — Team Wah Tailor.";
                //$msg = "Dear {$userDetails->name}, the onetime password {$otp} to reset your password at Wah Tailor is {$request->phone}. This OTP will expire in 5 minutes — Team Wah Tailor.";
               // $msg = "Dear {$userDetails->name}, Please use {$otp} to reset password on {$request->phone} & {$request->email}. This OTP will expire in 5 minutes — Team Wah Tailor.";
                $data['message'] = urlencode($msg);
                sendWhatsappSms($data);
                /**************************************************/
                /*****************Save Notification******************/
                $value = array();
                $value['user_id'] = $userDetails->id;
                $value['title'] = 'Forgot Password';
                $value['message'] = $msg;
                save_notification($value);
                /*****************Save Notification*******************/

                Mail::to($userDetails->email)->send(new Mailer($mailDetails));
                $this->data['details'] = $userDetails;
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Please check your email for OTP',
                    'data' => $this->data
                ], 200);
            }
        }
    }
    public function forget_pass_otp_validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp'               => 'required|numeric|digits:4',
            'source'            => 'required',
            'key'               => 'required',
            'phone'             => 'required'
        ]);
        //$post = $request->all();
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        } else if (!$this->validateAppkey($request->key)) {
            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid Key !',
                'data' => $this->object
            ], 401);
        } else {
            //$userDetails = User::select('*')->where('email',$request->email)->first();
            //pr($userDetails);
            $checkOTP = User::select('id', 'name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('otp', $request->otp)->where('phone', $request->phone)->first();
            if (is_null($checkOTP)) {
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Wrong OTP !',
                    'data'      => $this->object
                ], 400);
            } else {
                $new_pass = "1234";

                User::where('id', $checkOTP->id)->update([
                    'otp'            => null,
                    'password' => Hash::make($new_pass),
                    'email_validate' => 1,
                    'phone_validate' => 1
                ]);

                return response()->json([
                    'status' => TRUE,
                    'message' => 'Otp validated !! New password Sent To Your Mail !!',
                    'data' => $checkOTP
                ], 200);
            }
        }
    }
    public function user_story_banner(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key'               => 'required',
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($get['key'])) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = BannerModel::selectRaw('banner_title,banner_sub_title,banner_description,concat("' . asset('uploads/bannerImages') . '/",image) as image')->where('status', 1)->where('type', '1')
                    ->paginate(10);
                //->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function get_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($get['key'])) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', '0')
                    ->paginate(10);
                //->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }

    public function get_sub_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'id'                => 'required'
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($get['key'])) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', $get['id'])
                    ->paginate(10);
                //->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function category_men(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($get['key'])) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', '2')
                    ->paginate(10);
                //->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function category_women(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($get['key'])) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', '1')
                    ->paginate(10);
                //->get();
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function catagoryKids(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['detailsKid'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', '9')
                    ->paginate(10);
                //->get();
                //pr($this->data['detailsKid']);
                if (count($this->data['detailsKid']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function catagoryVariation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'parent'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['details'] = CategoryModel::selectRaw('id,name,description,concat("' . asset('uploads/category') . '/",image) as image,concat("' . asset('uploads/category') . '/",banner_image) as banner_image')->where('status', 1)->where('parent', $request->parent)
                    ->paginate(10);
                //->get();
                //pr($this->data['detailsKid']);
                if (count($this->data['details']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function product_list(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'category_id'       => 'required',
            'source'            => 'required'
        ]);
        //$get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $productDesignList = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.category_id', '=', $request->category_id)->where('product_design_images.is_primary', '=', '1')->where('product_designs.status', '!=', '3')->orderby('product_designs.id', 'desc')
                    ->paginate(10);
                //->get();
                if (count($productDesignList) > 0) :
                    $tempArray = [];
                    $appSettings = AppSettings::where('id', '=', '1')->first();
                    foreach ($productDesignList as $key => $value) :
                        $reviewrating = Review::selectRaw('AVG(rating) as ratings_average')->where('style_id', $value->id)->first();
                        if (!$reviewrating->ratings_average)
                            $reviewrating = $appSettings->rating;
                        else
                            $reviewrating = $reviewrating->ratings_average;
                        // dd($reviewrating);

                        //pr($appSettings);
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'category_id' => $value->category_id,
                            'delivery_id' => $value->delivery_id,
                            'title' => $value->title,
                            'quantity' => $value->quantity,
                            'price' => $value->price,
                            'size' => $value->size,
                            'short_description' => $value->short_description,
                            'is_featured' => $value->is_featured,
                            'is_trending' => $value->is_trending,
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            'categoryName' => $value->categoryName,
                            'image' => $value->image,
                            'rating' => number_format((float)$reviewrating, 1, '.', '')
                        ];

                    endforeach;
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      =>  $tempArray,
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function productToplist(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'category_id'       => 'required',
            'source'            => 'required'
        ]);
        //$get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $productDesignTopList = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.category_id', '=', $request->category_id)->where('product_designs.status', '!=', '3')->where('product_designs.is_trending', '=', '1')->where('product_design_images.is_primary', '=', '1')->orderby('product_designs.id', 'desc')
                    ->paginate(10);
                //->get();
                if (count($productDesignTopList) > 0) :
                    $tempArray = [];
                    $appSettings = AppSettings::where('id', '=', '1')->first();
                    foreach ($productDesignTopList as $key => $value) :
                        $reviewrating = Review::selectRaw('AVG(rating) as ratings_average')->where('style_id', $value->id)->first();
                        if (!$reviewrating->ratings_average)
                            $reviewrating = $appSettings->rating;
                        else
                            $reviewrating = $reviewrating->ratings_average;
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'category_id' => $value->category_id,
                            'delivery_id' => $value->delivery_id,
                            'title' => $value->title,
                            'quantity' => $value->quantity,
                            'price' => $value->price,
                            'size' => $value->size,
                            'short_description' => $value->short_description,
                            'is_featured' => $value->is_featured,
                            'is_trending' => $value->is_trending,
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            'categoryName' => $value->categoryName,
                            'image' => $value->image,
                            'rating' => number_format((float)$reviewrating, 1, '.', '')
                        ];
                    endforeach;
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $tempArray
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function trendingList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'is_trending'       => 'required'
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $productDesignTrendingList = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.is_trending', '=', '1')->where('product_design_images.is_primary', '=', '1')->where('product_designs.status', '!=', '3')->orderby('product_designs.id', 'desc')
                    ->paginate(10);
                //->get();
                //pr($this->data['productDesignTrendingList']);
                if (count($productDesignTrendingList) > 0) :
                    $tempArray = [];
                    $appSettings = AppSettings::where('id', '=', '1')->first();
                    foreach ($productDesignTrendingList as $key => $value) :
                        $reviewrating = Review::selectRaw('AVG(rating) as ratings_average')->where('style_id', $value->id)->first();
                        if (!$reviewrating->ratings_average)
                            $reviewrating = $appSettings->rating;
                        else
                            $reviewrating = $reviewrating->ratings_average;
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'category_id' => $value->category_id,
                            'delivery_id' => $value->delivery_id,
                            'title' => $value->title,
                            'quantity' => $value->quantity,
                            'price' => $value->price,
                            'size' => $value->size,
                            'short_description' => $value->short_description,
                            'is_featured' => $value->is_featured,
                            'is_trending' => $value->is_trending,
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            'categoryName' => $value->categoryName,
                            'image' => $value->image,
                            'rating' => number_format((float)$reviewrating, 1, '.', '')
                        ];
                    endforeach;
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $tempArray
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function featuredList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'is_featured'       => 'required'
        ]);
        $get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);

        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $productDesignFeaturedList = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.is_featured', '=', '1')->where('product_design_images.is_primary', '=', '1')->where('product_designs.status', '!=', '3')->orderby('product_designs.id', 'desc')
                    ->paginate(10);
                //->get();
                //pr($this->data['productDesignTrendingList']);
                if (count($productDesignFeaturedList) > 0) :
                    $tempArray = [];
                    $appSettings = AppSettings::where('id', '=', '1')->first();
                    foreach ($productDesignFeaturedList as $key => $value) :
                        $reviewrating = Review::selectRaw('AVG(rating) as ratings_average')->where('style_id', $value->id)->first();
                        if (!$reviewrating->ratings_average)
                            $reviewrating = $appSettings->rating;
                        else
                            $reviewrating = $reviewrating->ratings_average;
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'category_id' => $value->category_id,
                            'delivery_id' => $value->delivery_id,
                            'title' => $value->title,
                            'quantity' => $value->quantity,
                            'price' => $value->price,
                            'size' => $value->size,
                            'short_description' => $value->short_description,
                            'is_featured' => $value->is_featured,
                            'is_trending' => $value->is_trending,
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            'categoryName' => $value->categoryName,
                            'image' => $value->image,
                            'rating' => number_format((float)$reviewrating, 1, '.', '')
                        ];
                    endforeach;
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $tempArray
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }

    public function product_search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'searchtext'        => 'required',
            // 'user_id'            => 'required',
        ]);
        //$get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object

            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['result'] = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,
                concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.title', 'like', '%' . $request->post('searchtext') . '%')
                    ->where('product_designs.status', '!=', '3')
                    ->where('product_design_images.is_primary', '=', '1')
                    ->orderby('product_designs.id', 'desc')
                    // ->paginate(10);
                    ->get();
                /****************************************/
                if (Auth::guard('api')->user()) {
                    $SearchData = new SearchData();
                    $SearchData->user_id = Auth::guard('api')->user()->id;
                    $SearchData->search_key = $request->searchtext;
                    $SearchData->status = 1;
                    $SearchData->save();
                }
                /*******************************************/

                if (count($this->data['result']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data,
                        // 'user'         => $request->user_id
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->data
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function productDesignDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'id'                => 'required'
        ]);
        //$get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['productDesignDetailsList'] = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image,deliveries.day as day')
                    ->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->join('deliveries', 'deliveries.id', '=', 'product_designs.delivery_id', 'inner')->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('product_designs.id', '=', $request->id)->where('product_designs.status', '!=', '3')->orderby('product_designs.id', 'desc')
                    ->paginate(10);

                if (count($this->data['productDesignDetailsList']) > 0) :
                    $this->response = [];
                    $images = [];
                    //$reviewrating = [];
                    $this->response['id'] = $this->data['productDesignDetailsList'][0]->id;
                    $this->response['title'] = $this->data['productDesignDetailsList'][0]->title;
                    $this->response['category_id'] = $this->data['productDesignDetailsList'][0]->category_id;
                    $this->response['delivery_id'] = $this->data['productDesignDetailsList'][0]->delivery_id;
                    $this->response['size'] = $this->data['productDesignDetailsList'][0]->size;
                    $this->response['quantity'] = $this->data['productDesignDetailsList'][0]->quantity;
                    $this->response['price'] = $this->data['productDesignDetailsList'][0]->price;
                    $this->response['short_description'] = $this->data['productDesignDetailsList'][0]->short_description;
                    $this->response['is_featured'] = $this->data['productDesignDetailsList'][0]->is_featured;
                    $this->response['is_trending'] = $this->data['productDesignDetailsList'][0]->is_trending;
                    $this->response['status'] = $this->data['productDesignDetailsList'][0]->status;
                    $this->response['created_by'] = $this->data['productDesignDetailsList'][0]->created_by;
                    $this->response['updated_by'] = $this->data['productDesignDetailsList'][0]->updated_by;
                    $this->response['created_at'] = $this->data['productDesignDetailsList'][0]->created_at;
                    $this->response['updated_at'] = $this->data['productDesignDetailsList'][0]->updated_at;
                    $this->response['categoryName'] = $this->data['productDesignDetailsList'][0]->categoryName;
                    $this->response['sizeName'] = $this->data['productDesignDetailsList'][0]->sizeName;
                    $this->response['day'] = $this->data['productDesignDetailsList'][0]->day;
                    foreach ($this->data['productDesignDetailsList'] as $key => $value) :
                        $reviewrating = Review::selectRaw('AVG(rating) as ratings_average')->where('style_id', $value->id)->first();
                        $images[] = $value->image;
                    endforeach;
                    $this->response['rating'] = !is_null($reviewrating) ? number_format((float)$reviewrating->ratings_average, 1, '.', '') : '0.0';
                    $this->response['images'] = $images;
                    $this->response['addons'] = ProductDesignAddons::where('product_design_id',$request->id)->count() ;

                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->response
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function product_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'           => 'required',
            'source'        => 'required',
            'product_id'    => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status'   => FALSE,
                    'message'  => 'Please Input Valid Credentials',
                    'redirect' => ''
                ],
                200
            );
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Invalid Key !',
                    'data'      => $this->object
                ], 401);
            endif;
            try {
                $this->data['productreview'] = Review::selectRaw('product_designs.*,reviews.user_id,reviews.style_id,reviews.rating,reviews.comment,users.name as user_name ,concat("' . asset('uploads/userImages') . '/",users.image) as user_image')
                    ->join('users', 'users.id', '=', 'reviews.user_id', 'inner')->join('product_designs', 'product_designs.id', '=', 'reviews.style_id', 'inner')
                    ->where('product_designs.id', '=', $request->product_id)->where('reviews.approval', '=', '1')->orderby('reviews.id', 'desc')
                    ->paginate(10);
                if (count($this->data['productreview']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function sizeByDetails($value = '')
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'id'                => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['cmsDetails'] = Cms::select('*')->where('slug', '=', $slug)->where('status', '!=', '3')
                    ->paginate(10);
                //->get();
                if (count($this->data['cmsDetails']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 404);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function cms(Request $request, $slug = '')
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['cmsDetails'] = Cms::select('*')->where('slug', '=', $slug)->where('status', '!=', '3')
                    ->paginate(10);
                //->get();
                if (count($this->data['cmsDetails']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function pageList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $result = Cms::orderBy('id', 'asc');
                if (isset($request->slug)) {
                    $result = $result->where(['slug'=> $request->slug])->first();
                } else {
                    $result = $result->get();
                }
                $this->data['pageList'] = $result;
                if (!empty($this->data['pageList'])) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function countryList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['countryList'] = Country::orderBy('name', 'asc')
                    // ->paginate(10);
                    ->get();
                if (count($this->data['countryList']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function stateList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'country_id'        => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['stateList'] = State::where("country_id", $request->country_id)
                    ->get(["name", "id"]);
                if (count($this->data['stateList']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function cityList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
          //  'state_id'          => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['citiList'] = City::where("is_popular", 1)
                    ->get(["name", "id"]);
                if (count($this->data['citiList']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function addonList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'id'                => 'required'
        ]);
        //$get = $request->all();
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $addonList = ProductDesignAddons::selectRaw('product_design_id,input_group,custom_price,id')->where('product_design_id', '=', $request->id)->where('status', '=', '1')->orderby('input_group', 'asc')
                    ->paginate(10);
                //->get();
                $tempArr = [];
                if (count($addonList) > 0) :
                    foreach ($addonList as $key => $value) :
                        /*pr($value);*/
                        $tempArr[$key] = [
                            'product_design_id' => $value->product_design_id,
                            'style_name'        => $value->input_group,
                            'style_price'       => $value->custom_price
                        ];
                        $styleVariants = ProductDesignAddonsImages::where('product_design_addon_id', $value->id)->where('status', '=', '1')->orderby('title', 'asc')
                            ->paginate(10);
                        //  ->get();
                        foreach ($styleVariants as $key2 => $value2) :
                            if ($value2->addon_image && checkFileDirectory($value2->addon_image, 'uploads/productDesignImages')) :
                                $addOnImages = asset('uploads/productDesignImages/' . $value2->addon_image);
                            else :
                                $addOnImages = asset('assets/images/no-img-available.png');
                            endif;
                            $tempArr[$key]['style_variant'][$key2] = [
                                'addon_id'  => $value2->id,
                                'title'     => $value2->title,
                                'price'     => $value2->price,
                                'image'     => $addOnImages
                            ];
                        endforeach;
                    endforeach;
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      =>  $tempArr
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function userAddressList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $this->data['addressList'] = UserAddress::where('user_id', Auth::guard('api')->user()->id)->get();
                if (count($this->data['addressList']) > 0) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function userAddressAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'address'           => 'required',
            'landmark'          => 'required',
            'atra_street_sector_vilager' => 'required',
            'pincode'           => 'required',
           // 'country_id'        => 'required',
           // 'city_id'           => 'required',
           // 'state_id'          => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                $data= UserAddress::create([
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'pincode' => $request->pincode,
                    'landmark' => $request->landmark,
                    'atra_street_sector_vilager' => $request->atra_street_sector_vilager,
                    'user_id' => Auth::guard('api')->user()->id
                ]);
                return response()->json([
                    'status'    => TRUE,
                    'message'   => 'Address Added Successfully!!',
                    'data'      => $data
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function userAddressEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required',
            'id'                =>  'required',
            'address'           => 'required',
            'landmark'          => 'required',
            'atra_street_sector_vilager' => 'required',
            'pincode'           => 'required',
           // 'country_id'        => 'required',
          //  'city_id'           => 'required',
          //  'state_id'          => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                /*$this->data['addressList'] = UserAddress::all();*/
                $checkExistsPhone = User::where('phone', $request->phone)->exists();
                /*if(is_null($checkExistsPhone)):*/
                UserAddress::where('id', $request->id)->update([
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'pincode' => $request->pincode,
                    'landmark' => $request->landmark,
                    'atra_street_sector_vilager' => $request->atra_street_sector_vilager,
                  //  'country_id' => $request->country_id,
                  //  'state_id' => $request->state_id,
                  //  'city_id' => $request->city_id
                ]);
                return response()->json([
                    'status'    => TRUE,
                    'message'   => 'Address Updated Successfully!!',
                    'data'      => $this->object
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function settings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key'               => 'required',
            'source'            => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            try {
                /*$this->data['addressList'] = UserAddress::all();*/
                $settings = AppSettings::where('id', 1)->first();
                //->get();
                if (!empty($settings)) :
                    return response()->json([
                        'status'    => TRUE,
                        'message'   => 'Data Available!!',
                        'data'      => $settings
                    ], 200);
                else :
                    return response()->json([
                        'status'    => FALSE,
                        'message'   => 'No Data Found!!',
                        'data'      =>  $this->data
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'data'      => $this->object
                ], 500);
            }
        endif;
    }
    public function ImageUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => $validator->errors(),
                    'redirect' => ''
                ],
                400
            );
        else :
            if (!$this->validateAppkey($request->key)) :
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Key !',
                    'data' => $this->object
                ], 401);
            endif;
            //print_r(Auth::guard('api')->user()->id);exit;
            try {
                $url = "";
                if ($request->hasfile('image')) {
                    $image = $request->file('image');
                    $random = time() . rand(10, 1000);
                    $name = $random . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/productDesignImages'), $name);
                    $url = url('uploads/productDesignImages') . '/' . $name;
                }
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Image Added Successfully !!',
                    'data' => $url
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->data
                ], 500);
            }
        endif;
    }
     public function agentByPincode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pincode' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => $validator->errors(),
                    'redirect' => ''
                ],
                400
            );
        else :
            try {

                $DeliveryAgentPincode = DeliveryAgentPincode::
                with([ 'user'=> function ($query1) {
                    $query1->select('id', 'name', 'phone');
                }])
                ->where('zipcode', $request->pincode)
                ->get()
                ->unique('user_id')
                ->toArray();
                if (!empty($DeliveryAgentPincode)) {
                    $this->data['DeliveryAgentPincode'] = $DeliveryAgentPincode;
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $this->data
                ], 200);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Agent not found',
                    'data' => " "
                ], 200);
            }
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->data
                ], 500);
            }
        endif;
    }
}
