<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\User;
use Hash;

class CouponManagement extends Controller {

    public function couponList(Request $request) {
        if ($request->isMethod('post')):
            $validator = Validator::make($request->all(), [
                        'coupon_code' => 'required',
                        'coupon_type' => 'required',
                        'usage_limit_per_user' => 'required',
                        'coupon_discount' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required'
            ]);
            if ($validator->fails()):
                return response()->json([
                            'status' => FALSE,
                            'message' => 'Please Input Valid Credentials',
                            'redirect' => ''
                                ]
                                , 200);
            else:
                if (is_null($request->input('couponId'))):
                    if (Coupon::whereRaw('LOWER(`coupon_code`) = "' . strtolower($request->input('coupon_code')) . '"')->where('status', '!=', '3')->exists()):
                        return response()->json([
                                    'status' => FALSE,
                                    'message' => 'Coupon Code Already Exist !!',
                                    'redirect' => ''
                                        ]
                                        , 200);
                    else:
                        Coupon::create([
                            'coupon_type' => $request->input('coupon_type'),
                            'coupon_code' => $request->input('coupon_code'),
                            'coupon_discount' => $request->input('coupon_discount'),
                            'usage_limit_per_user' => $request->input('usage_limit_per_user'),
                            'start_date' => $request->input('start_date'),
                            'end_date' => $request->input('end_date'),
                            'created_by' => Auth::user()->id
                        ]);
                        return response()->json([
                                    'status' => TRUE,
                                    'message' => 'Coupon Added Successfully !!',
                                    'redirect' => 'coupon-management/list'
                                        ]
                                        , 200);
                    endif;
                else:
                    if (Coupon::whereRaw('LOWER(`coupon_code`) = "' . strtolower($request->input('coupon_code')) . '"')->where('id', '<>', $request->input('couponId'))->where('status', '!=', '3')->exists()):
                        return response()->json([
                                    'status' => FALSE,
                                    'message' => 'Coupon Code Already Exist !!',
                                    'redirect' => ''
                                        ]
                                        , 200);
                    else:
                        Coupon::where('id', $request->input('couponId'))->update([
                            'coupon_type' => $request->input('coupon_type'),
                            'coupon_code' => $request->input('coupon_code'),
                            'coupon_discount' => $request->input('coupon_discount'),
                            'usage_limit_per_user' => $request->input('usage_limit_per_user'),
                            'start_date' => $request->input('start_date'),
                            'end_date' => $request->input('end_date'),
                            'updated_by' => Auth::user()->id,
                        ]);
                        return response()->json([
                                    'status' => TRUE,
                                    'message' => 'Coupon Updated Successfully !!',
                                    'redirect' => 'coupon-management/list'
                                        ]
                                        , 200);
                    endif;
                endif;
            endif;
        endif;
        $this->data['title'] = 'Coupon List';
        $this->data['couponList'] = Coupon::where('status', '!=', '3')->paginate(5);
        return view('pages.coupon.list')->with($this->data);
    }

    public function couponAdd($id = null) {
        if (!is_null($id)):
            $this->data['title'] = 'Coupon Edit';
            $this->data['data'] = Coupon::find($id);
        else:
            $this->data['title'] = 'Coupon Add';
        endif;
        return view('pages.coupon.add')->with($this->data);
    }

    public function couponAssign(Request $request, $id = null) {
        $this->data['title'] = 'Coupon Assign List';
        $user = array();
        $UserCoupon = UserCoupon::with(['users'])->where(['coupon_id' => $id])->get();
        // dd($UserCoupon);
        foreach ($UserCoupon as $val) {
            $user[] = $val->user_id;
        }
        $this->data['coupon_id'] = $id;
        $this->data['userlist'] = User::whereNotIn('id', $user)->where(['role_id' => 3])->get();
        $this->data['couponList'] = $UserCoupon;
        // dd($this->data);
        return view('pages.coupon.assign')->with($this->data);
    }

    public function couponAssignadd(Request $request) {
        if ($request->ajax()) {  
            $coupon = Coupon::find($request->coupon_id);
            if(!empty($coupon)){
            if (is_array($request->user_id)) {
                foreach ($request->user_id as $user_id) {
                    $UserCoupon = new UserCoupon();
                    $UserCoupon->coupon_id = $request->coupon_id;
                    $UserCoupon->user_id = $user_id;
                    $UserCoupon->usage_limit_per_user = $coupon->usage_limit_per_user;
                    $UserCoupon->expiry_date = $coupon->end_date;
                    $UserCoupon->save();
                }
                 return back();
            } else {
                return back();
            }
            }
        }
    }
     public function couponAssigndelete($id) {        
            $UserCoupon = UserCoupon::find($id);
            if(isset($UserCoupon)){
                $UserCoupon->delete();
                 return back();
            } else {
                return back();
            }
        
    }

}
