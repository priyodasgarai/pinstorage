@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Category Management</h2>
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
        <a href="{{url('category-management/measurement-add/'.$addonId)}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Measurement
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
	                        <th>Label</th>
	                         <th>Created On</th>
                                 <th>Status</th>
<!--	                        <th>Action</th>-->
	                    </tr>
	                    </thead>
	                    <tbody> 
	                    	@if(count($addonList)>0)                       
	                    		@foreach ($addonList as $key => $value)
	                    		@php
	                    				if($value->status == 1):
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_design_measurements" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
	                    				else:
	                    					$status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="product_design_measurements" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
	                    				endif;
	                    			@endphp	
		                    <tr>
		                        <td>{{($key+1)}}</td>
		                        <td>{{$value->label}}</td>
		                        <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
		                         <td>{!!$status!!}</td>
		                       
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
<script>
$(document).on('click','.delete-value',function() {
  var id    = $(this).data('id');
  var keyId   = $(this).data('key');
  var table   = $(this).data('table');
  var status  = $(this).data('status');
  var dataJSON = { 
                    id:id,
                    keyId:keyId,
                    table:table,
                    status:status,
                    _token : _token
                  };
  $.confirm({
  icon: 'fa fa-spinner fa-spin',
    title: 'Confirm!',
    content: 'Do you really want to do this ?',
    type: 'orange',
    typeAnimated: true,
    buttons: {
        confirm: function () {

          if(id && table){
            $.ajax({
            type: "POST",
            url: baseUrl+"category-management/measurement-delete",
            data: dataJSON,
            dataType:"JSON",
            success:function(data) {
              if(data.status){
                if (data.postStatus == '3') {

                  $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: 'Data has been deleted !',
                    type: 'green',
                    typeAnimated: true,
                });
                setTimeout(function(){ location.reload() }, 1550);

                }else if(data.postStatus == '1'){
                $('#'+id).removeClass('badge-danger');
                $('#'+id).addClass('badge-primary');
                $('#'+id).html('Active');
                $('#'+id).data('status','0');
                $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: data.message,
                    type: 'green',
                    typeAnimated: true,
                });
                }else if(data.postStatus == '0'){

                  $('#'+id).removeClass('badge-primary');
                  $('#'+id).addClass('badge-danger');
                  $('#'+id).html('Inactive');
                  $('#'+id).data('status','1');

                $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: data.message,
                    type: 'green',
                    typeAnimated: true,
                });

                }
              }
              

                
              
            }
        });

          }
            
        },
        cancel: function () {
            $.alert({
              icon: 'fa fa-times',
              title: 'Canceled!',
              content: 'Process canceled',
              type: 'purple',
              typeAnimated: true,
            });
        }
    }
});
     
});
</script>
@endsection