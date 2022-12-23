@extends('layouts.master')
@section('content')
<style type="text/css">
    span.select2.select2-container {
    width: 100% !important;
}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Order Management</h2>
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
      <div class="title-action">
         <a href="{{url('orders/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Order List
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
                    <form role="form" data-action="orders/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="updateId" value="{{isset($details)?$details->id:''}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Order Id <sup>*</sup></label> 
                                    <input type="text" placeholder="Enter Order Id" data-check="Order Id" name="order_prefix_id" id="order_prefix_id" class="form-control requiredCheck" value="{{isset($details)?$details->order_prefix_id:''}}" readonly>
                                </div>
                            </div>

                            {{-- <div class="col-md-6">
                                <div class="form-group">
                                  <label>Select Order Status<sup>*</sup></label>
                                  <select class="form-control  select2 requiredCheck" name="status" id="status" data-check="Order Status">
                                    <option value="">-Choose Status-</option>
                                    <option value="1"{{(isset($details) && $details->status=='1')?'selected':''}}>Placed</option>
                                    <option value="0"{{(isset($details) && $details->status=='0')?'selected':''}}>Canceled</option>
                                    <option value="2"{{(isset($details) && $details->status=='2')?'selected':''}}>Out For Measurement</option>
                                    <option value="3"{{(isset($details) && $details->status=='3')?'selected':''}}>Arrived Tomorrow</option>
                                    <option value="4"{{(isset($details) && $details->status=='4')?'selected':''}}>Out For Delivery</option>
                                  </select>
                                </div>
                            </div>  --}}
                           {{--  @php
                              @if($settings->assignment_status==2)
                            @endphp --}}
                             <div class="col-md-6" style="{{($settings->assignment_status==2)?'display: block;':'display: none;'}}">
                                <div class="form-group">
                                  <label>Select Assignee Agent<sup>*</sup></label>
                                 <select class="form-control {{($settings->assignment_status==2)?'requiredCheck':''}}" name="user_id" id="user_id" data-check="Agent">
                                    <option value="">-Choose Agent-</option> 
                                      @if(count($agentList)>0)
                                        @foreach ($agentList as $key => $value)
                                      <option value="{{$value->id}}" {{(isset($orderAssignDetails) && $orderAssignDetails->user_id == $value->id)?'selected':''}}>
                                        {{$value->name}}
                                      </option>  
                                        @endforeach
                                      @else
                                      <option value="">No Agent Available!</option>
                                    @endif
                                  </select>
                                </div>
                            </div>                                        
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">{{(isset($details))?'Update':'Save'}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>
{{-- <script type="text/javascript">
    $(document).on('change','.isParentClass',function() {
        if($(this).val() == '1'){
            $('#parentMenuList').hide();
            $('#parent_id').removeClass('requiredCheck');
        }else{
            $('#parent_id').addClass('requiredCheck');
            $('#parentMenuList').show();
        }
    })
</script> --}}
@endsection
