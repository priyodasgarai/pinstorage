@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Change password</h2>
      <ol class="breadcrumb">
         <li>
            <a href="{{route('admin.dashboard')}}">Dashboard</a>
         </li>
         <li class="active">
            <strong><?=$title?></strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
   </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <!-- <h5>Add Menu</h5> -->
                </div>
                <div class="ibox-content">
                    <form role="form" data-action="{{ route('admin.password-update') }}" id="adminFrm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Old Password</label>
                                     <input type="password" name="oldpassword" id="oldpassword" class="form-control requiredCheck" placeholder="Old Password"   data-check="Old Password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="newpassword" id="newpassword" class="form-control requiredCheck" placeholder="New Password" data-check="New Password">
                                </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" name="confirmpassword" id="confirmpassword" class="form-control requiredCheck" placeholder="Confirm Password"   data-check="Confirm Password">
                                </div>
                            </div>

                        </div>
                            <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<!-- <script type="text/javascript">
    $(document).on('change','.isParentClass',function() {
        if($(this).val() == '1'){
            $('#parentMenuList').hide();
        }else{
            $('#parent_id').addClass('requiredCheck');
            $('#parentMenuList').show();

        }
    })
</script> -->
