@extends('layouts.web_master')

@section('content')
    <section>
        <div class="banner-inner">
            <img src="{{ asset('assets/web/images/about-banner.png')}}" alt="">
            <div class="banner-inner-content">
                <ul class="breadcrumb-item-content mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
                </ul>
                <h1>Contact Us</h1>
            </div>
        </div>
    </section>

    <div class="contact-inner-page">
        <div class="shp2"><img src="{{ asset('assets/web/images/shp2.png')}}" alt=""></div>
        <div class="shp3"><img src="{{ asset('assets/web/images/shp3.png')}}" alt=""></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="conact-us-wrap-one mb-30">
                        <h2>Contact For Any Query</h2>
                        <div class="sub-heading">Filler text is text that shares some characteristics of a real written
                            text, but is random or otherwise generated. It may be used to display a sample of fonts,
                            generate text for testing, </div>
                        <h6> <span><i class="fa fa-map-marker" aria-hidden="true"></i></span> Filler text is text that
                            shares some characteristics of a real written text, </h6>
                        <h6><span><i class="fa fa-envelope-o" aria-hidden="true"></i></span> <a
                                href="mailto:bjstiles@gmail.com">bjstiles@gmail.com</a></h6>
                        <h6> <span><i class="fa fa-phone" aria-hidden="true"></i></span> +0399 78214 87965 </h6>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="contact-form-wrap">
                        <form id="contact-form" action="" method="post">
                            <div class="contact-form">
                                <div class="contact-input">
                                    <div class="contact-inner no-padding">
                                        <input name="con_name" type="text" placeholder="Name *">
                                    </div>
                                    <div class="contact-inner no-padding">
                                        <input name="con_email" type="email" placeholder="Email *">
                                    </div>
                                </div>
                                <div class="contact-inner">
                                    <input name="con_subject" type="text" placeholder="Subject *">
                                </div>
                                <div class="contact-inner contact-message">
                                    <textarea name="con_message" placeholder="Please describe what you need."></textarea>
                                </div>
                                <div class="submit-btn mt-20">
                                    <button class="default-button more-info" type="submit"><span>Send
                                            Message</span></button>
                                    <p class="form-messege"></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
