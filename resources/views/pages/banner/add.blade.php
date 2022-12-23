@extends('layouts.master')
@section('content')
<style type="text/css">
    span.select2.select2-container {
    width: 100% !important;
}
</style>
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Slider Management</h2>
      <ol class="breadcrumb">
         <li>
            <a href="{{route('admin.dashboard')}}">Dashboard</a>
         </li>
         <li class="active">
            <strong><?=$title?></strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
      <div class="title-action">
         <a href="{{route('admin.slider-management.list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Slider List
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
                    <form role="form" data-action="{{route('admin.slider-management.save')}}" id="adminFrm" enctype="multipart/form-data" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{isset($details)?$details->id:''}}">
                        <div class="row">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label>Title<sup>*</sup></label>
                                <input type="text" placeholder="Enter  Title" data-check="Slider Title" name="title" id="title" class="form-control requiredCheck" value="{{isset($details)?$details->title:''}}">
                              </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                      <label>Description<sup>*</sup></label>
                                      <textarea id="description" name="description" placeholder="Enter Description here" class="form-control requiredCheck" rows="5" data-check="Slider Description">{{isset($details)?$details->description:''}}</textarea>
                                  </div>
                              </div>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Slider Image</label>
                                <input type="file" name="image" id="image" class="form-control requiredCheck" data-check="Slider Image" onchange="document.getElementById('image').src = window.URL.createObjectURL(this.files[0])"><br>
                                  {{-- @if(isset($details))
                                        <img src="{{ asset('uploads/bannerImages/'.$details->image) }}" height="150px" width="150px" id="bannerImg">
                                  @endif --}}
                                  @if(isset($details) && checkFileDirectory($details->image,'uploads/bannerImages'))
                                    <img src="{{ asset('uploads/bannerImages/'.$details->image) }}"  id="image" />
                                  @else
                                      <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}"   />
                                  @endif
                            </div>
                            @if(isset($details))
                                <input type="hidden" name="old_file" value="{{$details->image}}">
                            @endif
                          </div>
                        </div>
                            <button class="btn btn-primary" type="submit">{{(isset($details))?'Update':'Save'}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 <script type="text/javascript">

</script>
@endsection
