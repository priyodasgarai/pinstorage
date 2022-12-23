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
                    
                    <div class="row">
                        <div class="col-md-4">
                            Order ID : {{$orderDetails->order_prefix_id}}
                        </div> 
                        <div class="col-md-4">
                           
                        </div> 
                        <div class="col-md-4 text-right">
                             User Info<br>
                            {{$orderDetails->user->name}}<br>
                            {{$orderDetails->user->email}}<br>
                            {{$orderDetails->user->phone}}<br>
                            {{$orderDetails->user->name}}<br>
                        </div> 
                    </div>
                     <div class="row">
                        <div class="col-md-4">
                            Product Design : <br>
                           Title : {{ $orderDetails->productDesign->title }}<br>
                           Price : {{ $orderDetails->price }}<br>
                           Quantity : {{ $orderDetails->quantity }}<br>
                           Stiching Info : {{ ($orderDetails->stiching_info != '') ? $orderDetails->stiching_info : '' }}<br>
                          
                            
                        </div> 
                        <div class="col-md-4">
                            @if(isset($orderDetails->measurement) && !empty($orderDetails->measurement))
                          @foreach($orderDetails->measurement as $key)
                            {{$key->label}} : {{$key->value}}<br>
                          @endforeach
                          @endif
                        </div> 
                        <div class="col-md-4 text-left">
                          @if(is_object($designImage) && !empty($designImage->file_name))
                            <img src="{{url('/').'/uploads/productDesignImages/'.$designImage->file_name}}" width="180px" height="180px">
                          @endif
                        </div> 
                    </div>
                    
                </div>
            </div>
        </div>   
    </div>
</div>

@endsection
