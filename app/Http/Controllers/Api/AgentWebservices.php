<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductDesigns;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentStatus;
use App\Models\PickupScheduling;
use App\Models\User;
use Firebase\JWT\JWT;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\UserMeasurement;
use App\Models\UserCoupon;

class AgentWebservices extends Controller
{

    public function __construct()
    {
        $this->object = new \stdClass();
    }

    public function UserMeasurementAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'category_id' => 'required',
            'measurement' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);

        else :
             try {
                $search = UserMeasurement::where(['category_id' => $request->category_id, 'user_id' => $request->user_id])->first();
                if (!empty($search)) {
                    $search->measurement = $request->measurement;
                    $this->data['UserMeasurement'] = $search->save();
                } else {
                    $search = new UserMeasurement();
                    $search->user_id = $request->user_id;
                    $search->category_id = $request->category_id;
                    $search->measurement = $request->measurement;
                    $this->data['UserMeasurement'] = $search->save();
                }

                return response()->json([
                    'status' => TRUE,
                    'message' => 'UserMeasurement added successful !',
                    'data' => $this->data,
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
    public function UserMeasurementList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);

        else :
            try {
                $UserMeasurement = UserMeasurement::where(['user_id' => $request->user_id]);
                $total = $UserMeasurement->count();
                $this->data['UserMeasurement'] = $UserMeasurement->get();
                if ($total > 0) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'UserMeasurement get successful !',
                        'data' => $this->data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Data not found!',
                        'data' => $this->data,
                    ], 400);
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

    public function pincodeAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'zipcode' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);

        else :
            try {
                $zipcode = explode(",", $request->zipcode);

                if (!empty($zipcode)) {

                    foreach ($zipcode as $zip) {
                        $DeliveryAgentPincode = new DeliveryAgentPincode();
                        $DeliveryAgentPincode->user_id = $request->user_id;
                        $DeliveryAgentPincode->zipcode = $zip;
                        $this->data = $DeliveryAgentPincode->save();
                    }
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Pincode added successful !',
                        'data' => $this->data,
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
                if (User::where('email', $request->email)->where('role_id', '=', '2')->exists()) :
                    /* User::create([
                      'device_type'   => $request->input('device_type'),
                      'device_token'  => $request->input('device_token'),
                      ]); */
                    $user = User::select('id', 'name', 'email', 'phone', 'password', 'email_validate', 'phone_validate', 'status')->where('email', $request->email)->first();
                    //pr($user);
                    /* User::where('id',$user->id)->update([
                      'device_type'   => $request->input('device_type'),
                      'device_token'  => $request->input('device_token'),
                      ]); */

                    if ($user->status == 0) :
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Account Deactivated !',
                            'data' => $this->object,
                        ], 200);
                    else :
                        if (Hash::check($request->password, $user->password)) :
                            User::where('id', $user->id)->update([
                                'device_type' => $request->input('device_type'),
                                'device_token' => $request->input('device_token'),
                            ]);
                            $this->data['userDetails'] = [
                                'name' => $user->name,
                                'email' => $user->email,
                                'phone' => $user->phone,
                                'email_validate' => $user->email_validate,
                                'phone_validate' => $user->phone_validate,
                                'status' => $user->status,
                                'image' => ($user->image != '' ? asset("uploads/userImages/" . $user->image) : asset('assets/images/no-img-available.png')),
                            ];
                            $accessToken = $this->generateJWT($user->id, $request->input('email'), $user->phone, $request->input('device_token'));
                            User::where('id', $user->id)->update([
                                'app_access_token' => $accessToken,
                                'updated_by' => $user->id,
                            ]);
                            $this->data['token'] = [
                                'type' => 'Bearer',
                                'accessToken' => $accessToken,
                                'expireTime' => time() + (30 * 24 * 60 * 60)
                            ];
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
                    ], 422);
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
                if (Hash::check($request->get('new_password'), $userDetails->password)) {
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
                    ], 200);
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
                        'status' => TRUE,
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

    public function getProfileDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'name' => 'required|max:255|regex:/^[a-zA-ZÑñ\s]+$/',
            'email' => 'required|email',
            'city_id' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'phone' => 'required|digits:10'
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
                pr($userDetails);
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

    public function measurementList(Request $request)
    {
        // dd();
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
                $data = Order::with([
                    'user' => function ($query1) {
                        $query1->select('id', 'name', 'phone');
                    },
                    'productDesign' => function ($query2) {
                        $query2->select('id', 'category_id', 'title');
                    }, 'productDesign.category', 'productDesign.productimg' => function ($query2) {
                        $query2->select('id', 'product_design_id', 'is_primary', 'file_name');
                    }, 'productDesign.category.ProductDesignMeasurements'
                    // , 'pickup_schedulings'
                    , 'order_adresses' => function ($query7) {
                        $query7->select('id', 'address', 'order_id');
                    }
                    //   , 'DesignImages'
                    //  , 'order_by_agent_assigns'
                    //  , 'OrderStatus'
                    //  , 'alteration'
                    //  , 'CustomizeOrder'
                    //  , 'CustomizeOrder.addonsImages',
                    // 'measurement_address'
                ])
                    ->whereNull('orders.measurement')
                    // ->select('id','user_id','created_at','price','order_type','order_prefix_id','design_id','shipping_address_id','measurement_address_id','alteration_id')
                    // ->where('user_id', Auth::guard('api')->user()->id)
                    // ->where('order_adresses.address_type','=','2')
                    ->OrderBy('orders.created_at', 'DESC')
                    // ->get();
                    ->paginate(10);
                //
                if (count($data) > 0) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'imageUrl' => url('uploads/productDesignImages'),
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
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

    public function orderList(Request $request)
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
                $data = Order::with([
                    'user' => function ($query1) {
                        $query1->select('id', 'name', 'phone');
                    },
                    'productDesign' => function ($query2) {
                        $query2->select('id', 'category_id', 'title');
                    }
                    //  , 'productDesign.category.parentname'
                    , 'productDesign.productimg' => function ($query2) {
                        $query2->select('id', 'product_design_id', 'is_primary', 'file_name');
                    }
                    //  , 'productDesign.category.ProductDesignMeasurements'
                    // , 'pickup_schedulings'
                    , 'order_adresses' => function ($query7) {
                        $query7->select('id', 'address', 'order_id');
                    }
                    //   , 'DesignImages'
                    //  , 'order_by_agent_assigns'
                    //  , 'OrderStatus'
                    //  , 'alteration'
                    //  , 'CustomizeOrder'
                    //  , 'CustomizeOrder.addonsImages',
                    // 'measurement_address'
                    //     'user'
                    //     , 'productDesign'
                    //     , 'productDesign.category.parentname'
                    //     , 'productDesign.productimg'
                    //   //   , 'productDesign.category.ProductDesignMeasurements'
                    //     , 'pickup_schedulings'
                    //     , 'order_adresses'
                    //     , 'DesignImages'
                    //     , 'order_by_agent_assigns'
                    //     , 'OrderStatus'
                    //     , 'alteration'
                    //     , 'CustomizeOrder'
                    //     , 'CustomizeOrder.addonsImages',
                    //     'measurement_address'
                ])
                    ->orderby('orders.id', 'desc')
                    //  ->where('orders.status', '=', '1')
                    // ->get();
                    ->paginate(10);
                // return $data;
                if (count($data) > 0) {
                    return response()->json([
                        'status' => TRUE,
                        'imageUrl' => url('uploads/productDesignImages'),
                        'message' => 'Data Available!!',
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
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

    private function generateJWT($userId, $email, $phone, $deviceToken)
    {
        $token = [
            'id' => $userId,
            'email' => $email,
            'phone' => $phone,
            'deviceToken' => $deviceToken,
            'expireTime' => time() + (30 * 24 * 60 * 60)
        ];
        // pr($token);
        return JWT::encode($token, config('jwt.key'), 'HS256');
    }

    public function orderDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'source' => 'required',
            'order_id' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :


            try {

                $data = Order::with([
                    'user' => function ($query) {
                        $query->select('id', 'name as userName');
                    }, 'productDesign', 'productDesign.category.parentname'
                    //   , 'productDesign.category.ProductDesignMeasurements'
                    , 'pickup_schedulings', 'order_adresses', 'DesignImages', 'order_by_agent_assigns', 'OrderStatus', 'alteration', 'CustomizeOrder', 'CustomizeOrder.addonsImages',
                    'measurement_address'
                ])->where(['id' => $request->order_id])->first();

                if (!empty($data)) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
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

    public function measurementUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'measurement' => 'required',
            //  'otp' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);

        else :
            try {
                $order = Order::find($request->order_id);
                if (!empty($order)) {
                    $order->measurement = $request->measurement;
                    $order->status = 3;
                    $order->save();
                    $this->data = $order;
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $request->order_id;
                    $orderStatus->order_status = 3;
                    $orderStatus->created_by = Auth::guard('api')->user()->id;
                    $orderStatus->save();

                    /********************************************************/
                    $product_designs = ProductDesigns::find($order->design_id);
                    $search = UserMeasurement::where(['category_id' => $product_designs->category_id, 'user_id' => $order->user_id])->first();
                    if (!empty($search)) {
                        $search->measurement = $request->measurement;
                        $this->data['UserMeasurement'] = $search->save();
                    } else {
                        $search = new UserMeasurement();
                        $search->user_id = $order->user_id;
                        $search->category_id = $product_designs->category_id;
                        $search->measurement = $request->measurement;
                        $this->data['UserMeasurement'] = $search->save();
                    }
                    /*********************************************************/
                    /*                 * ***************Agent Measurement Request Send WP Message***************** */
                    $user = User::find(Auth::guard('api')->user()->id);
                    $data = array();
                    $data['mobile'] = $user->phone;
                   // $agentMsg = "Dear {$user->name}, Your Wah Tailor order request {$order->order_prefix_id} is waiting for you to take measurement. Team Wah Tailor.";
                   // $agentMsg = "Dear {$user->name}, New Order {$order->order_prefix_id} has been receieved & waiting  for you to take measurement.. Team Wah Tailor.";
                    $agentMsg = "Dear {$user->name},  Your Wah Tailor order request {$order->order_prefix_id} is waiting for you to take measurement. - Team Wah Tailor.";
                    $data['message'] = urlencode($agentMsg);
                    sendWhatsappSms($data);
                    /*****************Customer Push Notification ***************** */
                    $title = "Measurement Request";
                    $agent_device_token = $user->device_token;
                    send_notification($agent_device_token,  $title, $agentMsg);
                    /*****************customer email******************************/
                    $agentMailDetails = [
                        'orderAltId' => $order->order_prefix_id,
                        'subject' => 'Measurement Request !',
                        'html' => 'emails.agent-measurement-request',
                        'userName' => $user->name,
                    ];
                    Mail::to($user->email)->send(new Mailer($agentMailDetails));
                    /************************************************* */
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Measurement added successful !',
                        'data' => $this->data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order not found',
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
        endif;
    }

    public function sendMeasurementOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                $data = array();
                $order = Order::with('user')->find($request->order_id);
                if (!empty($order)) {
                    $otp = rand(1000, 9999);
                    $order->otp = $otp;
                    $order->status = 4;
                    $order->save();
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $request->order_id;
                    $orderStatus->order_status = 4;
                    $orderStatus->created_by = Auth::guard('api')->user()->id;
                    $orderStatus->save();
                    $data['order'] = $order;

                    /*                 * ***************Out for Delivery Customer Send WP Message***************** */
                    $data = array();
                    $data['mobile'] = $order->user->phone;
                    $msg = "Dear {$order->user->name},Your ORDER ID:{$order->order_prefix_id}  is out for delivery. Our delivery agent will arrive shortly & you are requested to recieve the parcel. — Team Wah Tailor.";
                   // $msg = "Dear {$order->user->name},Your ORDER ID:{$order->order_prefix_id} is out for delivery and will reach the delivery address today. Our delivery agent will arrive shortly and you are requested to arrange for receiving the parcel.Have a great day ahead!";
                    $data['message'] = urlencode($msg);
                    sendWhatsappSms($data);
                    /*****************Out for Delivery Customer Push Notification ***************** */
                    $title = "Out for Delivery";
                    $customer_device_token = $order->user->device_token;
                    send_notification($customer_device_token,  $title, $msg);
                    /*****************customer email******************************/
                    // $customerMailDetails = [
                    //     'otp' => $otp,
                    //     'subject' => 'Out for Delivery',
                    //     'html' => 'emails.customer-out-for-delivery',
                    //     'userName' => $order->user->name,
                    //     'orderId'=> $order->order_prefix_id,
                    // ];
                    // Mail::to($order->user->email)->send(new Mailer($customerMailDetails));
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Otp send successful !',
                        'data' => $data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order not found',
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
        endif;
    }

    public function readyForMeasurement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                $data = array();
                $order = Order::find($request->order_id);
                if (!empty($order)) {
                    $order->status = 2;
                    $order->save();
                    $data['order'] = $order;
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $request->order_id;
                    $orderStatus->order_status = 2;
                    $orderStatus->save();
                    $data['orderStatus'] = $orderStatus;
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Update measurement status successful !',
                        'data' => $data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order not found',
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
        endif;
    }

    public function orderStatusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                $data = array();
                $order = Order::with('user')->find($request->order_id);
                if (!empty($order)) {
                    $order->status = $request->status;
                    $order->save();
                    $data['order'] = $order;
                    $orderStatus = new OrderStatus();
                    $orderStatus->order_id = $request->order_id;
                    $orderStatus->order_status = $request->status;
                    $orderStatus->created_by = Auth::guard('api')->user()->id;
                    $orderStatus->save();
                    $data['orderStatus'] = $orderStatus;
                    if ($request->status == 2) {
                        /*                 * ***************Out for Measurement Customer Send WP Message***************** */
                        $data = array();
                        $data['mobile'] = $order->user->phone;
                        $msgMeasurement = "Dear {$order->user->name},As discussed, our expert designer will reach your registered delivery address on" . date("d/m/Y") . "  at TIME to take your measurements. Team Wah Tailor.!";
                        //$msgMeasurement = "Dear {$order->user->name},Our agent will reach your delivery address registered with us on " . date("d/m/Y") . " to take your measurements for the ORDER ID:{$order->order_prefix_id}. Thank you for choosing WahTailor!";
                        $data['message'] = urlencode($msgMeasurement);
                        sendWhatsappSms($data);
                        /*****************Out for Measurement Customer Push Notification ***************** */
                        $title = "Out for Measurement";
                        $customer_device_token = $order->user->device_token;
                        send_notification($customer_device_token,  $title, $msgMeasurement);
                        /*****************customer email******************************/
                    }
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Status added successful !',
                        'data' => $data,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order not found',
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
        endif;
    }

    public function orderMeasurementList(Request $request)
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
                $data = Order::with([
                    'user', 'productDesign', 'productDesign.category.parentname', 'productDesign.productimg', 'productDesign.category.ProductDesignMeasurements', 'pickup_schedulings', 'order_adresses', 'DesignImages', 'order_by_agent_assigns', 'OrderStatus', 'alteration', 'CustomizeOrder', 'CustomizeOrder.addonsImages',
                    'measurement_address'
                ])
                    //  ->where('user_id', Auth::guard('api')->user()->id)
                    ->whereIn('orders.status', [3, 4])
                    ->OrderBy('orders.created_at', 'DESC')
                    //  ->get();
                    ->paginate(10);

                if (count($data) > 0) {
                    return response()->json([
                        'status' => TRUE,
                        'message' => 'Data Available!!',
                        'imageUrl' => url('uploads/productDesignImages'),
                        'data' => $data
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'No Data Found!!',
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

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'otp' => 'required'
        ]);
        if ($validator->fails()) :
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        else :
            try {
                $data = array();
                $order = Order::with('user')->find($request->order_id);
                if (!empty($order)) {
                      if($order->otp == $request->otp){
                        $order->status = 5;
                        $order->save();
                        $data['order'] = $order;
                        $orderStatus = new OrderStatus();
                        $orderStatus->order_id = $request->order_id;
                        $orderStatus->order_status = 5;
                        $orderStatus->created_by = Auth::guard('api')->user()->id;
                        $orderStatus->save();
                        $data['orderStatus'] = $orderStatus;

                        /*                 * ***************Customer Delivered email Send WP Message***************** */
                        $agentDetails = User::where('id', Auth::guard('api')->user()->id)->first();
                        $data = array();
                        $data['mobile'] = $order->user->phone;
                       // $msgCustomerDelivered = "Dear {$order->user->name},Your ORDER ID:{$order->order_prefix_id} has been successfully delivered by our deliver agent {$agentDetails->name} at " . date("d/m/Y") . ". Please let us know if you have any questions.Thank you for choosing WahTailor!";
                        $msgCustomerDelivered = "Dear {$order->user->name},Your ORDER ID:{$order->order_prefix_id} has been successfully delivered by our deliver agent {$agentDetails->name} at " . date("d/m/Y") . ". today. For details, visit wahtailor.com.— Team Wah Tailor.";
                        $data['message'] = urlencode($msgCustomerDelivered);
                        sendWhatsappSms($data);
                        /*****************Customer Delivered email Push Notification ***************** */
                        $title = "Order Place";
                        $customer_device_token = $order->user->device_token;
                        send_notification($customer_device_token,  $title, $msgCustomerDelivered);
                        /*****************Customer Delivered email******************************/
                        $customerMailDetails = [
                             'orderAltId' => $order->order_prefix_id,
                            'subject' => 'Order Delivered !',
                            'html' => 'emails.customer-order-delivered',
                            'userName' => $order->user->name,
                            'agentName' => $agentDetails->name,
                        ];
                        Mail::to($order->user->email)->send(new Mailer($customerMailDetails));
                        /************************************************* */

                        return response()->json([
                            'status' => TRUE,
                            'message' => 'otp verified successful !',
                            'data' => $data,
                        ], 200);
                    } else {
                        return response()->json([
                            'status' => FALSE,
                            'message' => 'Entered otp does not matches',
                            'data' => $this->object
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'Order not found',
                        'data' => $this->object
                    ], 400);
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

    public function orderCount(Request $request)
    {
        try {
            $data = array();
            $data['Total_delivery'] = Order::where(['status' => 5])->count();
            $data['Total_pending_delivery'] = Order::where('status', '<>', 5)->count();
            $data['Total_measurement'] = Order::whereNotNull('measurement')->count();
            $data['Total_pending_measurement'] = Order::whereNull('measurement')->count();
            return response()->json([
                'status' => TRUE,
                'message' => 'Count get successful!',
                'data' => $data,
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

    public function orderPaymemt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'price' => 'required',
            'payment_mode' => 'required',
            'payment_type' => 'required',
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
            $order = Order::find($request->order_id);
            if (!empty($order)) {
                if ($order->due_price >= $request->price) {
                    $total_price = $order->due_price;
                    $paid_price = $request->price;
                    $due_price = $order->due_price - $request->price;
                    $payment_status = new PaymentStatus();
                    $payment_status->order_id = $request->order_id;
                    $payment_status->total_price = $total_price;
                    $payment_status->paid_price = $paid_price;
                    $payment_status->due_price = $due_price;
                    $payment_status->payment_mode = $request->payment_mode;
                    $payment_status->payment_type = $request->payment_type;
                    $payment_status->created_by = Auth::guard('api')->user()->id;
                    $payment_status->save();
                    $order->payment_status = 1;
                    $order->due_price = $due_price;
                    $order->save();
                    /****************coupon used*************************************/
                    if (isset($request->usercoupon_id)) {
                        $UserCoupon = UserCoupon::find($request->usercoupon_id);
                        $UserCoupon->total_usage = $UserCoupon->total_usage + 1;
                        $UserCoupon->save();
                    }
                    /*****************Admin order-paymemt Notification******************/
                    $admin = get_admin();
                    $admin_msg = "Dear Admin, We’ve received {$request->price} on " . date("d/m/Y") . " with {$order->order_prefix_id}. — Team Wah Tailor.";
                    $value2 = array();
                    $value2['user_id'] = $admin->id;
                    $value2['title'] = 'Order Request';
                    $value2['message'] = $admin_msg;
                    save_notification($value2);
                    $data2 = array();
                    $data2['mobile'] = $admin->phone;
                    $data2['message'] = urlencode($admin_msg);
                    sendWhatsappSms($data2);
                    /*                 * ***************Admin order-paymemt Emai Notification***************** */
                    $mailDetails = [
                        'transaction_ID' => $request->order_id,
                        'payment' => $request->price,
                        'subject' => 'Transaction !',
                        'html' => 'emails.admin-transaction',
                        'userName' => $admin->name,
                    ];
                    Mail::to($admin->email)->send(new Mailer($mailDetails));
                    /*                 * ***************Admin Emai Notification****************** */
                    /*****************Admin Notification*******************/
                    /*****************user order-paymemt Notification******************/
                    $user_id = $order->user_id;
                    $user = get_agent($user_id);
                    if ($order->due_price == $request->price) {
                        $user_msg = "Dear {$user->name}, We’ve confirmed your Rs.{$request->price} on " . date("d/m/Y") . ". Thank you for choosing us! For more billing information visit App.  — Team Wah Tailor.";
                    } else {
                        $user_msg = "Dear {$user->name}, We’ve confirmed your Rs.{$request->price} on " . date("d/m/Y") . ". Remaining balance: Rs.{$due_price}. Thank you for choosing us! For details visit App. — Team Wah Tailor.";
                    }
                    $value3 = array();
                    $value3['user_id'] = $user_id;
                    $value3['title'] = 'Transaction';
                    $value3['message'] = $user_msg;
                    save_notification($value3);
                    $data3 = array();
                    $data3['mobile'] = $user->phone;
                    $data3['message'] = urlencode($user_msg);
                    sendWhatsappSms($data3);
                    /*****************order-paymemt  Push Notification ***************** */
                    $paymemtTitle = "Transaction";
                    $customer_device_token = $user->device_token;
                    send_notification($customer_device_token,  $paymemtTitle, $user_msg);
                    /*                 * ***************Customer order-paymemt Emai Notification***************** */
                    $customerMailDetails = [
                        'transaction_ID' => $request->order_id,
                        'payment' => $request->price,
                        'subject' => 'Transaction',
                        'html' => 'emails.customer-transaction',
                        'userName' => $user->name,
                    ];
                    Mail::to($user->email)->send(new Mailer($customerMailDetails));
                    /*****************user Notification*******************/

                    return response()->json([
                        'status' => TRUE,
                        'message' => 'payment received successfully',
                        'data' => $order,
                    ], 200);
                } else {
                    return response()->json([
                        'status' => FALSE,
                        'message' => 'pay amount should be less or same!',
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

        } catch (\Exception $e) {
            logger($e->getMessage() . ' -- ' . $e->getLine() . ' -- ' . $e->getFile());
            return response()->json([
                'status' => FALSE,
                'message' => 'Oops Sank! Something Went Terribly Wrong !',
                'data' => $this->object
            ], 500);
        }
    }
    public function rescheduleMeasurement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            //            $data = array();
            //            $order = Order::find($request->order_id);
            $PickupScheduling = PickupScheduling::where(['order_id' => $request->order_id])->first();
            if (!empty($PickupScheduling)) {
                $PickupScheduling->pickup_date = $request->pickup_date;
                $PickupScheduling->pickup_time = $request->pickup_time;
                $PickupScheduling->save();
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Reschedule Measurement successful !',
                    'data' => $PickupScheduling
                ], 400);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Data not found !',
                    'data' => $this->object
                ], 400);
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

    public function rescheduleDeliveryDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'exp_delivery_date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => FALSE,
                'message' => $validator->errors(),
                'data' => $this->object
            ], 400);
        }
        try {
            //            $data = array();
            //            $order = Order::find($request->order_id);
            $PickupScheduling = PickupScheduling::where(['order_id' => $request->order_id])->first();
            if (!empty($PickupScheduling)) {
                $PickupScheduling->exp_delivery_date = $request->exp_delivery_date;
                $PickupScheduling->save();
                return response()->json([
                    'status' => TRUE,
                    'message' => 'Reschedule Delivery Date successful !',
                    'data' => $PickupScheduling
                ], 400);
            } else {
                return response()->json([
                    'status' => FALSE,
                    'message' => 'Data not found !',
                    'data' => $this->object
                ], 400);
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
