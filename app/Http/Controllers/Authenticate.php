<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Hash;
use Mail;
class Authenticate extends Controller
{
    public function login()
    {
    	if(Auth::guard('Admin')->check()):
            return redirect()->route('admin.dashboard');
        endif;
    	$this->data['title']='Login';
    	return view('pages.login')->with($this->data);
    }
    public function userCheck(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> trans('messages.38'),
        					'redirect'	=> ''
        			]
        		,200);
        else:
        	if(Auth::guard('Admin')->attempt($request->only('email','password'))):
                $user_details = Auth::guard('Admin')->user();
              //  dd($user_details);
                    return response()->json([
                        'status'	=> TRUE,
                        'message'	=>  trans('messages.37'),
                        'redirect'	=> route('admin.dashboard')
                ]
            ,200);


        	endif;
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=>trans('messages.36'),
        					'redirect'	=> ''
        			]
        		,200);
        endif;
    }
    public function userPostlogin(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
        					'redirect'	=> ''
        			]
        		,200);
        else:
            $otp = implode(",", $request->otp);
            $otp = str_replace(',', '', $otp);
            $user = User::where(['id'=>$request->user_id,'otp'=>$otp])->first();
        	if($user):
               Auth::guard('web')->login($user);
                    return response()->json([
                        'status'	=> TRUE,
                        'message'	=>  trans('messages.37'),
                        'redirect'	=> route('index')
                ]
            ,200);
        	 endif;
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=>trans('messages.39'),
        					'redirect'	=> ''
        			]
        		,200);
        endif;
    }
    public function webUserRegister(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255|regex:/^[a-zA-ZÑñ\s]+$/',
            'last_name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10',
            'country_id' => 'required',
        ]);
        if($validator->fails()){
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
        					'redirect'	=> ''
        			]
        		,200);
            }else{
            if (User::where('email', $request['email'])->where('status', '!=', '3')->exists()) :
                return response()->json(
                    [
                        'status'    => FALSE,
                        'message'   => trans('messages.44'),
                        'redirect'  => ''
                    ],
                    200
                );
            elseif (User::where('phone', $request['phone'])->where('status', '!=', '3')->exists()) :
                return response()->json(
                    [
                        'status'    => FALSE,
                        'message'   => trans('messages.43'),
                        'redirect'  => ''
                    ],
                    200
                );
            else :
                $otp = rand(100000, 999999);
                $user = User::create([
                    'first_name'          => $request['first_name'],
                    'last_name'          => $request['last_name'],
                    'email'         => $request['email'],
                    'phone'         => $request['phone'],
                    'password'      => Hash::make($otp),
                    'otp'       => $otp,
                    'country_id'    => $request['country_id'],
                    'image'         => "",
                    'email_validate' => 1,
                    'phone_validate' => 1,
                    'status' => 1,
                    'created_by' => 0

                ]);
                return response()->json(
                    [
                        'status'    => TRUE,
                        'message'   => trans('messages.22'),
                        'redirect'  => route('login')
                    ],
                    200
                );
            endif;
            }
    }
    public function webUserCheck(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:10',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
        					'redirect'	=> ''
        			]
        		,200);
        else:
            if (User::where('phone', $request['phone'])->where('status', '!=', '3')->exists()) {
               // $otp = rand(100001, 999999);
                $otp = "123456";
                $user = User::where('phone', $request['phone'])->where('status', '!=', '3')->first('id');
                $updateUser = User::where('id','=',$user->id)->update([
                    'otp'  => $otp ,
                    'password'      => Hash::make($otp),
        ]);
                if ($updateUser) {
                    $id = base64_encode($user->id . '||' . env('APP_KEY'));
                    return response()->json([
                        'status'	=> TRUE,
                        'message'	=> trans('messages.42'),
                        'redirect'	=> route('otp.verify',$id)
                ]
            ,200);
                } else {
                    return response()->json([
                        'status'	=> FALSE,
                        'message'	=> trans('messages.41'),
                        'redirect'	=> ''
                ]
            ,200);
                }

            }else{
                return response()->json([
                    'status'	=> FALSE,
                    'message'	=> trans('messages.40'),
                    'redirect'	=> ''
            ]
        ,200);
            }
        endif;
    }
    public function forgotPassword(Request $request)
    {
        $otp = rand(1001, 9999);
        $checkExists = User::select('id','name','email','phone','email_validate','phone_validate','status')->where('role_id','=','1')->first();
        if(!is_null($checkExists)):
            $mailDetails = [
                'otp'       => $otp,
                'subject'   => 'Forgot Password OTP !',
                'html'      => 'emails.super-admin-forgot-password',
                'userName'  =>  $checkExists->name
            ];
            Mail::to($checkExists->email)->send(new Mailer($mailDetails));
            User::where('id',$checkExists->id)->update([
                'otp'=>$otp
            ]);
            if($request->input('flag') == 1):
                return response()->json([
                        'status'    => TRUE,
                        'message'   => 'New OTP Has Been Sent to Your Mail !!',
                        'redirect'  => 'validate-otp'
                ],200);
            endif;
                return response()->json([
                        'status'    => TRUE,
                        'message'   => 'OTP Has Been Sent to Your Mail !!',
                        'redirect'  => 'validate-otp'
                ]
            ,200);
        endif;
    }
    public function otpExpire(Request $request)
    {
        if(Auth::guard('Admin')->check()):
            return redirect()->route('admin.dashboard');
        endif;
        User::where('id','=','1')->where('role_id','=','1')->update([
                                'otp'  => null

                    ]);
        return response()->json([
                                'status'    => TRUE,
                                'message'   => 'OTP Expired!!',
                                'redirect'  => ''
                        ]
                    ,200);
    }
    public function validateOtp(Request $request)
    {
        if(Auth::guard('Admin')->check()):
            return redirect('dashboard');
        endif;
        if($request->isMethod('post')):
            $requestOtp = $request->input('otp_first_degit').$request->input('otp_second_degit').$request->input('otp_third_degit').$request->input('otp_fourth_degit');
            if($requestOtp==''):
                if($request->input('new_pass') != $request->input('cnf_password')):
                     return response()->json([
                                'status'    => FALSE,
                                'message'   => 'New Password & Confirm Password Does not Match!!',
                                'redirect'  => ''
                        ]
                    ,200);
                endif;
                if(User::where('role_id','=','1')->where('otp',null)->exists()):
                    User::where('role_id','=','1')->where('otp',null)->update([
                        'password'=>Hash::make($request->input('new_pass'))
                    ]);
                    return response()->json([
                                'status'    => TRUE,
                                'message'   => 'Password Updated succesfully!!',
                                'redirect'  => 'login'
                        ]
                    ,200);
                endif;
                return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Something Went Wrong !!',
                            'redirect'  => ''
                    ]
                ,500);
            endif;
            $checkOTP = User::select('name','email','phone','email_validate','phone_validate','status')->where('role_id','=','1')->where('otp',$requestOtp)->first();
            if(is_null($checkOTP)):
                return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Invalid OTP !!',
                            'redirect'  => 'validate-otp'
                    ]
                ,200);
            else:
                User::where('id','=','1')->where('role_id','=','1')->update([
                            'otp'  => null

                ]);
                return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Otp validated !!',
                            'redirect'  => ''
                    ]
                ,200);
            endif;
        endif;
        $this->data['title']='Otp Verification';
        return view('pages.send-otp')->with($this->data);
    }
    public function logout()
    {
    	Session::flush();
        Auth::guard('Admin')->logout();
        return redirect()->route('admin.login');
    }
    public function userLogout()
    {
    	Session::flush();
        Auth::guard('web')->logout();
        return redirect()->route('index');
    }
}
