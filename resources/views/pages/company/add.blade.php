@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Page Management</h2>
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
         <a href="{{url('cms-management/company-details')}}" class="btn btn-primary"><i class="fa fa-list"></i>
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
                    <form role="form" data-action="cms-management/company-details/save" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{isset($details)?$details->id:''}}">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Title<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Title" data-check="Title" name="title" id="title" class="form-control requiredCheck restrictSpecial" value="{{isset($details)?$details->title:''}}">
 
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                                  <label>Description<sup>*</sup></label>
                                  <textarea id="description" name="description" placeholder="Enter Description here" class="form-control requiredCheck" rows="5" data-check="Description">{{isset($details)?$details->description:''}}</textarea> 
                              </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Image<sup>*</sup></label> 
                                <input type="file" name="image" id="image" class="form-control " data-check="Image"><br>
                                @if(isset($details) && checkFileDirectory($details->image,'uploads/cmsImages'))
                                <img src="{{ asset('uploads/cmsImages/'.$details->image) }}" height="100px" width="150px" id="cmsImg">
                                @else
                                   <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="custImg" height="100px" width="150px" id="cmsImg" />
                                @endif
                            </div>
                            @if(isset($details))
                                <input type="hidden" name="old_file" value="{{$details->image}}">
                            @endif
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

@push('scripts')
<script src="{{asset('assets/admin/js/tinymce/tinymce.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/editor.js')}}"></script>

@endpush
