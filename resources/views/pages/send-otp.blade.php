<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?=$title?></title>
      {{-- favicon --}}
      <link rel="icon" type="image/x-icon" href="{{asset('assets/admin/img/favicon.ico')}}">
      {{-- <link href="{{asset('assets/admin/img/favicon.ico')}}"> --}}
      <link href="{{asset('assets/admin/css/bootstrap.min.css')}}" rel="stylesheet">
      <link href="{{asset('assets/admin/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
      <link href="{{asset('assets/admin/css/animate.css')}}" rel="stylesheet">
      <link href="{{asset('assets/admin/css/style.css')}}" rel="stylesheet">
      <link href="{{asset('assets/admin/css/jquery-confirm.css')}}" rel="stylesheet">

      <script src="{{asset('assets/admin/js/jquery-3.1.1.min.js')}}"></script>
      <script src="{{asset('assets/admin/js/jquery-confirm.js')}}"></script>
      
      <script src="{{asset('assets/admin/js/admin-common.js')}}"></script>
      

      <script type="text/javascript">
         let baseUrl = "{{url('/')}}/";
      </script>
   </head>
   <body class="gray-bg">
       <div class="container">
      <div class="loginColumns animated fadeInDown">
         <div class="row">
            <div class="col-md-6 text-center">
                <div class="login-logo"
                   <a href="#" style="text-align: center;">
                  <img src="{{asset('assets/images/logo.png')}}">
               </a></div>
               <h2 class="font-bold">Welcome to {{env('APP_NAME')}}</h2>
            
              {{--  <p>
                  When an unknown printer took a galley of type and scrambled it to make a type specimen book.
               </p> --}}
               <p>
                It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.
               </p>
            </div>
            <div class="col-md-6">
               <div class="ibox-content">
                  <form class="m-t" role="form" data-action="validate-otp" id="adminFrm1" method="POST">
                     @csrf
                     <div id="password-section">
                        
                     </div>
                     <div class="userInput" id="otp-section">
                        <input class="c requiredCheck" name="otp_first_degit" type="text" id='ist' maxlength="1" onkeyup="clickEvent(this,'sec')" data-check="OTP" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        <input class="c requiredCheck" type="text" name="otp_second_degit" id="sec" maxlength="1" onkeyup="clickEvent(this,'third')" onkeydown ="backspaceEvent('ist',this)" data-check="OTP" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        <input class="c requiredCheck" type="text" name="otp_third_degit" id="third" maxlength="1" onkeyup="clickEvent(this,'fourth')" onkeydown ="backspaceEvent('sec',this)" data-check="OTP" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        <input class="c requiredCheck" type="text" name="otp_fourth_degit" id="fourth" maxlength="1" onkeydown ="backspaceEvent('third',this)" data-check="OTP" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                     </div>


                     <button type="submit" class="btn btn-primary block full-width m-b">Submit</button>
                     <div id="otp-section-buttons">
                        <p><b>OTP will expire in <span id="timer"></span></b></p>
                        <a href="javascript:void(0)" id="resend-otp">
                        <!--<strong>Resend OTP</strong>-->
                        </a>
                     </div>
                  </form>
                  <p class="m-t">
                     <a href="{{url('login')}}">Back To Login</a>
                  </p>
               </div>
            </div>
         </div>
         </div>
         </div>
        <!-- <hr/>-->
         <div class="footerbt">
        <div class="container">
         <div class="row">
            <div class="col-lg-12 col-md-12 col-12 text-center">
                Copyright © 2022 WahTailor.com. All Right Reserved.
            </div>
         </div>
         </div>
         </div>
         </div>
         <!--<div class="row">
            <div class="col-md-6">
               Copyright Example Company
            </div>
            <div class="col-md-6 text-right">
               <small>© 2014-2015</small>
            </div>
         </div>-->
      </div>
      <script type="text/javascript">
      let Token = "{{ csrf_token() }}";
     
      </script>
      <script src="{{asset('assets/admin/js/admin-pas.js')}}"></script>
   </body>
</html>