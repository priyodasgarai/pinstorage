@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>FAQ Management</h2>
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
         <a href="{{url('faq-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            List
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
                    <form role="form" data-action="faq-management/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{isset($data)?$data->id:''}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Question Title<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Question" data-check="Question Title" name="question" id="question" class="form-control requiredCheck" value="{{isset($data)?$data->question:''}}">
 
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                                  <label>Question Answer<sup>*</sup></label>
                                  <textarea id="answer" name="answer" placeholder="Enter Answer here" class="form-control requiredCheck" rows="5" data-check="Question Answer">{{isset($data)?$data->answer:''}}</textarea> 
                              </div>
                          </div>
                        </div>
                            <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>
@endsection
