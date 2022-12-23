<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;
use App\Models\Country;
use App\Models\User;
class HomeController extends Controller
{
    public function index()    {
        Session::put('page','home');
        return view('web.index');
    }
    public function login(){
        Session::put('page','login');
        return view('web.login');
    }
    public function signUp(){
        Session::put('page','sign-up');
        $countryList = Country::get();
        return view('web.sign_up',compact('countryList'));
    }
    public function aboutUs(){
        Session::put('page','about-us');
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
        Session::put('page','contact-us');
        return view('web.contact_us');
    }
}
