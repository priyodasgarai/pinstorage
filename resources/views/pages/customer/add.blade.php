@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Customer Management</h2>
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
      <div class="title-action">
         <a href="{{route('admin.customer-management.list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Customer List
         </a>
      </div>
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
                    <form role="form" data-action="{{route('admin.customer-management.list')}}" id="adminFrm" method="POST">
                      @csrf
                        <input type="hidden" name="id" value="{{isset($data)?$data->id:''}}">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name<sup>*</sup></label>
                                    <input type="text" placeholder="Enter First Name" data-check="First Name" name="first_name" id="first_name" class="form-control requiredCheck" value="{{isset($data)?$data->first_name:''}}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Last Name" data-check="Last Name" name="last_name" id="last_name" class="form-control requiredCheck" value="{{isset($data)?$data->last_name:''}}">
                                </div>
                            </div>
                             <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email<sup>*</sup></label>
                                    <input type="email" placeholder="Enter Email" data-check="Email" name="email" id="email" class="form-control requiredCheck" value="{{isset($data)?$data->email:''}}">
                                </div>
                            </div>

                          </div>


                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone <sup>*</sup></label>
                                    <input type="text" placeholder="Enter Phone" data-check="Phone" name="phone" id="phone" class="form-control requiredCheck" value="{{isset($data)?$data->phone:''}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                </div>
                            </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Select Country<sup>*</sup></label>
                              <select class="form-control requiredCheck" name="country_id" id="country_id" data-check="Country">
                                <option value="">-Choose Country-</option>
                                 @if(isset($countryList))
                                  @foreach ($countryList as $key => $value)
                                  <option value="{{$value->id}}" {{(isset($data) && $data->country_id == $value->id)?'selected':''}}>
                                  {{$value->name}}
                                  </option>
                                   @endforeach
                                  @else
                                  <option value="">No Country Available!</option>
                                @endif
                              </select>
                            </div>
                        </div>
                          <div class="col-md-4">
                            <div class="form-group">
                                <label>Profile Image</label>
                                <input type="file" name="image" id="image" class="" data-check="Profile Image" onchange="document.getElementById('custImg').src = window.URL.createObjectURL(this.files[0])"><br>
                                  @if(isset($data) && checkFileDirectory($data->image,'uploads/userImages'))
                                    <img src="{{ asset('uploads/userImages/'.$data->image) }}" height="100px" width="150px" id="custImg" />
                                  @else
                                      <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="custImg" height="100px" width="150px" id="custImg" />
                                  @endif
                            </div>
                            @if(isset($data))
                                <input type="hidden" name="old_file" value="{{$data->image}}">
                            @endif
                          </div>
                        </div>
                            <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
// $(document).ready(function() {
// $('#country_id').on('change', function() {
// var country_id = this.value;
// $("#state_id").html('');
// $.ajax({
// url:baseUrl+'customer-management/get-states-by-customer-country',
// type: "POST",
// data: {
// country_id: country_id,
// _token : _token
// },
// dataType : 'json',
// success: function(result){
// $('#state_id').html('<option value="">Select State</option>');
// $.each(result.states,function(key,value){
// $("#state_id").append('<option value="'+value.id+'">'+value.name+'</option>');
// });
// $('#city_id').html('<option value="">Select State First</option>');
// }
// });
// });
// $('#state_id').on('change', function() {
// var state_id = this.value;
// $("#city_id").html('');
// $.ajax({
// url:baseUrl+'customer-management/get-cities-by-customer-state',
// type: "POST",
// data: {
// state_id: state_id,
// _token : _token
// },
// dataType : 'json',
// success: function(result){
// $('#city_id').html('<option value="">Select City</option>');
// $.each(result.cities,function(key,value){
// $("#city_id").append('<option value="'+value.id+'">'+value.name+'</option>');
// });
// }
// });
// });
// });
</script>
@endsection
