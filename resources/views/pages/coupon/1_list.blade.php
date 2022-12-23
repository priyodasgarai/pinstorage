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
            <strong>{{$title}}</strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
      <div class="title-action">
         <a href="{{url('coupon-management/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Coupon
         </a>
      </div>
   </div>
</div>
<div class="wrapper wrapper-content">
	<div class="row">
	    <div class="col-lg-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title"> 
	            </div>
	            <div class="ibox-content">

	                <table class="table table-bordered">
	                    <thead>
	                    <tr>
	                        <th>#</th>
	                        <th>Coupon Code</th>
	                        <th>Coupon Discount</th>
	                        <th>Coupon Type</th>
	                        <th>User Limit</th>
	                        <th>Created On</th>
	                        <th>Status</th>
	                        <th>Start Date</th>
	                        <th>End Date</th>
	                        <th>Action</th>
	                    </tr>
	                    </thead>
	                    <tbody> 
	                    	@if(count($couponList)>0)
	                    		@foreach ($couponList as $key => $value)
	                    			@php
	                    				if($value->status == 1):
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="coupons" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
	                    				else:
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="coupons" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
	                    				endif;
	                    			@endphp

		                    <tr>
		                        <td>{{($key+1)}}</td>
		                        <td>{{$value->coupon_code}}</td>
		                        <td>{{round(number_format($value->coupon_discount,2))}}</td>
		                        <td>{{$value->coupon_type}}</td>
		                        <td>{{$value->usage_limit_per_user}}</td>
		                        <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
		                        <td>{!!$status!!}</td>
		                        <td>{{$value->start_date}}</td>
		                        <td>{{$value->end_date}}</td>
		                        <td>
		                        	<a href="{{url('coupon-management/edit/'.$value->id)}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
		                        	<a href="javascript:void(0)" id="{{$value->id}}" data-table="coupons" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		                        </td>
		                    </tr>
	                    	@endforeach
	                    	@else 
	                    	<tr>
	                    		<td colspan="10" align="center">No Data Available!</td>
	                    	</tr>
	                    	@endif
	                    </tbody>
	                </table>
	                <nav aria-label="Page navigation example">
                  		{!! $couponList->links() !!}
           			</nav>

	            </div>
	        </div>
	    </div>   
	</div>
</div>
@endsection