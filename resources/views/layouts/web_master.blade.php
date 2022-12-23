<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/web/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/stellarnav.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/web/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/web/css/sweetalert2.min.css') }}">
    <link href="{{ asset('assets/admin/css/jquery-confirm.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    @stack('css')
    <title>Pin Storage</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/web/images/fav-icon.png') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="main-nav sticky">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-6">
                    <div class="logo"> <a class="navbar-brand" href="index.html"> <img class="logo img-fluid"
                                src="{{ asset('assets/web/images/logo.png') }}" alt="logo" /> </a> </div>
                </div>
                <div class="col-lg-7 col-md-6 col-6">
                    <div id="main-nav" class="stellarnav">
                        <ul>
                            <li><a href="{{ route('index') }}">HOME</a></li>
                            <li><a href="{{ route('about-us') }}" class="active">ABOUT US</a></li>
                            <li><a href="{{ route('contact.us') }}">CONTACT US</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-3 col-12">
                    @if(Auth::guard('web')->check())
                    <div class="login"> <a href="{{ route('user.logout') }}">LOG OUT </a> </div>
                    @else
                    <div class="login"> <a href="{{ route('login') }}">LOG IN </a>/ <a href="{{ route('sign-up') }}">SIGN UP</a> </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @yield('content')





    <div class="footer ptb-100 bg-f9faff">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4 col-12">
                    <div class="footer-logo-area"> <img class="black-logo" src="assets/images/footer-logo.png"
                            alt="logo">
                        <div class="footer-content-card">
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4 col-12">
                    <div class="footer-links footer-quick-links">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="{{ route('index') }}" target="_blank"> Home </a></li>
                            <li><a href="{{ route('about-us') }}" target="_blank"> About Us </a></li>
                            <li><a href="{{ route('contact.us') }}" target="_blank"> Contact Us </a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4 col-12">
                    <div class="footer-links footer-quick-links">
                        <h3>Get In Touch</h3>
                        <p><span><i class="fa fa-envelope-o" aria-hidden="true"></i></span><a
                                href="mailto:bjstiles@gmail.com">bjstiles@gmail.com</a></p>
                        <div class="login2"> <a href="{{ route('login') }}">LOG IN </a>/ <a href="{{ route('sign-up') }}">SIGN UP</a> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="copyright-left">
                        <p>2022 © <a href="#">Pin Storage.</a> All Rights Reserved.</p>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-12">
                    <div class="copyright-right">
                        <p>Designed By <a href="#">Digital SFTware</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="go-top"><i class="fa fa-angle-double-up" aria-hidden="true"></i></div>
    <script src="{{ asset('assets/web/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/stellarnav.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/WOW.js') }}"></script>
    <script src="{{ asset('assets/web/js/custom.js') }}"></script>
    <script src="{{ asset('assets/web/js/sweetalert2.min.js') }}"></script>
    <script src="{{asset('assets/admin/js/admin-common.js')}}"></script>
    <script src="{{ asset('assets/admin/js/jquery-confirm.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/select2/select2.full.min.js') }}"></script>
    @stack('scripts')
</body>

</html>
