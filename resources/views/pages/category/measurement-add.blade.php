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
            <strong><?=$title?></strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
      <div class="title-action">
         <a href="{{url('category-management/measurement-list/'.$productDesignId)}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Management List
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
                    <form role="form" action="{{url('category-management/measurement-list/'.$productDesignId)}}"  method="POST">
                        @csrf
                       
                        <input type="hidden" name="product_category_id" value="{{$productDesignId}}">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Label<sup>*</sup></label>
                                 <input type="text" placeholder="Enter Label" data-check="Label" name="label"   class="form-control requiredCheck" value="">
                            </div>
                               <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Save">
                             
                          </div>
                         
                        </div>
                        </div>
                       
                       
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>

@endsection
