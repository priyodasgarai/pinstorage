<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\ProductDesigns;
use App\Models\OrderByAgentAssign;
use App\Models\Contact;
use App\Models\Order;
use App\Models\UserAddress;
use App\Models\OrderAdress;
use App\Models\PickupScheduling;
use App\Models\CustomizeOrder;
use App\Models\AdjustmentRequest;
use App\Models\OrderDateHistory;
use App\Models\OrderStatus;
use App\Models\AppSettings;
use App\Models\ContactForm;
use App\Models\AlterationRequest;
use Firebase\JWT\JWT;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Hash;
use PDF;
use Illuminate\Support\Facades\Mail;
use Razorpay\Api\Api;
use App\Models\PaymentLaser;
use App\Models\PaymentWallet;
use App\Models\SearchData;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Notification;
use App\Models\PaymentStatus;
use App\Models\DeliveryAgentPincode;

class CustomerWebservices extends Controller
{

    public function __construct()
    {
        $this->object = new \stdClass();
    }
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'device_type' => 'required|numeric|digits:1',
            'device_token' => 'required',
            'name' => 'required|max:255|regex:/^[a-zA-ZÑñ\s]+$/',
            'email' => 'required|email',
            'password' => 'required',
            'phone' => 'required|numeric|digits:10',
            'address' => 'required',
            'pincode' => 'required',
           // 'country_id' => 'required',
           // 'state_id' => 'required',
            'city_id' => 'required',
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
                 $otp = rand(1001, 9999);
               // $otp = "1234";
                $checkExists = User::select('id', 'name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('email', $request->email)->first();
                $checkExistsPhone = User::select('id', 'name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('phone', $request->phone)->first();
                if (is_null($checkExists) && is_null($checkExistsPhone)) :
                    $user = User::create([
                        'role_id' => 3,
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'phone' => $request->input('phone'),
                        'password' => Hash::make($request->input('password')),
                        'address' => $request->input('address'),
                        'landmark' => $request->input('landmark'),
                        'pincode' => $request->input('pincode'),
                       // 'country_id' => $request->input('country_id'),
                      //  'state_id' => $request->input('state_id'),
                        'city_id' => $request->input('city_id'),
                        'device_type' => $request->input('device_type'),
                        'device_token' => $request->input('device_token'),
                        'otp' => $otp,
                    ]);
                    $mailDetails = [
                        'otp' => $otp,
                        'subject' => 'Welcome to ' . env('APP_NAME') . ' Validate Your Email !',
                        'html' => 'emails.registration-otp',
                        'userName' => $request->input('name'),
                    ];
                    Mail::to($request->input('email'))->send(new Mailer($mailDetails));
                    $accessToken = $this->generateJWT($user->id, $request->input('name'), $request->input('email'), $request->input('phone'), $request->input('device_token'));
                    User::where('id', $user->id)->update([
                        'app_access_token' => $accessToken,
                        'created_by' => $user->id,
                    ]);
                    /*                     * ***************Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $request->input('phone');
                    $msg = "Welcome aboard the Wah Tailor family, {$request->name}! Thank you for creating an account with us. You can access it here at any time Wah Tailor — Team Wah Tailor.";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*                     * *********************************************** */
                    /*                     * ***************Save Notification***************** */
                    $value = array();
                    $value['user_id'] = $user->id;
                    $value['title'] = 'User Register';
                    $value['message'] = $msg;
                    save_notification($value);
                    /*                     * ***************Save Notification****************** */
                    $this->data['userDetails'] = User::select('*')->where('id', $user->id)->first();
                    $this->data['token'] = [
                        'type' => 'Bearer',
                        'accessToken' => $accessToken,
                        'expireTime' => time() + (30 * 24 * 60 * 60)
                    ];
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Registration successful! An OTP Has Been Sent to Your Email !!',
                        'data' => $this->data
                    ], 200);
                else :
                    if ($checkExists->email_validate == 0) :
                        $mailDetails = [
                            'otp' => $otp,
                            'subject' => 'Welcome to ' . env('APP_NAME') . ' Validate Your Email !',
                            'html' => 'emails.registration-otp',
                            'userName' => $checkExists->name,
                        ];
                        Mail::to($request->input('email'))->send(new Mailer($mailDetails));
                        $accessToken = $this->generateJWT($checkExists->id, $request->input('name'), $request->input('email'), $request->input('phone'), $request->input('device_token'));
                        User::where('id', $checkExists->id)->update([
                            'app_access_token' => $accessToken,
                            'updated_by' => $checkExists->id,
                            'otp' => $otp
                        ]);
                        $this->data['userDetails'] = $checkExists;
                        $this->data['token'] = [
                            'type' => 'Bearer',
                            'accessToken' => $accessToken,
                            'expireTime' => time() + (30 * 24 * 60 * 60)
                        ];
                        return response()->json([
                            'status' => TRUE,
                            'message' => 'An OTP Has Been Sent to Your Email !!',
                            'data' => $this->data
                        ], 200);
                    elseif (!is_null($checkExistsPhone)) :
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Phone Already Exists !!',
                            'data' => $this->object
                        ], 200);
                    else :
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Email Already Exists !!',
                            'data' => $this->object
                        ], 200);
                    endif;
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    /*
      function: Customer Login
      Author : Somnath Bhunia
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'device_type' => 'required|numeric|digits:1',
            'device_token' => 'required',
            'email' => 'required|email',
            'password' => 'required',
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
                if (User::where('email', $request->email)->exists()) :
                    $user = User::select('*')->where('email', $request->email)->first();
                    if ($user->status == 0) :
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Account Deactivated !',
                            'data' => $this->object,
                        ], 200);
                    else :
                        if (Hash::check($request->password, $user->password)) :
                            $this->data['userDetails'] = [
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'email_validate' => $user->email_validate,
                                'phone_validate' => $user->phone_validate,
                                'status' => $user->status,
                                'address' => $user->address,
                                'country_id' => $user->country_id,
                                'state_id' => $user->state_id,
                                'city_id' => $user->city_id,
                                'landmark' => $user->landmark,
                                'pincode' => $user->pincode,
                                'image' => ($user->image != '' ? asset("uploads/userImages/" . $user->image) : asset('assets/images/no-img-available.png')),
                            ];
                            $accessToken = $this->generateJWT($user->id, $request->input('name'), $request->input('email'), $user->phone, $request->input('device_token'));
                            User::where('id', $user->id)->update([
                                'app_access_token' => $accessToken,
                                'updated_by' => $user->id,
                                'device_token' => $request->device_token,
                                'device_type' => $request->device_type,
                            ]);
                            $this->data['token'] = [
                                'type' => 'Bearer',
                                'accessToken' => $accessToken,
                                'expireTime' => time() + (30 * 24 * 60 * 60)
                            ];
                            $this->data['count'] = Order::all()->count();
                            return response()->json([
                                'status' => TRUE,
                                'message' => 'Log In successful !',
                                'data' => $this->data,
                            ], 200);
                        endif;
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Wrong Credential!',
                            'data' => $this->object,
                        ], 400);
                    endif;
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No User With Email Address Present In The Database !',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $token = [
                    'id' => '',
                    'name' => '',
                    'email' => '',
                    'phone' => '',
                    'deviceToken' => '',
                    'expireTime' => time() - 3600
                ];
                $accessToken = JWT::encode($token, config('jwt.key'), 'HS256');
                User::where('id', Auth::guard('api')->user()->id)->update([
                    'app_access_token' => $accessToken,
                    'updated_by' => Auth::guard('api')->user()->id,
                ]);
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Successfully Logout !',
                    'data' => $this->object
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    /*
      function: Validate OTP
      Author : Somnath Bhunia
     */

    public function forget_pass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'phone' => 'required'
        ]);
        //$post= $request->all();
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        } elseif (!$this->validateAppkey($request->input('key'))) {

            return response()->json([
                'status' => FALSE,
                'message' => 'Invalid Key !',
                'data' => $this->object
            ], 401);
        } else {
            $userDetails = User::select('name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('phone', $request->input('phone'))->first();
            if (empty($userDetails)) {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Phone Number not present in our system',
                    'data' => $this->object
                ], 400);
            } else {
                 $otp = rand(1001, 9999);
               // $otp = "1234";
                User::where('id', $userDetails->id)->update([
                    'OTP' => $otp
                ]);
                $mailDetails = [
                    'otp' => $otp,
                    'subject' => 'Forget Password',
                    'html' => 'emails.forgot-otp-email',
                    'userName' => $userDetails->name,
                    'userNumber' => $userDetails->phone,
                    'userEmail' => $userDetails->email,
                ];
                Mail::to($userDetails->email)->send(new Mailer($mailDetails));
                /*                     * ***************Send Forget Password WP Message***************** */
                $data = array();
                $data['mobile'] = $userDetails->phone;
                $msgForget = "Dear {$userDetails->name}, the onetime password {$otp} to reset your password at Wah Tailor on {$userDetails->phone} & {$userDetails->email}. This OTP will expire in 5 minutes — Team Wah Tailor.";
                $data['message'] = urlencode($msgForget);
                sendWhatsappSms($data);
                /*                     * *********************************************** */
                $this->data['details'] = $userDetails;
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Please check your email for OTP',
                    'data' => $this->data
                ], 200);
            }
        }
    }

    public function validateOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'otp' => 'required|numeric|digits:4',
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
                $checkOTP = User::select('name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('id', Auth::guard('api')->user()->id)->where('otp', $request->input('otp'))->first();
                if (is_null($checkOTP)) :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Wrong OTP !',
                        'data' => $this->object
                    ], 400);
                else :
                    User::where('id', Auth::guard('api')->user()->id)->update([
                        'otp' => null,
                        'email_validate' => 1,
                        'phone_validate' => 1
                    ]);
                    $checkOTP['email_validate'] = 1;
                    $this->data['userDetails'] = $checkOTP;
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'OTP Validated !',
                        'data' => $this->data
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
        return response()->json($request->all());
    }

    /*
      function: Resend OTP
      Author : Somnath Bhunia
     */

    public function resendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required'
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
            $otp = rand(1001, 9999);
            try {
                User::where('id', Auth::guard('api')->user()->id)->update([
                    'OTP' => $otp
                ]);
                $userDetails = User::select('name', 'email', 'phone', 'email_validate', 'phone_validate', 'status')->where('id', Auth::guard('api')->user()->id)->first();
                $mailDetails = [
                    'otp' => $otp,
                    'subject' => 'Welcome to ' . env('APP_NAME') . ' Validate Your Email !',
                    'html' => 'emails.registration-otp',
                    'userName' => $userDetails->name,
                ];
                Mail::to(Auth::guard('api')->user()->email)->send(new Mailer($mailDetails));
                $this->data['userDetails'] = $userDetails;
                return response()->json([
                    'status' => TRUE,
                    'message' => 'New OTP Has Been Sent !',
                    'data' => $this->data
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    /*
      function: JWT Generate
      Author : Somnath Bhunia
     */

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required'
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
                $userDetails = User::where('id', Auth::guard('api')->user()->id)->first();
                if (Hash::check($request->input('new_password'), $userDetails->password)) {
                    return response()->json(
                        [
                            'status' => FALSE,
                            'message' => 'Your current password  matches with the old password!!',
                            'redirect' => ''
                        ],
                        200
                    );
                }
                if (!Hash::check($request->input('old_password'), $userDetails->password)) :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Old Password Does Not Match!',
                        'data' => $this->object
                    ], 403);
                elseif ($request->input('new_password') != $request->input('confirm_password')) :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'New Password & Confirm Passsword Does Not Match!',
                        'data' => $this->object
                    ], 200);
                else :
                    User::where('id', Auth::guard('api')->user()->id)
                        ->update([
                            'password' => Hash::make($request->input('new_password'))
                        ]);
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Password Updated Successfully!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    /*
      function: JWT Generate
      Author : Somnath Bhunia
     */

    private function generateJWT($userId, $name, $email, $phone, $deviceToken)
    {
        $token = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'deviceToken' => $deviceToken,
            'expireTime' => time() + (30 * 24 * 60 * 60)
        ];
        return JWT::encode($token, config('jwt.key'), 'HS256');
    }

    public function edit_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'name' => 'required|max:255|regex:/^[a-zA-ZÑñ\s]+$/',
            'source' => 'required',
            'email' => 'required|email',
            'phone' => 'required|digits:10',
            'address' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
            $requestData = $request->all();
            try {

                if ($request->hasFile('image')) :
                    $this->fileName = time() . '.' . $request->file('image')->extension();
                    $request->file('image')->move(public_path('uploads/userImages'), $this->fileName);
                    User::where('id', Auth::guard('api')->user()->id)->update([
                        'image' => $this->fileName
                    ]);
                endif;

                User::where('id', Auth::guard('api')->user()->id)->update([
                    'name' => $requestData['name'],
                    'email' => $requestData['email'],
                    'phone' => $requestData['phone'],
                    'address' => $requestData['address'],
                    'country_id' => $requestData['country_id'],
                    'state_id' => $requestData['state_id'],
                    'city_id' => $requestData['city_id'],
                    'landmark' => $request->post('landmark'),
                    'updated_by' => Auth::guard('api')->user()->id
                ]);
                $this->data['userDetails'] = User::selectRaw('id,name,email,phone,email_validate,phone_validate,address,country_id,state_id,city_id,landmark,updated_by,concat("' . asset('uploads/userImages') . '/",image) as image,status')->where('id', Auth::guard('api')->user()->id)->first();
                return response()->json(
                    [
                        'status' => TRUE,
                        'message' => 'Profile Updated Successfully !!',
                        'data' => $this->data
                    ],
                    200
                );
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }
    public function wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
            try {
                $this->data['wishlist'] = Wishlist::selectRaw('wishlists.*,product_designs.title,product_designs.short_description,product_designs.price,product_designs.quantity,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image,product_design_images.is_primary')->join('users', 'users.id', '=', 'wishlists.user_id', 'inner')
                    ->join('product_designs', 'product_designs.id', '=', 'wishlists.product_id', 'inner')
                    ->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('wishlists.user_id', Auth::guard('api')->user()->id)->where('wishlists.status', '!=', '3')->where('product_designs.status', '=', '1')->where('product_design_images.is_primary', '=', '1')->orderby('wishlists.id', 'desc')
                   // ->paginate(10);
                 ->get();

                if (count($this->data['wishlist']) > 0) :
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $this->data
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function wishlistRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required'
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
            try {
                Wishlist::where('user_id', Auth::guard('api')->user()->id)->where('product_id', $request->id)->delete();
                return response()->json(
                    [
                        'status' => TRUE,
                        'message' => 'Wishlist Deleted Successfully !!',
                        'data' => $this->object
                    ],
                    200
                );
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function ProductAddWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
            try {
                $checkExists = Wishlist::select('id', 'user_id', 'product_id', 'status')->where('product_id', $request->product_id)
                    ->where('user_id', Auth::guard('api')->user()->id)
                    ->where('status', '!=', '3')->first();
                if (is_null($checkExists)) :
                    Wishlist::create([
                        'user_id' => Auth::guard('api')->user()->id,
                        'product_id' => $request->product_id,
                        'status' => 1
                    ]);
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Product Added To the Wishlist !!',
                        'data' => $this->object
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Product Already Exist !!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function addToCart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'product_id' => 'required',
            'quantity' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
            try {
                $checkExists = Cart::select('id', 'user_id', 'product_id', 'quantity', 'status')->where('product_id', $request->product_id)
                    ->where('user_id', Auth::guard('api')->user()->id)
                    ->where('status', '=', '1')->first();
                if (is_null($checkExists)) :
                    Cart::create([
                        'user_id' => Auth::guard('api')->user()->id,
                        'product_id' => $request->product_id,
                        'quantity' => $request->quantity,
                        'status' => 1
                    ]);
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Product Added To the Cart !!',
                        'data' => $this->object
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Product Already Exist !!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
            try {
                $this->data['cart'] = Cart::selectRaw('carts.*,product_designs.title,product_designs.short_description,product_designs.price,concat("' . asset('uploads/productDesignImages') . '/",product_design_images.file_name) as image,product_design_images.is_primary')->join('users', 'users.id', '=', 'carts.user_id', 'inner')
                    ->join('product_designs', 'product_designs.id', '=', 'carts.product_id', 'inner')
                    ->join('product_design_images', 'product_design_images.product_design_id', '=', 'product_designs.id', 'inner')->where('carts.user_id', Auth::guard('api')->user()->id)->where('carts.status', '!=', '3')->where('product_designs.status', '=', '1')->where('product_design_images.is_primary', '=', '1')->orderby('carts.id', 'desc')
                     ->get();
                  //  ->paginate(10);

                if (count($this->data['cart']) > 0) :
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function cartRemove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required'
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
            try {
                Cart::where('user_id', Auth::guard('api')->user()->id)->where('id', $request->id)->delete();
                return response()->json(
                    [
                        'status' => TRUE,
                        'message' => 'Product Removed From Cart Successfully !!',
                        'data' => $this->object
                    ],
                    200
                );
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function customerQuery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'user_id' => 'required',
            'type' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
                Contact::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'type' => 1,
                    'message' => $request->input('message'),
                ]);
                $this->data['queryList'] = Contact::selectRaw('contacts.*,users.name as user_name,users.email as user_email,users.role_id')
                    ->join('users', 'users.id', '=', 'contacts.user_id', 'inner')
                    ->where('users.role_id', '=', '3')->where('contacts.status', '!=', '3')->where('contacts.type', '=', '1')->orderby('contacts.id', 'desc')
                    // ->get();
                    ->paginate(10);
                //pr($this->data['productDesignTrendingList']);
                if (count($this->data['queryList']) > 0) :
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function customerSuggetions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'user_id' => 'required',
            'type' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
                Contact::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'type' => 2,
                    'message' => $request->input('message'),
                ]);
                $this->data['queryList'] = Contact::selectRaw('contacts.*,users.name as user_name,users.email as user_email,users.role_id')
                    ->join('users', 'users.id', '=', 'contacts.user_id', 'inner')
                    ->where('users.role_id', '=', '3')->where('contacts.status', '!=', '3')->where('contacts.type', '=', '2')->orderby('contacts.id', 'desc')
                    // ->get();
                    ->paginate(10);
                //pr($this->data['productDesignTrendingList']);
                if (count($this->data['queryList']) > 0) :
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $this->data
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function contactApiList(Request $request)
    {
        //dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                //echo Auth::guard('api')->user()->name;
                $contacts = ContactForm::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'date' => $request->date,
                    'from_time' => now()->parse($request->from_time)->format('h:i s A'),
                    'to_time' => now()->parse($request->to_time)->format('h:i s A'),
                    'message' => $request->input('message')
                ]);
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Contact Form Submitted Successfully !!',
                    'data' => $this->object
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
        //$get = $request->all();
    }

    public function customerAlterationRequest(Request $request)
    {
        //dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'alteration_type' => 'required',
            'orderid' => $request->alteration_type == 2 ? [
                'required',
                function ($attribute, $value, $fail) {
                    $cnt = Order::where('user_id', Auth::guard('api')->user()->id)->where('order_prefix_id', $value)->count();
                    if (!$cnt) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ] : '',
            'job_title' => 'required',
            'length' => 'required',
            'job_description' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                //echo Auth::guard('api')->user()->name;
                if ($request->hasFile('alteration_image')) :
                    $this->fileName = time() . '.' . $request->file('alteration_image')->extension();
                    $request->file('alteration_image')->move(public_path('uploads/alterationImages'), $this->fileName);

                endif;
                $alterationList = AlterationRequest::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'alteration_type' => $request->alteration_type,
                    'job_title' => $request->job_title,
                    'length' => $request->length,
                    'orderid' => $request->orderid,
                    'job_description' => $request->job_description,
                    'alteration_image' => $this->fileName,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Alteration Form Submitted Successfully!!',
                    'data' => $this->object
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
        //$get = $request->all();
    }

    public function customerAlterationRequestList(Request $request)
    {
        //dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                $alterationList = AlterationRequest::where('user_id', Auth::guard('api')->user()->id)->OrderBy('id', 'DESC')
                    // ->get();
                    ->paginate(10);
                if (count($alterationList) > 0) :
                    $tempArray = [];
                    foreach ($alterationList as $key => $value) :
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'user_id' => $value->user_id,
                            'alteration_type' => $value->alteration_type,
                            'length' => $value->length,
                            'job_title' => $value->job_title,
                            'alteration_image' => ($value->alteration_image != '' ? asset("uploads/alterationImages/" . $value->alteration_image) : asset('assets/images/no-img-available.png')),
                            'job_description' => $value->job_description,
                            'alteration_price' => !is_null($value->alteration_price) ? $value->alteration_price : '',
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                        ];

                    endforeach;
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $tempArray
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $tempArray
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
        //$get = $request->all();
    }

    public function customerAlterationRequestDenied(Request $request)
    {
        //dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            AlterationRequest::where('id', $request->id)->update(['status' => 0]);
            return response()->json([
                'status' => TRUE,
                'message' => 'Concent Submitted Successfully!!',
                'data' => []
            ], 200);
        endif;
    }

    public function customerAlterationRequestAcceptDenied(Request $request)
    {
        //dd(auth()->user());
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'alteration_type' => 'required',
            'length' => 'required',
            'job_title' => 'required',
            'job_description' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                //echo Auth::guard('api')->user()->name;
                if ($request->hasFile('alteration_image')) :
                    $this->fileName = time() . '.' . $request->file('alteration_image')->extension();
                    $request->file('alteration_image')->move(public_path('uploads/alterationImages'), $this->fileName);
                /* User::where('id',Auth::guard('api')->user()->id)->update([
                  'image'          => $this->fileName
                  ]); */
                endif;
                $alterationList = AlterationRequest::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'alteration_type' => $request->alteration_type,
                    'length' => $request->length,
                    'job_title' => $request->job_title,
                    'job_description' => $request->job_description,
                    'alteration_image' => $this->fileName,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                $alterationList = AlterationRequest::where('user_id', Auth::guard('api')->user()->id)
                    // ->get();
                    ->paginate(10);
                if (count($alterationList) > 0) :
                    $tempArray = [];
                    foreach ($alterationList as $key => $value) :
                        $tempArray[$key] = [
                            'id' => $value->id,
                            'user_id' => $value->user_id,
                            'alteration_type' => $value->alteration_type,
                            'length' => $value->length,
                            'job_title' => $value->job_title,
                            'alteration_image' => ($value->alteration_image != '' ? asset("uploads/alterationImages/" . $value->alteration_image) : asset('assets/images/no-img-available.png')),
                            'job_description' => $value->job_description,
                            'status' => $value->status,
                            'created_by' => $value->created_by,
                            'updated_by' => $value->updated_by,
                            'created_at' => $value->created_at,
                            'updated_at' => $value->updated_at,
                            /* 'rating'=>!is_null($reviewrating)?round($reviewrating->ratings_average):'0' */
                        ];

                    endforeach;
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Alteration Form Submitted Successfully!!',
                        'data' => $tempArray
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
                        'data' => $tempArray
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
        //$get = $request->all();
    }

    public function customerReviewAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required',
            'product_id' => 'required',
            'rating' => 'required',
            'comment' => 'required',
            'upload_image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
                if ($request->hasFile('upload_image')) :
                    $this->fileName = time() . '.' . $request->file('upload_image')->extension();
                    $request->file('upload_image')->move(public_path('uploads/productDesignImages'), $this->fileName);
                /* User::where('id',Auth::guard('api')->user()->id)->update([
                  'image'          => $this->fileName
                  ]); */
                endif;

                $review = Review::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'style_id' => $request->product_id,
                    'order_id' => $request->order_id,
                    'rating' => $request->rating,
                    'comment' => $request->comment,
                    'upload_image' => $this->fileName
                ]);

                $order = Order::where(['id' => $request->order_id])->first();
                $customer = get_agent(Auth::guard('api')->user()->id);
                if (!is_null($review)) :

                    /*                 * ***************Admin Emai Notification***************** */
                    $admin = get_admin();
                    $mailDetails = [
                        'orderAltId' => $order->order_prefix_id,
                        'subject' => 'Order Review !',
                        'html' => 'emails.admin-review',
                        'userName' => $customer->name,
                    ];
                    // return view('emails.order-request')->with($data);
                    Mail::to($admin->email)->send(new Mailer($mailDetails));
                    /*                 * ***************Admin Emai Notification****************** */
                    /* ****************Admin Notification*******************/
                    /* ****************Customer Notification******************/

                    $customer_msg = "Dear {$customer->name}, Thanks for choosing Wah tailor. Please provide your valuable feedback. {$order->order_prefix_id}. — Team Wah Tailor.";
                    $value3 = array();
                    $value3['user_id'] = $customer->id;
                    $value3['title'] = 'Review';
                    $value3['message'] = $customer_msg;
                    save_notification($value3);
                    $data3 = array();
                    $data3['mobile'] = $customer->phone;
                    $data3['message'] = urlencode($customer_msg);
                    sendWhatsappSms($data3);
                    /*****************Customer Push Notification ***************** */
                    $title = "Review";
                    $customer_device_token = $customer->device_token;
                    send_notification($customer_device_token,  $title, $customer_msg);
                    /* ****************Customer Notification****************** */
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Review Added Successfully !!',
                        'data' => $this->object
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Unable To Add !!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function CustomerOrderAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'design_id' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'delivery_id' => 'required',
            'shipping_address_id' => 'required',
            'measurement_address_id' => 'required',
            'pickup_date' => 'required',
            'pickup_time' => 'required',
            'exp_delivery_date' => 'required',
            'payment_method' => 'required',
            'payment_type' => 'required',
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
            $shipingaddress = UserAddress::find($request->shipping_address_id);

            $measurementAddress = UserAddress::find($request->measurement_address_id);
           // dd($measurement_address);
            if(!empty($shipingaddress)){
                $customerPincode = $shipingaddress->pincode;
                $DeliveryAgentPincode = DeliveryAgentPincode::where('zipcode',$customerPincode)->first();
                if(empty($DeliveryAgentPincode)){
                    return response()->json([
                        'status' => FALSE,
                        'message' => "Delivery Agent Not Found on Shiping Pincode : {$customerPincode} !!",
                        'data' => $shipingaddress
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Shiping Address Not Found!!',
                    'data' => $shipingaddress
                ], 200);
            }
            if(!empty($measurementAddress)){
                $customerPincode = $measurementAddress->pincode;
                $DeliveryAgentPincode = DeliveryAgentPincode::where('zipcode',$customerPincode)->first();
                if(empty($DeliveryAgentPincode)){
                    return response()->json([
                        'status' => FALSE,
                        'message' => "Delivery Agent Not Found on Measurement Pincode : {$customerPincode} !!",
                        'data' => $measurementAddress
                    ], 200);
                }
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Shiping Address Not Found!!',
                    'data' => $measurementAddress
                ], 200);
            }
            \DB::beginTransaction();
            try {
                $Payment_data = "";
                $settings = AppSettings::where('id', 1)->first();
                $orderCount = Order::orderBy('id', 'desc')->first();
               if(!empty($orderCount)):
                    if ($orderCount->id <= 1620):
                        $orderCount = 1620;
                        $orderAltId = $orderCount + 1;
                    else :
                        $orderAltId = $orderCount->id + 1;
                    endif;
                else:
                    $orderAltId = 1620;
                endif;
                //dd($request->payment_method);
                $orderAltId = $settings->order_id_prefix . $orderAltId;
                 /**************Used Wallete********************** */
                $user = User::where(['id' => Auth::guard('api')->user()->id])->first();
                $due_price = $request->price;
                if (isset($request->walletUsed) && $request->walletUsed > 0) {
                    $user->wallet = $user->wallet - $request->walletUsed;
                    $user->save();
                    $due_price = $request->price - $request->walletUsed ;
                }
                /*****************Used Wallete*************************** */
                $order = Order::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'size_id' => $request->size_id,
                    'design_id' => $request->design_id,
                    'price' => $request->price,
                    'due_price' => $due_price,
                    'extra_charge' => $request->extra_charge,
                    'is_lining' => $request->is_lining,
                    'is_alteration' => $request->is_alteration,
                    'is_padded' => $request->is_padded,
                    'order_prefix_id' => $orderAltId,
                    'is_customize' => $request->is_customize,
                    'quantity' => $request->quantity,
                    'delivery_id' => $request->delivery_id,
                    'shipping_address_id' => $request->shipping_address_id,
                    'measurement_address_id' => $request->measurement_address_id,
                    'stiching_info' => $request->stiching_info,
                    'additional_info' => $request->additional_info,
                    'payment_method' => $request->payment_method,
                    'payment_type' => $request->payment_type,
                    'status' => 1
                ]);
                OrderStatus::create([
                    'order_id' => $order->id,
                    'order_status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);

                Cart::where(['user_id' => Auth::guard('api')->user()->id, 'product_id' => $request->design_id])->delete();

                OrderAdress::create([
                    'order_id' => $order->id,
                    // 'full_name'=>$request->full_name,
                    'phone' => $shipingaddress->phone,
                    'address' => $shipingaddress->address,
                    'pincode' => $shipingaddress->pincode,
                    'landmark' => $shipingaddress->landmark,
                    'atra_street_sector_vilager' => $shipingaddress->atra_street_sector_vilager,
                    'country_id' => $shipingaddress->country_id,
                    'state_id' => $shipingaddress->state_id,
                    'city_id' => $shipingaddress->city_id,
                    'address_type' => 1,
                    'status' => 1
                ]);

                OrderAdress::create([
                    'order_id' => $order->id,
                    // 'full_name'=>$request->full_name,
                    'phone' => $measurementAddress->phone,
                    'address' => $measurementAddress->address,
                    'pincode' => $measurementAddress->pincode,
                    'landmark' => $measurementAddress->landmark,
                    'atra_street_sector_vilager' => $measurementAddress->atra_street_sector_vilager,
                    'country_id' => $measurementAddress->country_id,
                    'state_id' => $measurementAddress->state_id,
                    'city_id' => $measurementAddress->city_id,
                    'address_type' => 2,
                    'status' => 1
                ]);
                if ($request->is_customize == 1) :
                    foreach ($request->addons as $key => $value) :
                        //dd($value);
                        CustomizeOrder::create([
                            'order_id' => $order->id,
                            'addon_id' => $value['addon_id'],
                            'addon_price' => $value['addon_price'],
                            'instruction' => $value['instruction'],
                            'image' => $value['userimage'],
                            /* 'image'=>$addonImage */
                        ]);
                    endforeach;
                endif;
                //endif;
                PickupScheduling::create([
                    'order_id' => $order->id,
                    'pickup_date' => $request->pickup_date,
                    'pickup_time' => $request->pickup_time,
                    'exp_delivery_date' => $request->exp_delivery_date,
                    'pickup_address' => $request->pickup_address,
                    'contact_person_name' => $request->contact_person_name,
                    'contact_person_number' => $request->contact_person_number,
                    'contact_person_email' => $request->contact_person_email,
                    'contact_person_alternative_number' => $request->contact_person_alternative_number,
                    'status' => 1
                ]);

                $agent = User::where('role_id', 2)->where('status', 1)
                    ->paginate(10);

                OrderByAgentAssign::create([
                    'order_id' => $order->id,
                    // 'user_id'=>$agent[0]->id,
                    'user_id' => 6,
                    'status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                /*****************************************razorPay*************************** */
                $PaymentLaser = array();
                $api_key = env('RAZORPAY_API_KEY');//"rzp_test_OaqqdS52ezrJuO";
                $api_secret = env('RAZORPAY_API_SECRET');//"SVmdstAcV9FYREMgyTIkF64N";
                if ($request->payment_method == 1) {
                    $api = new Api($api_key, $api_secret);
                    $requestData = array();
                    $requestData['receipt'] = $order->id;
                    $requestData['amount'] = $order->price * 100;
                    $requestData['currency'] = 'INR';
                    $requestData['notes'] = $order;
                    $result = $api->order->create($requestData);
                    if (isset($result->id)) {
                        $PaymentLaser = new PaymentLaser();
                        $PaymentLaser->payment_id = $result->id;
                        $PaymentLaser->order_id = $order->id;
                        $PaymentLaser->signature_hash = json_encode($order);
                        $Payment_data = $PaymentLaser->save();
                    }
                }
                // elseif($request->payment_method == 0 && $request->payment_type == 2){
                //     $orderPrice = round($order->price / 2, 2);
                //     $due_price = $order->price - $orderPrice;
                //     $api = new Api($api_key, $api_secret);
                //     $requestData = array();
                //     $requestData['receipt'] = $order->id;
                //     $requestData['amount'] = $orderPrice * 100;
                //     $requestData['currency'] = 'INR';
                //     $requestData['notes'] = $order;
                //     $result = $api->order->create($requestData);
                //     $order->due_price = $due_price;
                //     $order->save();
                //     if (isset($result->id)) {
                //         $PaymentLaser = new PaymentLaser();
                //         $PaymentLaser->payment_id = $result->id;
                //         $PaymentLaser->order_id = $order->id;
                //         $PaymentLaser->signature_hash = json_encode($order);
                //         $Payment_data = $PaymentLaser->save();
                //     }
                // }

                \DB::commit();
                /**************************************razorPay*************************** */
                if ($request->payment_method == 0) {
                    /************************************ALL Notification***************************************** */
                    $design_title = ProductDesigns::find($request->design_id);
                    /*                 * ***************Customer Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $user->phone;
                    $msg = "Dear {$user->name}, your order {$orderAltId} for {$design_title->title} has been placed successfully. Track the order status at Track My Order in App — Team Wah Tailor.";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*****************Customer Push Notification ***************** */
                    $title = "Order Place";
                    $customer_device_token = $user->device_token;
                    send_notification($customer_device_token, $title, $msg);
                    /*****************customer email******************************/
                    $customerMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Place !',
                        'html' => 'emails.customer-order-request',
                        'userName' => $user->name,
                    ];
                    Mail::to($user->email)->send(new Mailer($customerMailDetails));
                    /************************************************* */
                    /*****************Agent Push Notification ***************** */
                    $agent = User::where('id', 6)->first();
                    $title = "Order Request";
                    $agent_device_token = $agent->device_token;
                    $agent_msg = "Dear {$agent->name}, Your Wah Tailor order request {$orderAltId} is waiting for you to delivery. — Team Wah Tailor.";
                    send_notification($agent_device_token, $title, $agent_msg);
                    /********Agent Whatsapp message**********/
                    $data = array();
                    $data['mobile'] = $agent->phone;
                    $data['message'] = urlencode($agent_msg);
                    sendWhatsappSms($data);
                    /********Agent Email**********/
                    $agentMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.agent-order-request',
                        'userName' => $agent->name,
                    ];
                    Mail::to($agent->email)->send(new Mailer($agentMailDetails));
                    /*                 * ***************push Notification****************** */
                    /*                 * ***************Save Notification***************** */
                    $value = array();
                    $value['user_id'] = Auth::guard('api')->user()->id;
                    $value['title'] = 'Order Place';
                    $value['message'] = $msg;
                    save_notification($value);
                    /*                 * ***************Save Notification****************** */
                    /*                 * ***************Admin Emai Notification***************** */
                    $admin = get_admin();
                    $mailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.order-request',
                        'userName' => $admin->name,
                    ];
                    // return view('emails.order-request')->with($data);
                    Mail::to($admin->email)->send(new Mailer($mailDetails));
                    /*                 * ***************Admin Emai Notification****************** */
                    /************************************ALL Notification***************************************** */
                }
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Order Added  Successfully !!',
                    'data' => $order->order_prefix_id,
                    'Payment_details' => $PaymentLaser,
                ], 200);
            } catch (\Exception $e) {
                \DB::rollback();
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }
    public function CustomerReorder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required',
            // 'price' => 'required',
            // 'quantity' => 'required',
            // 'delivery_id' => 'required',
            // 'shipping_address_id' => 'required',
            // 'measurement_address_id' => 'required',
            // 'pickup_date' => 'required',
            // 'pickup_time' => 'required',
            // 'exp_delivery_date' => 'required',
            // 'payment_method' => 'required'
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
            $order = Order::with(['pickup_schedulings', 'CustomizeOrder'])->find($request->order_id);
            if (empty($order)) {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Invalid Order Id !',
                    'data' => $this->object
                ], 401);
            }
            //dd($order);
            \DB::beginTransaction();
            try {
                $Payment_data = "";
                $settings = AppSettings::where('id', 1)->first();
                $orderCount = Order::orderBy('id', 'desc')->first();
               if(!empty($orderCount)):
                    if ($orderCount->id <= 1620):
                        $orderCount = 1620;
                        $orderAltId = $orderCount + 1;
                    else :
                        $orderAltId = $orderCount->id + 1;
                    endif;
                else:
                    $orderAltId = 1620;
                endif;
                $orderAltId = $settings->order_id_prefix . $orderAltId;
                $order = Order::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'size_id' => $order->size_id,
                    'design_id' => $order->design_id,
                    'price' => $order->price,
                    'due_price' => $order->price,
                    'extra_charge' => $order->extra_charge,
                    'is_lining' => $order->is_lining,
                    'is_alteration' => $order->is_alteration,
                    'is_padded' => $order->is_padded,
                    'order_prefix_id' => $orderAltId,
                    'is_customize' => $order->is_customize,
                    'quantity' => $order->quantity,
                    'delivery_id' => $order->delivery_id,
                    'shipping_address_id' => $order->shipping_address_id,
                    'measurement_address_id' => $order->measurement_address_id,
                    'stiching_info' => $order->stiching_info,
                    'additional_info' => $order->additional_info,
                    'payment_method' => $order->payment_method,
                    'payment_type' => $order->payment_type,
                    'status' => 1
                ]);
                OrderStatus::create([
                    'order_id' => $order->id,
                    'order_status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                $user = User::where(['id' => Auth::guard('api')->user()->id])->first();
                if (isset($request->walletUsed) && $request->walletUsed > 0) {
                    $user->wallet = $user->wallet - $request->walletUsed;
                    $user->save();
                }
                Cart::where(['user_id' => Auth::guard('api')->user()->id, 'product_id' => $order->design_id])->delete();
                $shipingaddress = UserAddress::find($order->shipping_address_id);
                OrderAdress::create([
                    'order_id' => $order->id,
                    // 'full_name'=>$request->full_name,
                    'phone' => $shipingaddress->phone,
                    'address' => $shipingaddress->address,
                    'pincode' => $shipingaddress->pincode,
                    'landmark' => $shipingaddress->landmark,
                    'atra_street_sector_vilager' => $shipingaddress->atra_street_sector_vilager,
                    'country_id' => $shipingaddress->country_id,
                    'state_id' => $shipingaddress->state_id,
                    'city_id' => $shipingaddress->city_id,
                    'address_type' => 1,
                    'status' => 1
                ]);
                $measurement_address = UserAddress::find($order->measurement_address_id);
                OrderAdress::create([
                    'order_id' => $order->id,
                    // 'full_name'=>$request->full_name,
                    'phone' => $measurement_address->phone,
                    'address' => $measurement_address->address,
                    'pincode' => $measurement_address->pincode,
                    'landmark' => $measurement_address->landmark,
                    'atra_street_sector_vilager' => $measurement_address->atra_street_sector_vilager,
                    'country_id' => $measurement_address->country_id,
                    'state_id' => $measurement_address->state_id,
                    'city_id' => $measurement_address->city_id,
                    'address_type' => 2,
                    'status' => 1
                ]);
                if ($order->is_customize == 1) :
                    if (isset($order->CustomizeOrder)) {
                        foreach ($order->CustomizeOrder as $key => $value) :
                            //dd($value);

                            CustomizeOrder::create([
                                'order_id' => $order->id,
                                'addon_id' => $value->addon_id,
                                'addon_price' => $value->addon_price,
                                'instruction' => $value->instruction,
                                'image' => $value->userimage,
                                /* 'image'=>$addonImage */
                            ]);
                        endforeach;
                    }
                endif;
                if (isset($order->pickup_schedulings)) {
                    PickupScheduling::create([
                        'order_id' => $order->id,
                        'pickup_date' => $order->pickup_schedulings->pickup_date,
                        'pickup_time' => $order->pickup_schedulings->pickup_time,
                        'exp_delivery_date' => $order->pickup_schedulings->exp_delivery_date,
                        'pickup_address' => $order->pickup_schedulings->pickup_address,
                        'contact_person_name' => $order->pickup_schedulings->contact_person_name,
                        'contact_person_number' => $order->pickup_schedulings->contact_person_number,
                        'contact_person_email' => $order->pickup_schedulings->contact_person_email,
                        'contact_person_alternative_number' => $order->pickup_schedulings->contact_person_alternative_number,
                        'status' => 1
                    ]);
                }
                // $agent = User::where('role_id', 2)->where('status', 1)
                //     ->paginate(10);

                OrderByAgentAssign::create([
                    'order_id' => $order->id,
                    // 'user_id'=>$agent[0]->id,
                    'user_id' => 6,
                    'status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                /*****************************************razorPay*************************** */
                $PaymentLaser = array();
                if ($order->payment_method == 1) {
                    $api_key = env('RAZORPAY_API_KEY');//"rzp_test_OaqqdS52ezrJuO";
                    $api_secret = env('RAZORPAY_API_SECRET');//"SVmdstAcV9FYREMgyTIkF64N";
                    $api = new Api($api_key, $api_secret);
                    $requestData = array();
                    $requestData['receipt'] = $order->id;
                    $requestData['amount'] = $order->price * 100;
                    $requestData['currency'] = 'INR';
                    $requestData['notes'] = $order;
                    $result = $api->order->create($requestData);
                    if (isset($result->id)) {
                        $PaymentLaser = new PaymentLaser();
                        $PaymentLaser->payment_id = $result->id;
                        $PaymentLaser->order_id = $order->id;
                        $PaymentLaser->signature_hash = json_encode($order);
                        $Payment_data = $PaymentLaser->save();
                    }
                }
                \DB::commit();
                /**************************************razorPay*************************** */
                if ($order->payment_method == 0) {
                    /************************************ALL Notification***************************************** */
                    $design_title = ProductDesigns::find($order->design_id);
                    $orderAltId = $order->order_prefix_id;
                    /*                 * ***************Customer Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $user->phone;
                    $msg = "Dear {$user->name}, your order {$orderAltId} for {$design_title->title} has been placed successfully. Track the order status at Track My Order in App — Team Wah Tailor.";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*****************Customer Push Notification ***************** */
                    $title = "Order Place";
                    $customer_device_token = $user->device_token;
                    send_notification($customer_device_token, $title, $msg);
                    /*****************customer email******************************/
                    $customerMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Place !',
                        'html' => 'emails.customer-order-request',
                        'userName' => $user->name,
                    ];
                    Mail::to($user->email)->send(new Mailer($customerMailDetails));
                    /************************************************* */
                    /*****************Agent Push Notification ***************** */
                    $agent = User::where('id', 6)->first();
                    $title = "Order Request";
                    $agent_device_token = $agent->device_token;
                    $agent_msg = "Dear {$agent->name}, Your Wah Tailor order request {$orderAltId} is waiting for you to delivery. — Team Wah Tailor.";
                    send_notification($agent_device_token, $title, $agent_msg);
                    /********Agent Whatsapp message**********/
                    $data = array();
                    $data['mobile'] = $agent->phone;
                    $data['message'] = urlencode($agent_msg);
                    sendWhatsappSms($data);
                    /********Agent Email**********/
                    $agentMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.agent-order-request',
                        'userName' => $agent->name,
                    ];
                    Mail::to($agent->email)->send(new Mailer($agentMailDetails));
                    /*                 * ***************push Notification****************** */
                    /*                 * ***************Save Notification***************** */
                    $value = array();
                    $value['user_id'] = Auth::guard('api')->user()->id;
                    $value['title'] = 'Order Place';
                    $value['message'] = $msg;
                    save_notification($value);
                    /*                 * ***************Save Notification****************** */
                    /*                 * ***************Admin Emai Notification***************** */
                    $admin = get_admin();
                    $mailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.order-request',
                        'userName' => $admin->name,
                    ];
                    // return view('emails.order-request')->with($data);
                    Mail::to($admin->email)->send(new Mailer($mailDetails));
                    /*                 * ***************Admin Emai Notification****************** */
                    /************************************ALL Notification***************************************** */
                }
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Order Added  Successfully !!',
                    'data' => $order->order_prefix_id,
                    'Payment_details' => $PaymentLaser,
                ], 200);
            } catch (\Exception $e) {
                \DB::rollback();
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }


    public function CustomerAlterationOrderAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'alterationid' => 'required',
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
                $settings = AppSettings::where('id', 1)->first();
                $orderCount = Order::select('id')->count();
                $orderAltId = 1;
                if ($orderCount > 0) :
                    $orderAltId = $orderCount + 1;
                endif;
                // $orderAltId = sprintf('%010d', $orderAltId);
                $orderAltId = $settings->order_id_prefix . $orderAltId;
                $order = Order::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'alteration_id' => $request->alterationid,
                    'price' => $request->totalprice,
                    'order_prefix_id' => $orderAltId,
                    'quantity' => 1,
                    'order_type' => 1,
                    'delivery_id' => $request->delivery_id,
                    'shipping_address_id' => $request->delivery_id,
                    'status' => 1
                ]);
                OrderStatus::create([
                    'order_id' => $order->id,
                    'order_status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                $shipingaddress = UserAddress::find($request->delivery_id);
                if ($shipingaddress) :
                    OrderAdress::create([
                        'order_id' => $order->id,
                        // 'full_name'=>$request->full_name,
                        'phone' => $shipingaddress->phone,
                        'address' => $shipingaddress->address,
                        'pincode' => $shipingaddress->pincode,
                        'landmark' => $shipingaddress->landmark,
                        'atra_street_sector_vilager' => $shipingaddress->atra_street_sector_vilager,
                        'country_id' => $shipingaddress->country_id,
                        'state_id' => $shipingaddress->state_id,
                        'city_id' => $shipingaddress->city_id,
                        'address_type' => 1,
                        'status' => 1
                    ]);
                endif;
                OrderByAgentAssign::create([
                    'order_id' => $order->id,
                    // 'user_id'=>$dagent[0]->id,
                    'user_id' => 6,
                    'status' => 1,
                    'created_by' => Auth::guard('api')->user()->id
                ]);
                AlterationRequest::where('id', $request->alterationid)->update(['status' => 1]);

                return response()->json([
                    'status' => TRUE,
                    'message' => 'Order Added  Successfully !!',
                    'data' => $order->order_prefix_id
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function CustomerMyOrder(Request $request)
    {
        define("DEFAULT_RECORDS_LIMIT", "5");
        $page_index = (int) $request->input('start') > 0 ? $request->input('start') : 1;
        $limit = (int) $request->input('length') > 0 ? $request->input('length') : DEFAULT_RECORDS_LIMIT;
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $this->data['myOrders'] = Order::with([
                    'product' => function ($query1) {
                        $query1->select('id', 'category_id', 'title');
                    }, 'shipping' => function ($query4) {
                        $query4->select('id', 'full_name', 'address', 'pincode');
                    }, 'measurement_address' => function ($query5) {
                        $query5->select('id', 'full_name', 'address', 'pincode');
                    }, 'product.productimg' => function ($query2) {
                        $query2->select('id', 'file_name', 'product_design_id');
                    }, 'alteration'
                ])
                    ->select('id', 'user_id', 'status', 'created_at', 'price', 'order_type', 'order_prefix_id', 'design_id', 'shipping_address_id', 'measurement_address_id', 'alteration_id')
                    ->where('user_id', Auth::guard('api')->user()->id)
                    ->OrderBy('id', 'DESC')
                    ->paginate(10);
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $this->data
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function orderCanceled(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required',
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
                $order = Order::with([
                    'user' => function ($query) {
                        $query->select('id', 'name', 'role_id', 'email', 'phone', 'address');
                    },
                    'pickup_schedulings' => function ($query) {
                        $query->select('id', 'order_id', 'exp_delivery_date');
                    },
                    'product' => function ($query1) {
                        $query1->select('id', 'category_id', 'title');
                    }
                ])
                    ->where(['id' => $request->id, 'user_id' => Auth::guard('api')->user()->id])
                    ->first();
                // dd($order);
                if (isset($order)) {
                    $order->status = 0;
                    $order->save();
                    OrderStatus::create([
                        'order_id' => $request->id,
                        'order_status' => 0,
                        'created_by' => Auth::guard('api')->user()->id
                    ]);
                    /*                     * ****************wallet*************************** */
                    $wallet = $order->price - $order->due_price;
                    $user = User::find(Auth::guard('api')->user()->id);
                    $user->wallet = $user->wallet + $wallet;
                    $user->save();
                    $PaymentWallet = new PaymentWallet();
                    $PaymentWallet->user_id = Auth::guard('api')->user()->id;
                    $PaymentWallet->order_id = $request->id;
                    $PaymentWallet->wallet = $user->wallet;
                    $PaymentWallet->debit = 0;
                    $PaymentWallet->credit = $wallet;
                    $PaymentWallet->status = 2;
                    $PaymentWallet->save();
                    $order->due_price = $order->price;
                    $order->save();
                    /****************Customer Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $order->user->phone;
                    $msg = "Dear {$order->user->name}, your order {$order->order_prefix_id} for {$order->product->title} has been cancelled. Track the order status at Track My Order in App. — Team Wah Tailor.";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*****************Customer Push Notification ***************** */
                    $title = "Cancelled";
                    $customer_device_token = $order->user->device_token;
                    send_notification($customer_device_token, $title, $msg);
                    /*****************customer email******************************/
                    // $customerCancelledMailDetails = [
                    //     'orderAltId' => $order->user->name,
                    //     'design_id' => $order->product->title,
                    //     'subject' => 'Order Reschedule !',
                    //     'html' => 'emails.customer-order-cancelled',
                    //     'userName' => $order->user->name,
                    //     'measurementDate'=> date("d-m-Y", strtotime($request->created_at))
                    // ];
                    // Mail::to($order->user->email)->send(new Mailer($customerCancelledMailDetails));
                    // /************************************************* */
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Order Canceled Successfully!!',
                        'data' => $this->object
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order Not found!!',
                        'data' => $this->object
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
        endif;
    }
    public function orderPaymemt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'transaction_id' => 'required',
            'transaction_data' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            $data = array();
            $order = Order::where(['order_prefix_id' => $request->order_id])->first();
            if (!empty($order)) {
                if ($order->payment_method == 0) {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'This order payment Method is COD !',
                        'data' => $order
                    ], 400);
                }
                $paymentLaser =  PaymentLaser::where(['order_id' => $order->id])->first();
                if (!empty($paymentLaser)) {
                    $total_price = $order->price;
                    $paid_price = $order->due_price;
                    $due_price = 0;
                    $payment_status = new PaymentStatus();
                    $payment_status->order_id = $order->id;
                    $payment_status->total_price = $total_price;
                    $payment_status->paid_price = $paid_price;
                    $payment_status->due_price = $due_price;
                    $payment_status->created_by = Auth::guard('api')->user()->id;
                    $payment_status->save();
                    $order->payment_status = 1;
                    $order->due_price = $due_price;
                    $order->save();
                    /********Update Payment Details******************* */
                    $paymentLaser->transaction_id = $request->transaction_id;
                    $paymentLaser->transaction_data = $request->transaction_data;
                    $paymentLaser->save();
                    /****************coupon used*************************************/
                    if (isset($request->usercoupon_id)) {
                        $UserCoupon = UserCoupon::find($request->usercoupon_id);
                        $UserCoupon->total_usage = $UserCoupon->total_usage + 1;
                        $UserCoupon->save();
                    }

                 /************************************ALL Notification***************************************** */
                    $design_title = ProductDesigns::find($order->design_id);
                    $orderAltId = $order->order_prefix_id;
                    $user = User::where(['id' => Auth::guard('api')->user()->id])->first();
                    /*                 * ***************Customer Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $user->phone;
                    $msg = "Dear {$user->name}, your order {$orderAltId} for {$design_title->title} has been placed successfully. Track the order status at Track My Order in App — Team Wah Tailor.";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*****************Customer Push Notification ***************** */
                    $title = "Order Place";
                    $customer_device_token = $user->device_token;
                    send_notification($customer_device_token, $title, $msg);
                    /*****************customer email******************************/
                    $customerMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Place !',
                        'html' => 'emails.customer-order-request',
                        'userName' => $user->name,
                    ];
                    Mail::to($user->email)->send(new Mailer($customerMailDetails));
                    /************************************************* */
                    /*****************Agent Push Notification ***************** */
                    $agent = User::where('id', 6)->first();
                    $title = "Order Request";
                    $agent_device_token = $agent->device_token;
                    $agent_msg = "Dear {$agent->name}, Your Wah Tailor order request {$orderAltId} is waiting for you to delivery. — Team Wah Tailor.";
                    send_notification($agent_device_token, $title, $agent_msg);
                    /********Agent Whatsapp message**********/
                    $data = array();
                    $data['mobile'] = $agent->phone;
                    $data['message'] = urlencode($agent_msg);
                    sendWhatsappSms($data);
                    /********Agent Email**********/
                    $agentMailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.agent-order-request',
                        'userName' => $agent->name,
                    ];
                    Mail::to($agent->email)->send(new Mailer($agentMailDetails));
                    /*                 * ***************push Notification****************** */
                    /*                 * ***************Save Notification***************** */
                    $value = array();
                    $value['user_id'] = Auth::guard('api')->user()->id;
                    $value['title'] = 'Order Place';
                    $value['message'] = $msg;
                    save_notification($value);
                    /*                 * ***************Save Notification****************** */
                    /*                 * ***************Admin Emai Notification***************** */
                    $admin = get_admin();
                    $mailDetails = [
                        'orderAltId' => $orderAltId,
                        'design_id' => $design_title->title,
                        'subject' => 'Order Request !',
                        'html' => 'emails.order-request',
                        'userName' => $admin->name,
                    ];
                    // return view('emails.order-request')->with($data);
                    Mail::to($admin->email)->send(new Mailer($mailDetails));
                    /**********************Notification End********************************** */

                    return response()->json([
                        'status' => TRUE,
                        'message' => 'payment received successfully',
                        'data' => $order,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Razorpay PaymentId Not Found!',
                        'data' => $this->object
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Order not found !',
                    'data' => $this->object
                ], 400);
            }

            //  return $zipcode;
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status' => FALSE,
                'message' => 'Oops Sank! Something Went Terribly Wrong !',
                'data' => $this->object
            ], 500);
        }
    }
    // public function orderPaymemt(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'order_id' => 'required',
    //         'transaction_id' => 'required',
    //         'transaction_data' => 'required',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => FALSE,
    //             'message' => $validator->errors(),
    //             'data' => $this->object
    //         ], 400);
    //     }
    //     try {
    //         $data = array();
    //         $order = Order::where(['order_prefix_id' => $request->order_id])->first();
    //         if (!empty($order)) {
    //             $paymentLaser =  PaymentLaser::where(['order_id' => $order->id])->first();
    //             if (!empty($paymentLaser)) {
    //                 $total_price = $order->price;
    //                 $paid_price = $order->due_price;
    //                 $due_price = 0;
    //                 $payment_status = new PaymentStatus();
    //                 $payment_status->order_id = $order->id;
    //                 $payment_status->total_price = $total_price;
    //                 $payment_status->paid_price = $paid_price;
    //                 $payment_status->due_price = $due_price;
    //                 $payment_status->created_by = Auth::guard('api')->user()->id;
    //                 $payment_status->save();
    //                 $order->payment_status = 1;
    //                 $order->due_price = $due_price;
    //                 $order->save();
    //                 /********Update Payment Details******************* */
    //                 $paymentLaser->transaction_id = $request->transaction_id;
    //                 $paymentLaser->transaction_data = $request->transaction_data;
    //                 $paymentLaser->save();
    //                 /****************coupon used*************************************/
    //                 if (isset($request->usercoupon_id)) {
    //                     $UserCoupon = UserCoupon::find($request->usercoupon_id);
    //                     $UserCoupon->total_usage = $UserCoupon->total_usage + 1;
    //                     $UserCoupon->save();
    //                 }

    //                   /************************************ALL Notification***************************************** */
    //                 $design_title = ProductDesigns::find($order->design_id);
    //                 $orderAltId = $order->order_prefix_id;
    //                 $user = User::where(['id' => Auth::guard('api')->user()->id])->first();
    //                 /*                 * ***************Customer Send WP Message***************** */
    //                 $data = array();
    //                 $data['mobile'] = $user->phone;
    //                 $msg = "Dear {$user->name}, your order {$orderAltId} for {$design_title->title} has been placed successfully. Track the order status at Track My Order in App — Team Wah Tailor.";
    //                 $data['message'] = urlencode($msg);
    //                 sendWhatsappSms($data);
    //                 /*****************Customer Push Notification ***************** */
    //                 $title = "Order Place";
    //                 $customer_device_token = $user->device_token;
    //                 send_notification($customer_device_token, $title, $msg);
    //                 /*****************customer email******************************/
    //                 $customerMailDetails = [
    //                     'orderAltId' => $orderAltId,
    //                     'design_id' => $design_title->title,
    //                     'subject' => 'Order Place !',
    //                     'html' => 'emails.customer-order-request',
    //                     'userName' => $user->name,
    //                 ];
    //                 Mail::to($user->email)->send(new Mailer($customerMailDetails));
    //                 /************************************************* */
    //                 /*****************Agent Push Notification ***************** */
    //                 $agent = User::where('id', 6)->first();
    //                 $title = "Order Request";
    //                 $agent_device_token = $agent->device_token;
    //                 $agent_msg = "Dear {$agent->name}, Your Wah Tailor order request {$orderAltId} is waiting for you to delivery. — Team Wah Tailor.";
    //                 send_notification($agent_device_token, $title, $agent_msg);
    //                 /********Agent Whatsapp message**********/
    //                 $data = array();
    //                 $data['mobile'] = $agent->phone;
    //                 $data['message'] = urlencode($agent_msg);
    //                 sendWhatsappSms($data);
    //                 /********Agent Email**********/
    //                 $agentMailDetails = [
    //                     'orderAltId' => $orderAltId,
    //                     'design_id' => $design_title->title,
    //                     'subject' => 'Order Request !',
    //                     'html' => 'emails.agent-order-request',
    //                     'userName' => $agent->name,
    //                 ];
    //                 Mail::to($agent->email)->send(new Mailer($agentMailDetails));
    //                 /*                 * ***************push Notification****************** */
    //                 /*                 * ***************Save Notification***************** */
    //                 $value = array();
    //                 $value['user_id'] = Auth::guard('api')->user()->id;
    //                 $value['title'] = 'Order Place';
    //                 $value['message'] = $msg;
    //                 save_notification($value);
    //                 /*                 * ***************Save Notification****************** */
    //                 /*                 * ***************Admin Emai Notification***************** */
    //                 $admin = get_admin();
    //                 $mailDetails = [
    //                     'orderAltId' => $orderAltId,
    //                     'design_id' => $design_title->title,
    //                     'subject' => 'Order Request !',
    //                     'html' => 'emails.order-request',
    //                     'userName' => $admin->name,
    //                 ];
    //                 // return view('emails.order-request')->with($data);
    //                 Mail::to($admin->email)->send(new Mailer($mailDetails));
    //                 /**********************Notification End********************************** */

    //                 return response()->json([
    //                     'status' => TRUE,
    //                     'message' => 'payment received successfully',
    //                     'data' => $order,
    //                 ], 200);
    //             } else {
    //                 return response()->json([
    //                     'status' => FALSE,
    //                     'message' => 'Razorpay PaymentId Not Found!',
    //                     'data' => $this->object
    //                 ], 400);
    //             }
    //         } else {
    //             return response()->json([
    //                 'status' => FALSE,
    //                 'message' => 'Order not found !',
    //                 'data' => $this->object
    //             ], 400);
    //         }

    //         //  return $zipcode;
    //     } catch (\Exception $e) {
    //         logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
    //         return response()->json([
    //             'status' => FALSE,
    //             'message' => 'Oops Sank! Something Went Terribly Wrong !',
    //             'data' => $this->object
    //         ], 500);
    //     }
    // }

    public function orderDateReschedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required',
            'created_at' => 'required'
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
                $ordersId = Order::with([
                    'user' => function ($query) {
                        $query->select('id', 'name', 'role_id', 'email', 'phone', 'address');
                    },
                    'pickup_schedulings' => function ($query) {
                        $query->select('id', 'order_id', 'exp_delivery_date');
                    },
                    'product' => function ($query1) {
                        $query1->select('id', 'category_id', 'title');
                    }
                ])->find($request->id);
                //return $ordersId;
                OrderDateHistory::create([
                    'order_id' => $ordersId->id,
                    'order_date' => $ordersId->created_at
                ]);
                // Order::where('id', $request->id)->where('user_id', Auth::guard('api')->user()->id)->update([
                //     'created_at' => $request->created_at
                // ]);
                PickupScheduling::where('order_id', $request->id)->update([
                    'created_at' => $request->created_at
                ]);

                /************************************ALL Notification***************************************** */
                /****************Customer Send WP Message***************** */
                $data = array();
                $data['mobile'] = $ordersId->user->phone;
                $msg = "Dear {$ordersId->user->name}, your order {$ordersId->order_prefix_id} for {$ordersId->product->title} successfully rescheduled on " . date("d-m-Y", strtotime($request->created_at)) . " . Track the order status at Track My Order in App — Team Wah Tailor.";
               // $msg = "Dear {$ordersId->user->name}, your order {$ordersId->order_prefix_id} for {$ordersId->product->title} successfully rescheduled on DATE for Measurement " . date("d-m-Y", strtotime($request->created_at)) . " . Track the order status at Track My Order in App — Team Wah Tailor.";
                $data['message'] = urlencode($msg);
                sendWhatsappSms($data);
                /*****************Customer Push Notification ***************** */
                $title = "Reschedule";
                $customer_device_token = $ordersId->user->device_token;
                send_notification($customer_device_token, $title, $msg);
                /*****************customer email******************************/
                $customerRescheduleMailDetails = [
                    'orderAltId' => $ordersId->user->name,
                    'design_id' => $ordersId->product->title,
                    'subject' => 'Order Reschedule !',
                    'html' => 'emails.customer-order-reschedule',
                    'userName' => $ordersId->user->name,
                    'measurementDate' => date("d-m-Y", strtotime($request->created_at))
                ];
                Mail::to($ordersId->user->email)->send(new Mailer($customerRescheduleMailDetails));
                /************************************************* */
                /************************************** */
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Order Reschedule Successfully!!',
                    'data' => $this->object
                ], 200);
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function CustomerOrderDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required',
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
                $this->data['orderDetails'] = Order::selectRaw('orders.id,orders.user_id,orders.order_prefix_id,order_statuses.order_id,order_statuses.order_status,order_statuses.created_at')->join('order_statuses', 'order_statuses.order_id', '=', 'orders.id', 'inner')->where('orders.id', $request->id)->where('orders.user_id', Auth::guard('api')->user()->id)->orderby('order_statuses.created_at', 'DESC')->take(1)
                    // ->get();
                    ->paginate(10);
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $this->data
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

    public function CustomerOrderDetailsInvoice($id)
    {
        try {
            $data['title'] = 'INVOICE';
            $data['orderDetails'] = Order::with([
                'OrderStatus' => function ($query) {
                    $query->select('id', 'order_id', 'order_status', 'created_at');
                    $query->OrderBy('order_statuses.created_at', 'DESC');
                    $query->first();
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'role_id', 'email', 'phone', 'address');
                },
                'product' => function ($query) {
                    $query->select('id', 'title');
                },
                'pickup_schedulings' => function ($query) {
                    $query->select('id', 'order_id', 'exp_delivery_date');
                },
            ])
                ->select('id', 'user_id', 'order_prefix_id', 'price', 'design_id')
                ->where('orders.id', $id)
                ->first();
            // return view('pages.order_pdf_view', $data);
            $pdf = PDF::loadView('pages.order_pdf_view', $data);
            return $pdf->download('orders.pdf');
        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status' => FALSE,
                'message' => 'Oops Sank! Something Went Terribly Wrong !',
                'data' => $this->data
            ], 500);
        }
        // endif;
    }

    public function searchHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $this->data = SearchData::where('user_id', Auth::guard('api')->user()->id)->latest()->get();
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $this->data
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

    public function searchCoupon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'coupon_code' => 'required',
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
            try {
                $coupon = Coupon::where(['coupon_code' => $request->coupon_code])->first();

                if (!empty($coupon)) {

                    if ($coupon->end_date <= date('Y/m/d H:i:s')) {
                        $usercoupon = UserCoupon::where(['coupon_id' => $coupon->id, 'user_id' => Auth::guard('api')->user()->id])->first();
                        if (!empty($usercoupon)) {
                            if ($usercoupon->usage_limit_per_user > $usercoupon->total_usage) {
                                $this->data['coupon'] = $coupon;
                                $this->data['usercoupon'] = $usercoupon;
                                return response()->json([
                                    'status' => TRUE,
                                    'message' => 'Coupon is valid!!',
                                    'data' => $this->data
                                ], 200);
                            } else {
                                return response()->json([
                                    'status' => FALSE,
                                    'message' => 'Coupon Already used!!',
                                    'data' => $this->data
                                ], 200);
                            }
                        } else {
                            return response()->json([
                                'status' => FALSE,
                                'message' => 'Coupon Not Assign!!',
                                'data' => $this->data
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Coupon expired!!',
                            'data' => $this->data
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Coupon Not Available!!',
                        'data' => $this->data
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

    public function orderTrack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'id' => 'required',
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
            try {
                $this->data['orderTrackDetails'] = Order::with([
                    'OrderStatus' => function ($query) {
                        $query->OrderBy('order_statuses.created_at', 'ASC');
                    }
                ])
                    ->where('orders.id', $request->id)
                    ->first();
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $this->data
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

    public function adjustmentRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required',
            'comments' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json(
                [
                    'status' => FALSE,
                    'message' => 'Please Input Valid Credentials',
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
                $adjustments = AdjustmentRequest::create([
                    'user_id' => Auth::guard('api')->user()->id,
                    'order_id' => $request->order_id,
                    'comments' => $request->comments
                ]);
                if (!is_null($adjustments)) :
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Request Added Successfully !!',
                        'data' => $this->object
                    ], 200);
                else :
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Unable To Add !!',
                        'data' => $this->object
                    ], 200);
                endif;
            } catch (\Exception $e) {
                logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->object
                ], 500);
            }
        endif;
    }

    public function walletBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $wallet = "";
                $wallet = User::where(['id' => Auth::guard('api')->user()->id])->first('wallet');
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Data Available!!',
                    'data' => $wallet
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
    public function notificationList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $Notification = Notification::where(['user_id' => Auth::guard('api')->user()->id])->orderby('id', 'desc')->paginate(10);
                //->get();
                if (!empty($Notification)) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $Notification
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Data Not Available!!',
                        'data' => ""
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

    public function clearSearch(Request $request)
  {
      $validator = Validator::make($request->all(), [
          'key' => 'required',
          'source' => 'required',
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
          try {
              $SearchData = SearchData::where(['user_id' => Auth::guard('api')->user()->id])->delete();
              if ($SearchData) {
                  return response()->json([
                      'status' => TRUE,
                      'message' => 'SearchData Delete Successfully!!',
                      'data' => $SearchData
                  ], 200);
              } else {
                  return response()->json([
                      'status' => FALSE,
                      'message' => 'Data Not Available!!',
                      'data' => ""
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
   public function clearNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
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
                $Notification = Notification::where(['user_id' => Auth::guard('api')->user()->id])->delete();
                if ($Notification) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Notification Delete Successfully!!',
						'data' => ""
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Data Not Available!!',
                        'data' => ""
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
    public function cartWishlistsCount() {
            try {
                $this->data['totalWishlist'] = Wishlist::where(['user_id' => Auth::guard('api')->user()->id])->count();
                $this->data['totalCart'] = Cart::where(['user_id' => Auth::guard('api')->user()->id])->count();
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
						'data' => $this->data
                    ], 200);

            } catch (\Exception $e) {
				logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Oops Sank! Something Went Terribly Wrong !',
                    'data' => $this->data
                ], 500);
            }
    }
}
