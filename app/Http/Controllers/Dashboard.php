<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller {

    public function index($value = '') {
        $this->data['title'] = "Dashboard";
        return view('pages.dashboard')->with($this->data);
    }
    public function changePassword()
	{
		$this->data['title']='Change-Password';
    	return view('pages.change-password')->with($this->data);
	}
	public function passwordUpdate(Request $request)
	{
		$validator = Validator::make($request->all(), [
            'oldpassword' => 'required',
            'newpassword' => 'required',
            'confirmpassword' => 'required',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> 'Please Input Valid Credentials',
       						'redirect'	=> ''
        			]
        		,200);
        else:
            if (Hash::check($request->get('newpassword'), Auth::guard('Admin')->user()->password)){
                return response()->json([
    					'status'	=> FALSE,
    					'message'	=> 'Your current password  matches with the old password!!',
   						'redirect'	=> ''
        			]
        		,200);
            }
        	if (Hash::check($request->get('oldpassword'), Auth::guard('Admin')->user()->password)):

        		if($request->get('newpassword')==$request->get('confirmpassword')):
        		 	$user = Auth::guard('Admin')->user();
			        $user->password = \Hash::make($request->get('newpassword'));
			        $user->save();
			        return response()->json([
        					'status'	=> TRUE,
        					'message'	=> 'Password Updated Successfully!!',
        					'redirect'	=> 'dashboard'
        			]
        		,200);
			    else:
			    	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> 'New Password & Confirm Password  missmatch!!',
       						'redirect'	=> ''
        			]
        		,200);
        		endif;
        	else:
        		return response()->json([
        					'status'	=> FALSE,
        					'message'	=> 'Your current password does not matches with the password!!',
       						'redirect'	=> ''
        			]
        		,200);
        	endif;
        	//$user = Auth::user();
        endif;
	}
    public function editProfile()
	{
		$this->data['title']='Profile';
        $this->data['userDetails'] = Auth::guard('Admin')->user();
    	return view('pages.profile')->with($this->data);
	}
    public function profileUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);
        if($validator->fails()):
            return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Please Input Valid Credentials',
                            'redirect'  => ''
                    ]
                ,200);
        else:
            $this->fileName = "";
            if ($request->hasFile('image')) :
                $image = $request->file('image');
                    $this->fileName = time() . '.' . $image->getClientOriginalName();
                    $image->move(public_path('uploads/'), $this->fileName);
            endif;
                $user = Auth::guard('Admin')->user();
                $user->name=$request->get('name');
                $user->email=$request->get('email');
                $user->phone=$request->get('phone');
                $user->image = $this->fileName;
                $user->save();
                return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Profile Updated Successfully!!',
                            'redirect'  => 'dashboard'
                    ]
                ,200);
        endif;
    }

}
