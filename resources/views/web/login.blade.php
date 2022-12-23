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
                            <h2><span>THANK YOU FOR</span> <a href="#">SIGN UP</a></h2>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <a href="{{ route('send-otp') }}">
                                        <button class="default-button disabled submit-now" type="submit"
                                            style="pointer-events: all; cursor: pointer;"><span>LOGIN</span></button>
                                        </a>
                                            <div id="msgSubmit" class="h6 text-center hidden"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>

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
