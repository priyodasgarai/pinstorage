@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
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
                <div class="col-lg-2">

                </div>
</div>
<div class="wrapper wrapper-content">
            <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">

                    <div class="ibox-content">
                        <div class="lightBoxGallery">
                        	<div class="row">
                        	@if(count($gallery)>0)
                        	@foreach($gallery as $key=>$value)
                        		@php
	                    				if($value->is_primary == 1):
	                    					$flag=1;
	                    				else:
	                    					$flag=0;
	                    				endif;
	                    		@endphp
                        		<div class="col-md-3">
                        			<img src="{{ asset('uploads/productDesignImages') . '/' . $value->file_names }}" id="bannerImg" alt="your image" width="100%" height="200" title="{{($value->is_primary == 1)?'':'Mark as Primary'}}" />
                        			<div class="chk-box">
                        				<input type="checkbox" name="chck" class="fa_check"{{($value->is_primary == 1)?'checked':''}} title="{{($value->is_primary == 1)?'':'Mark as Primary'}}">
                        				{{-- <i class="fa fa-check"></i> --}}
                        			</div>
                        			@if($value->is_primary ==1)
                            		<a href="javascript:void(0)" id="{{$value->id}}" data-table="product_designs" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status" style="display: none;"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            		@else
                            		<a href="javascript:void(0)" id="{{$value->id}}" data-table="product_designs" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status" style=""><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            		@endif
                        		</div>
                        		{{-- @else
                        		$flag=0
                        		@endif --}}
                        	@endforeach
                            @endif
                        	</div>
                        </div>

                    </div>
                </div>
            </div>

            </div>
        </div>
        <script type="text/javascript">
        	$(document).ready(function() {
        		$('.fa_check').change(function(){
				    $('.fa_check').prop('checked', false); 
				    $(this).prop('checked', true);
                    var ids = [];
                    $('.fa_check').each(function(){  
                    if($(this).is(":checked"))  
                        {  
                            ids.push($(this).val()); 
                            //console.log(IDS);
                        }  
                    });  
                    languages = ids.toString();
                    console.log(languages);
        		});
        	});
        </script>
@endsection