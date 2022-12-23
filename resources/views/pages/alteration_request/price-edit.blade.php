@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Alteration Request</h2>
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
        <a href="{{url('customer-alteration-request/list')}}" class="btn btn-primary"><i class="fa fa-arrow-left"></i>
         Back
        </a>
     </div>
   </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    @if(isset($data->order))
                    <h5>Order ID : {{  $data->order->order_prefix_id}}</h5>
                    @endif
                </div>
                <div class="ibox-content">
                    <form role="form" data-action="customer-alteration-request/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="alterationId" value="{{isset($data)?$data->id:''}}">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Alteration Cost<sup>*</sup></label> 
                                <input type="text" placeholder="Enter Price" data-check="Alteration Cost" name="alteration_price" id="alteration_price"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($data)?$data->alteration_price:''}}">
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
{{-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">

   $('.decimal').keyup(function(){
    var val = $(this).val();
    if(isNaN(val)){
         val = val.replace(/[^0-9\.]/g,'');
         if(val.split('.').length>2) 
             val =val.replace(/\.+$/,"");
    }
    $(this).val(val); 
});
</script> --}}