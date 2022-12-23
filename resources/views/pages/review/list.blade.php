@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Review Management</h2>
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
	            </div>
	            <div class="ibox-content">

	                <table class="table table-bordered">
	                    <thead>
	                    <tr>
	                        <th>#</th>
	                        <th>Customer Name</th>
	                        <th>Customer Email</th>
	                        <th>Rating</th>
	                        <th>Created On</th>
	                        <th>Approval</th>
	                        <th>Action</th>
	                    </tr>
	                    </thead>
	                    <tbody> 
	                    	@if(count($reviewList)>0)
	                    		@foreach($reviewList as $key => $value)
	                    			@php
	                    				if($value->approval == 1):
	                    					$approval = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="reviews" data-approval="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-approval">Approved</a>';
	                    				else:
	                    					$approval = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="reviews" data-approval="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-approval">Unapproved</a>';
	                    				endif;
	                    			@endphp

		                    <tr>
		                        <td>{{($key+1)}}</td>
		                        <td>{{$value->userName}}</td>
		                        <td>{{$value->userEmail}}</td>
		                        @if($value->rating =='5')
		                        <td>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
		                        </td>
		                        @elseif($value->rating =='4')
		                        <td>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
		                        </td>
		                        @elseif($value->rating =='3')
		                         <td>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
									<i class="fa fa-star" aria-hidden="true"></i>
		                        </td>
		                        @elseif($value->rating =='2')
		                        <td>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        </td>
		                        @elseif($value->rating =='1')
		                        <td>
		                        	<i class="fa fa-star" aria-hidden="true"></i>
		                        </td>
		                        @else
		                        <td>
		                        	{{-- <i class="fa fa-star-o" aria-hidden="true"></i> --}}
		                        </td>
		                        @endif
		                        <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
		                        <td>{!!$approval!!}</td>
		                        <td>
		                        	<a href="javascript:void(0)" id="{{$value->id}}" data-table="reviews" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
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
                  		{!! $reviewList->links() !!}
           			</nav>

	            </div>
	        </div>
	    </div>   
	</div>
</div>
@endsection