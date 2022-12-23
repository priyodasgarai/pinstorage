@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Customer Management</h2>
      <ol class="breadcrumb">
         <li>
            <a href="{{url('dashboard')}}">Dashboard</a>
         </li>
         <li class="active">
            <strong><?=$title?></strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
      <div class="title-action">
         <a href="{{url('customer-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Change Password
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
                    <form role="form" data-action="delivery-agent/change-password" id="adminFrm" method="POST">
                      @csrf
                        <input type="hidden" name="id" value="{{isset($id)?$id:''}}">

                          <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>Password <sup>*</sup></label>
                                    <input type="password" placeholder="Enter Password" data-check="Password" name="password" id="password" class="form-control  requiredCheck " value="">
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label>Confirm Password <sup>*</sup></label>
                                  <input type="password" placeholder="Enter Confirmed Password" data-check="confirmed_password" name="confirmed_password" id="confirmed_password" class="form-control   requiredCheck " value="">
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
