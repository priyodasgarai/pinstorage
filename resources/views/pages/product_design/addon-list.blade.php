@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Product Design Management</h2>
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
        <a href="{{url('product-design-management/addon-add/'.$addonId)}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Addon
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
	                        <th>Title</th>
                            <th>Price</th>
	                        <th>Created On</th>
	                        <th>Status</th>
	                        <th>Action</th>
	                    </tr>
	                    </thead>
	                    <tbody>
	                    	@if(count($addonList)>0)
	                    		@foreach ($addonList as $key => $value)
	                    			@php
	                    				if($value->status == 1):
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_design_addons" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
	                    				else:
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_design_addons" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
	                    				endif;
	                    			@endphp
		                    <tr>
		                        <td>{{($key+1)}}</td>
		                        <td>{{$value->productDesign->title}}</td>
                                <td>₹{{$value->custom_price}}</td>

		                        {{-- <td>{{$value->price}}</td> --}}
		                        {{-- @if($value->addon_image) --}}
                                {{-- <td><img src="{{ asset('uploads/productDesignImages') . '/' . $value->addon_image }}" id="bannerImg" alt="your image" width="80px" height="80" /></td>
                                @else
                                <td><img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="bannerImg" alt="your image" width="80" height="80" /></td>
                                @endif --}}
		                        <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
		                        <td>{!!$status!!}</td>
		                        <td>

		                        	<a href="{{url('product-design-management/addon-edit/'.$addonId.'/'.$value->id)}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
		                        	<a href="javascript:void(0)" id="{{$value->id}}" data-table="product_design_addons" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		                        </td>
		                    </tr>
	                    	@endforeach
	                    	@else
	                    	<tr>
	                    		<td colspan="5" align="center">No Data Available!</td>
	                    	</tr>
	                    	@endif
	                    </tbody>
	                </table>
	                <nav aria-label="Page navigation example">
                  		{{-- {!! $addonList->links() !!} --}}
           			</nav>

	            </div>
	        </div>
	    </div>
	</div>
</div>
@endsection
