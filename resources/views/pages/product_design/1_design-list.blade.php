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
         <a href="{{url('product-design-management/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Product Design
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
	                        <th>Category Name</th>
	                        <th>Title</th>
	                        <th>Price</th>
	                        <th>Image</th>
	                        <th>Created On</th>
	                        <th>Status</th>
	                        <th>Action</th>
	                    </tr>
	                    </thead>
	                    <tbody> 
	                    	@if(count($productDesignList)>0)
	                    		@foreach ($productDesignList as $key => $value)
	                    			@php
	                    				if($value->status == 1):
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_designs" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
	                    				else:
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_designs" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
	                    				endif;
	                    			@endphp
		                    <tr>
		                        <td>{{($key+1)}}</td>
		                        <td>{{$value->categoryName}}</td>
		                        <td>{{$value->title}}</td>
		                        <td>₹{{$value->price}}</td>
		                       	@if($value->image && checkFileDirectory($value->image,'uploads/productDesignImages'))
                                <td><img src="{{ asset('uploads/productDesignImages') . '/' . $value->image }}" id="bannerImg" alt="your image" width="80px" height="80px" /></td>
                                @else
                                <td><img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="bannerImg" alt="your image" width="80" height="80" /></td>
                                @endif
		                        <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
		                        <td>{!!$status!!}</td>
		                        <td>
		                        	<a href="{{url('product-design-management/edit/'.$value->id)}}" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
		                        	<a href="javascript:void(0)" id="{{$value->id}}" data-table="product_designs" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
		                        	<a href="{{url('product-design-management/view-gallery/'.$value->id)}}" class="btn btn-success" title="View Gallery"><i class="fa fa-eye" aria-hidden="true"></i></a>
		                        	<a href="{{url('product-design-management/addon-list/'.$value->id)}}" class="btn btn-warning" title="Add Addons"><i class="fa fa-plus" aria-hidden="true"></i></a>
		                        </td>
		                    </tr>
	                    	@endforeach
	                    	@else 
	                    	<tr>
	                    		<td colspan="8" align="center">No Data Available!</td>
	                    	</tr>
	                    	@endif
	                    </tbody>
	                </table>
	                <nav aria-label="Page navigation example">
                  		{!! $productDesignList->links() !!}
           			</nav>

	            </div>
	        </div>
	    </div>   
	</div>
</div>
@endsection