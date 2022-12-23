@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>App Settings</h2>
      <ol class="breadcrumb">
         <li>
            <a href="{{url('dashboard')}}">Dashboard</a>
         </li>
         <li class="active">
            <strong>{{$title}}</strong>
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
                    <form role="form" data-action="app-settings" id="adminFrm" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company Mail</label> 
                                     <input type="email" placeholder="Enter Email" data-check="Email" name="email" id="email" class="form-control" value="{{$appSettings->company_mail}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Company Phone</label> 
                                   <input type="text" placeholder="Enter Phone" data-check="Phone" name="phone" id="phone" class="form-control" value="{{$appSettings->company_phone}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                </div>
                            </div>
                           {{--  <div class="col-md-4">
                              <div class="form-group">
                                    <label>Rezorpay Sandbox Publishable Key</label> 
                                    <input type="text" name="rezorpay_sandbox_publishable_key" id="rezorpay_sandbox_publishable_key" class="form-control " placeholder="Rezorpay Sandbox Publishable Key" data-check="Rezorpay Sandbox Publishable Key" value="{{$appSettings->rezorpay_sandbox_publishable_key}}">
                              </div>
                            </div>
                             --}}
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Lining Cost<sup>*</sup></label> 
                                <input type="text" placeholder="Enter Price" data-check="Lining Cost" name="lining_cost" id="lining_cost"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{$appSettings->lining_cost}}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Order Prefix<sup>*</sup></label> 
                                <input type="text" placeholder="Enter Prefix" data-check="Order Prefix" name="order_id_prefix" id="order_id_prefix"  class="form-control requiredCheck"  value="{{$appSettings->order_id_prefix}}">
                            </div>
                          </div>
                        </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label>Alteration Cost<sup>*</sup></label>
                                  <input type="text" placeholder="Enter Price" data-check="Lining Cost" name="alteration_cost" id="alteration_cost"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{$appSettings->alteration_cost}}">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label>Padded Cost<sup>*</sup></label>
                                  <input type="text" placeholder="Enter Price" data-check="Padded Cost" name="padded_cost" id="padded_cost"  class="form-control requiredCheck"  value="{{$appSettings->padded_cost}}">
                              </div>
                            </div>
                          </div>
                        <div class="row">
                          <div class="col-md-6">
                                <div class="form-group">
                                <label>Delivery Agent Assignment Process<sup>*</sup></label>
                                <br>
                                <label>
                                  <input type="radio" name="assignment_status" value="1" {{(isset($appSettings) && $appSettings->assignment_status == '1')?'checked':''}} class="requiredCheck">  
                                  Auto
                                </label>
                                <label>
                                  <input type="radio" name="assignment_status" value="2" class="requiredCheck" {{(isset($appSettings) && $appSettings->assignment_status == '2')?'checked':''}}> Manual
                                </label>
                              </div> 
                            </div>
                            
                          <div class="col-md-6">
                            
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