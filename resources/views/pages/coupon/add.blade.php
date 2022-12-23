@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Coupon Management</h2>
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
         <a href="{{url('coupon-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
             List
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
                   <form role="form" data-action="coupon-management/list" id="adminFrm" method="POST">
                    @csrf
                        <input type="hidden" name="couponId" value="{{isset($data)?$data->id:''}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Coupon Code<sup>*</sup></label> 
                                    <input type="text" placeholder="Enter Coupon Code" data-check="Coupon Code" name="coupon_code" id="coupon_code" class="form-control requiredCheck" value="{{isset($data)?$data->coupon_code:couponCode()}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label>Select Coupon Type<sup>*</sup></label>
                                  <select class="form-control  select2 requiredCheck" name="coupon_type" id="coupon_type" data-check="Coupon Type">
                                    <option value="">-Choose Type-</option>
                                    <option value="%"{{(isset($data) && $data->coupon_type=='%')?'selected':''}}>%</option>
                                    <option value="flat"{{(isset($data) && $data->coupon_type=='flat')?'selected':''}}>flat</option>
                                  </select>
                            </div>
                            
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                    <label>No of Use/Per User<sup>*</sup></label><br> 
                                    <input type="number" placeholder="Enter Usage Per User" data-check="Usage Per User" name="usage_limit_per_user" id="usage_limit_per_user" class="form-control requiredCheck" value="{{isset($data)?$data->usage_limit_per_user:1}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                            </div>
                            
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                    <label>Discount<sup>*</sup></label> 
                                    <input type="text" placeholder="Enter Discount" data-check="Discount" name="coupon_discount" id="coupon_discount" class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($data)?$data->coupon_discount:''}}">
                            </div>
                          </div>
                          
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                    <label>Start Date<sup>*</sup></label> 
                                    <input type="date"  data-check="Start Date" name="start_date" id="start_date" class="form-control requiredCheck" value="{{isset($data)?$data->start_date:''}}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                    <label>End Date<sup>*</sup></label> 
                                    <input type="date" data-check="End Date" name="end_date" id="end_date" class="form-control requiredCheck" value="{{isset($data)?$data->end_date:''}}">
                            </div>
                          </div>
                        </div>
                            <button class="btn btn-primary" type="submit">{{(isset($data))?'Update':'Save'}}</button>
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>
@endsection
