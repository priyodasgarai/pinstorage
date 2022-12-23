<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?=$title?></title>
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
      <div class="loginColumns animated fadeInDown">
         <div class="row">
            <div class="col-md-6">
               <h2 class="font-bold">Welcome to {{env('APP_NAME')}}</h2>
               <p>
                  Perfectly designed and precisely prepared admin theme with over 50 pages with extra new web app views.
               </p>
               <p>
                  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
               </p>
               <p>
                  When an unknown printer took a galley of type and scrambled it to make a type specimen book.
               </p>
               <p>
                  <small>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</small>
               </p>
            </div>
            <div class="col-md-6">
               <div class="ibox-content">
                  <form class="m-t" role="form" data-action="forgot-password" id="adminFrm" method="POST">
                     @csrf
                     <div class="form-group">
                        <input type="text" name="email" id="email" class="form-control requiredCheck" data-check="Email" placeholder="Email" >
                     </div>
                     <button type="submit" class="btn btn-primary block full-width m-b">Send</button>
                     <a href="{{url('login')}}">
                     <small>Back To Login</small>
                     </a>
                  </form>
                  <p class="m-t">
                     <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small>
                  </p>
               </div>
            </div>
         </div>
         <hr/>
         <div class="row">
            <div class="col-md-6">
               Copyright Example Company
            </div>
            <div class="col-md-6 text-right">
               <small>© 2014-2015</small>
            </div>
         </div>
      </div>
   </body>
</html>