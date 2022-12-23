@extends('layouts.web_master')

@section('content')
    <section>
        <div class="banner">
            <div class="banner-slider-area owl-carousel owl-loaded owl-drag">
                <div class="owl-stage-outer">
                    <div class="owl-stage">
                        <div class="owl-item"> <img src="{{ asset('assets/web/images/banner.png')}}" alt="banner">
                            <div class="banner-inner-text">
                                <h1>SAVE YOUR PIN AND PASSWORD</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque congue tempus sapien, ac
                                    dignissim tellus facilisis vitae. Sed id sapien sed eros porttitor consectetur in id
                                    sem.</p>
                                <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a>
                            </div>
                        </div>
                        <div class="owl-item"> <img src="{{ asset('assets/web/images/banner.png')}}" alt="banner">
                            <div class="banner-inner-text">
                                <h1>SAVE YOUR PIN AND PASSWORD</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque congue tempus sapien, ac
                                    dignissim tellus facilisis vitae. Sed id sapien sed eros porttitor consectetur in id
                                    sem.</p>
                                <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a>
                            </div>
                        </div>
                        <div class="owl-item"> <img src="{{ asset('assets/web/images/banner.png')}}" alt="banner">
                            <div class="banner-inner-text">
                                <h1>SAVE YOUR PIN AND PASSWORD</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque congue tempus sapien, ac
                                    dignissim tellus facilisis vitae. Sed id sapien sed eros porttitor consectetur in id
                                    sem.</p>
                                <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a>
                            </div>
                        </div>
                        <div class="owl-item"> <img src="assets/images/banner.png')}}" alt="banner">
                            <div class="banner-inner-text">
                                <h1>SAVE YOUR PIN AND PASSWORD</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque congue tempus sapien, ac
                                    dignissim tellus facilisis vitae. Sed id sapien sed eros porttitor consectetur in id
                                    sem.</p>
                                <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a>
                            </div>
                        </div>
                        <div class="owl-item"> <img src="{{ asset('assets/web/images/banner.png')}}" alt="banner">
                            <div class="banner-inner-text">
                                <h1>SAVE YOUR PIN AND PASSWORD</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque congue tempus sapien, ac
                                    dignissim tellus facilisis vitae. Sed id sapien sed eros porttitor consectetur in id
                                    sem.</p>
                                <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="owl-dots">
                    <button role="button" class="owl-dot active"><span></span></button>
                    <button role="button" class="owl-dot"><span></span></button>
                    <button role="button" class="owl-dot"><span></span></button>
                    <button role="button" class="owl-dot"><span></span></button>
                </div>
            </div>
        </div>
    </section>

<div class="about">
    <div class="shp1"><img src="{{ asset('assets/web/images/shp1.png')}}" alt=""></div>
    <div class="shp2"><img src="{{ asset('assets/web/images/shp2.png')}}" alt=""></div>
    <div class="shp3"><img src="{{ asset('assets/web/images/shp3.png')}}" alt=""></div>
    <div class="container">
      <div class="row">
        <div class="col-lg-5 col-sm-5 col-12">
          <div class="about-inner-left wow animate fadeInLeft"> <img src="{{ asset('assets/web/images/image-removebg-preview.png')}}" alt=""> </div>
        </div>
        <div class="col-lg-5 col-sm-5 col-12">
          <div class="about-inner-mid wow animate fadeInDown">
            <h2>ABOUT PIN STORAGE</h2>
            <p>Sed ut erat id massa lobortis hendrerit non in arcu. Cras varius vitae elit a ullamcorper. Proin porta massa et placerat vehicula. Proin a erat pulvinar magna viverra dignissim. Morbi molestie sodales arcu vitae dapibus. Cras vel urna dui.</p>
            <p>Sed viverra nulla eu erat consequat, vel eleifend justo tempor. Vivamus sed ultrices eros. Pellentesque tempor aliquet urna, in luctus arcu porttitor ut. Aenean facilisis augue mi, at tincidunt massa dapibus nec.</p>
            <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a> </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-12">
          <div class="about-inner-right wow animate fadeInRight"> <img src="{{ asset('assets/web/images/mobile2.png')}}" alt=""> </div>
        </div>
      </div>
    </div>
  </div>
  <div class="safely-store">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-sm-6 col-12">
          <div class="safely-store-left wow animate fadeInLeft">
            <h2>SAFELY STORE YOUR PIN</h2>
            <p>Whether you wish to store PIN numbers for your EC or credit cards, e-mail account passwords, or other sensitive data e.g. for shopping portals etc., Pin Storage is the secure alternative for those who are sceptical about conventional online and cloud solutions.</p>
          </div>
          <div class="safely-store-left-inner wow animate fadeInLeft">
            <h4> <span><img src="{{ asset('assets/web/images/thumbtacks.png')}}" alt=""></span> Have Complete Control Over Device Passwords!</h4>
            <h4> <span><img src="{{ asset('assets/web/images/thumbtacks.png')}}" alt=""></span> Create And Safely Store Account Details.</h4>
            <h4> <span><img src="{{ asset('assets/web/images/thumbtacks.png')}}" alt=""></span> Safely Store Your Confidential Pin Codes.</h4>
            <h4> <span><img src="{{ asset('assets/web/images/thumbtacks.png')}}" alt=""></span> Secure Your Digital Life.</h4>
            <a class="default-button more-info" href="#"><span>DISCOVER MORE</span></a> </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-12">
          <div class="safely-store-right wow animate fadeInRight"> <img src="{{ asset('assets/web/images/banner-small.png')}}" alt=""> </div>
        </div>
      </div>
    </div>
  </div>
@endsection
