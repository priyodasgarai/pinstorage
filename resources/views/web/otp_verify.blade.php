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
                            <h2><span>OTP</span> VERIFICATION</h2>
                            <form  role="form" data-action={{ route("user.post.login") }} id="adminFrm" method="POST">
                                @csrf

                                <div class="row">
                                    <p class="enter-num">Enter the OTP you receive to <span>{{ $user->phone }}</span></p>
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <div class="row otp-box">
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-12">
                                            <div class="form-group">
                                                <input type="password" class="form-control" name="otp[]"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <h5>RESEND OTP</h5>
                                    </div>
                                    <div class="col-md-12 col-sm-12 col-12">
                                        <button class="default-button disabled submit-now" type="submit"
                                            style="pointer-events: all; cursor: pointer;"><span>SUBMIT</span></button>
                                        <div id="" class="h6 text-center hidden"></div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </form>
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
