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
                The business is a web based Pin storage for your family and friends to store mobile phone,
Ipda and any pins details you may have for your friends and or family.
               </p>
            </div>
            <div class="col-md-6">
               <div class="ibox-content">
                  <form class="m-t" role="form" data-action={{ route("admin.user-check") }} id="adminFrm" method="POST">
                     @csrf
                     <div class="form-group">
                        <input type="email" name="email" id="email" class="form-control requiredCheck" data-check="Email" placeholder="Email" >
                     </div>
                     <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control requiredCheck" data-check="Password" placeholder="Password" >
                     </div>
                     <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                     <a href="javascript:void(0)" id="forgot-a-password">
                     Forgot password?
                     </a>
                  </form>
                  <p class="m-t">
                   Inspinia we app framework base on Bootstrap 3 &copy; 2014
                  </p>
               </div>
            </div>
         </div>
         </div>
         </div>



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
   <script type="text/javascript">
      $(document).on('click','#forgot-a-password',function() {
         let jc;
         let Token = "{{ csrf_token() }}";
         $.ajax({
               type: "POST",
               url: baseUrl+"forgot-password",// where you wanna post
               data: {_token:Token,flag:'0'},
               beforeSend: function() {
                  jc = $.dialog({
                      icon: 'fa fa-spinner fa-spin',
                      title: 'Working!',
                      content: 'Sit back, we are processing your request!',
                      type: 'dark',
                      closeIcon: false

                  });
               },
               success: function(data) {
                  jc.close();
                  if(data.status){
                     $.alert({
                         icon: 'fa fa-check',
                         title: 'Success!',
                         content: data.message,
                        type: 'green',
                        typeAnimated: true,
                     });
                     if(data.redirect != ''){
                        setTimeout(function(){ window.location.href=baseUrl+data.redirect }, 3000);
                     }
                  }
               }
         });
      });
   </script>
   </body>
</html>
