@extends('layouts.master')
@section('content')
<style type="text/css">
    span.select2.select2-container {
    width: 100% !important;
}
</style>
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
         <a href="{{url('category-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Category List
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
                    <form role="form" data-action="category-management/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="updateId" value="{{isset($details)?$details->id:''}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Category Name <sup>*</sup></label> 
                                    <input type="text" placeholder="Enter category name" data-check="Category Name" name="name" id="name" class="form-control requiredCheck" value="{{isset($details)?$details->name:''}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Is this a parent category ?<sup>*</sup></label>
                                    <br>
                                    <label>
                                        <input type="radio" name="is_parent" value="1" class="isParentClass" {{(isset($details) && $details->parent == 0)?'checked':'checked'}} >  
                                        Yes
                                    </label>
                                    <label>
                                        <input type="radio" name="is_parent" value="0" class="isParentClass" {{(isset($details) && $details->parent > 0)?'checked':''}}>   No
                                    </label>
                                </div> 
                            </div>                           
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="parentMenuList" style="{{(isset($details) && $details->parent > 0)?'display: block;':'display: none;'}}">
                                <div class="form-group">
                                    <label>Select Parent Category <sup>*</sup></label>
                                    <select class="form-control  select2" name="parent_id" id="parent_id" data-check="Parent Category">
                                        <option value="">-Choose Category-</option>
                                        @php
                                            if(count($categoryList)>0): 
                                                foreach ($categoryList as $key => $value):
                                        @endphp
                                            <option value="{{$value->id}}" {{(isset($details) && $details->parent > 0 && $details->parent == $value->id)?'selected':''}}>
                                                {{$value->name}}
                                                
                                            </option>
                                        @php  
                                            endforeach;
                                            else: 
                                        @endphp
                                            <option value="">No Category Available!</option>
                                        @php 
                                            endif;
                                        @endphp
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Image</label>
                                    <br>
                                    @if(isset($details) && checkFileDirectory($details->image,'uploads/category'))
                                        <img src="{{ asset('uploads/category/'.$details->image) }}" height="150px" width="150px">
                                    @else
                                      <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="bannerImg" height="150px" width="150px"  />
                                    @endif
                                    <input type="file" class="form-control" data-check="Image" accept="image/*" name="image" id="image">
                                </div>
                                {{-- @if(isset($details))
                                <input type="hidden" name="old_file" value="{{$details->image}}">
                                @endif --}}
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" data-check="Description" rows="8">{{(isset($details))?$details->description:''}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Banner Image</label>
                                <input type="file" name="banner_image" id="banner_image" class="form-control" data-check="Banner Image" ><br>
                                @if(isset($details) && checkFileDirectory($details->banner_image,'uploads/category'))
                                    <img src="{{ asset('uploads/category/'.$details->banner_image) }}"  id="catBannerImg" />
                                @else
                                    <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="catBannerImg"  />
                                @endif
                            </div>
                            {{-- @if(isset($details))
                                <input type="hidden" name="old_banner_file" value="{{$details->banner_image}}">
                            @endif --}}
                          </div>
                        </div>
                        <div>
                            <button class="btn btn-primary" type="submit">{{(isset($details))?'Update':'Save'}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>
<script type="text/javascript">
    $(document).on('change','.isParentClass',function() {
        if($(this).val() == '1'){
            $('#parentMenuList').hide();
            $('#parent_id').removeClass('requiredCheck');
        }else{
            $('#parent_id').addClass('requiredCheck');
            $('#parentMenuList').show();
        }
    })
</script>
@endsection
