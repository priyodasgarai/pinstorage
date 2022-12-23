@extends('layouts.web_master')

@section('content')
    <section class="login-inner">
        <div class="container">
            <div class="login-inner-box">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="login-inner-box-left">
                            <div class="login-inner-box-left-round"></div>
                            <div class="login-logo"></div>
                            <h2>LOGIN</h2>
                            <form  role="form" data-action={{ route("web-user-check") }} id="adminFrm" method="POST">
                                @csrf
                                <div class="row">
                                    <p class="enter-num">Enter Your Mobile Number</p>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <div class="form-group">
                                            <input type="number" name="phone" class="form-control"
                                                placeholder="Enter Your Phone No." id="phone" required=""
                                                data-error="Please enter your phone number">
                                            <div class="form-icon"><i class="fa fa-mobile" aria-hidden="true"></i></i>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-12">
                                            <button class="default-button disabled submit-now" type="submit"
                                                style="pointer-events: all; cursor: pointer;"><span>SEND OTP</span></button>
                                            <div id="msgSubmit" class="h6 text-center hidden"></div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <h4>Don't have account? <a href="{{ route("sign-up") }}">SIGN UP</a></h4>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-6">
                        <div class="login-inner-box-right"> </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
