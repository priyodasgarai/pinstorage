@extends('layouts.master')

@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Profile Settings</h2>
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
                    <form role="form" data-action="{{route('admin.profile-update')}}" id="adminFrm" method="POST">
                      @csrf
                      <input type="hidden" name="profileId" value="{{$userDetails->id}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label>Name</label>
                                  <input type="text" name="name" id="name" class="form-control requiredCheck" placeholder="Name"  value="{{$userDetails->name}}" data-check="Name">
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" id="email" class="form-control requiredCheck" placeholder="Email" readonly  value="{{$userDetails->email}}" data-check="Email">
                              </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                    <label>Phone</label>
                                    <input type="number" name="phone" id="phone" class="form-control requiredCheck" placeholder="Phone Number" value="{{$userDetails->phone}}" data-check="Phone">
                              </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                  <label>Image</label>
                                  <input type="file" name="image" id="image" class="form-control " placeholder="image"  value="" data-check="image">
                            </div>
                          </div>
                        </div>

                            <button class="btn btn-primary" type="submit">Update</button>
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
