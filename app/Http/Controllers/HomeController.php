<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\User;
class HomeController extends Controller
{
    public function index()    {
        return view('web.index');
    }
    public function login(){
        return view('web.login');
    }
    public function signUp(){
        $countryList = Country::get();
        return view('web.sign_up',compact('countryList'));
    }
    public function aboutUs(){
        return view('web.about');
    }
    public function otpVerify($id){
        $val = explode("||", base64_decode($id));
        $user_id = $val[0];
        $user = User::find($user_id);
        return view('web.otp_verify',compact('user'));
    }
    public function sendOtp(){
        return view('web.send_otp');
    }
    public function contactUs(){
        return view('web.contact_us');
    }
}
