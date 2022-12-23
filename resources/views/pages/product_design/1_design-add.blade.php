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
         <a href="{{url('product-design-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Design List
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
                    <form role="form" data-action="product-design-management/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="productDesignId" value="{{isset($details)?$details->id:''}}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label>Select Category<sup>*</sup></label>
                                  <select class="form-control requiredCheck" name="category_id" id="category_id" data-check="Product Category">
                                    <option value="">-Choose Category-</option> 
                                      @if(count($catagoryList)>0)
                                        @foreach ($catagoryList as $key => $value)
                                      <option value="{{$value->id}}" {{(isset($details) && $details->category_id == $value->id)?'selected':''}}>
                                        {{$value->name}}
                                      </option>  
                                        @endforeach
                                      @else
                                      <option value="">No Category Available!</option>
                                    @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Title<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Title" data-check="Title" name="title" id="title"  class="form-control requiredCheck" value="{{isset($details)?$details->title:''}}">
                                    {{-- <label>Price<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Price" data-check="Service Price" name="price" id="price"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($data)?$data->price:''}}"> --}}
                              </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Price<sup>*</sup></label>
                              <input type="text" placeholder="Enter Price" data-check="Price" name="price" id="price"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($details)?$details->price:''}}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                  <label>Is_Featured<sup>*</sup></label>
                                  <select class="form-control requiredCheck" name="is_featured" id="is_featured" data-check="Type">
                                    <option value="">-Choose Type-</option>
                                    <option value="1"{{(isset($details) && $details->is_featured=='1')?'selected':''}}>Yes</option>
                                    <option value="0"{{(isset($details) && $details->is_featured=='0')?'selected':''}}>No</option>
                                  </select>
                              </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6" style="{{isset($details)?'display: none':'display:block'}}">
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="file_name[]" id="file_name[]" class="form-control" data-check="Image" multiple><br>
                                  {{-- @if(isset($details))
                                        <img src="{{ asset('uploads/productDesignImages/'.$details->file_name) }}" height="60%" width="100" id="bannerImg" />
                                  @endif --}}
                            </div>
                            {{-- @if(isset($details))
                                <input type="hidden" name="old_file" value="{{$details->file_name}}">
                            @endif --}}
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                  <label>Is_Trending<sup>*</sup></label>
                                  <select class="form-control requiredCheck" name="is_trending" id="is_trending" data-check="Type">
                                    <option value="">-Choose Type-</option>
                                    <option value="1"{{(isset($details) && $details->is_trending=='1')?'selected':''}}>Yes</option>
                                    <option value="0"{{(isset($details) && $details->is_trending=='0')?'selected':''}}>No</option>
                                  </select>
                              </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                                <div class="form-group">
                                    <label>Short Description</label>
                                    <textarea class="form-control" name="short_description" id="short_description" data-check="Short Description" rows="8">{{(isset($details))?$details->short_description:''}}</textarea>
                                </div>
                            </div>
                        </div>
                         <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                <label>Select Delivery Day<sup>*</sup></label>
                                  <select class="form-control requiredCheck" name="delivery_id" id="delivery_id" data-check="Delivery Day">
                                    <option value="">-Choose Day-</option> 
                                      @if(count($Deliverytime)>0)
                                        @foreach ($Deliverytime as $key => $value)
                                      <option value="{{$value->id}}" {{(isset($details) && $details->delivery_id == $value->id)?'selected':''}}>
                                        {{$value->day}}
                                      </option>  
                                        @endforeach
                                      @else
                                      <option value="">No Day Available!</option>
                                    @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Quantity<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Product Quantity" data-check="Title" name="quantity" id="quantity"  class="form-control requiredCheck" data-check="Quantity" value="{{isset($details)?$details->quantity:''}}" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                    {{-- <label>Price<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Price" data-check="Service Price" name="price" id="price"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($data)?$data->price:''}}"> --}}
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label>Size</label>
                                  <select class="form-control myselect" multiple name="size[]" id="size" data-check="Product Category" >
                                     @php
                                      $selected = explode(",", isset($details)?$details->size:'');
                                     @endphp
                                      <option value="">-Choose Size-</option> 
                                      <option value="S"{{(isset($details) && (in_array('S', $selected))) ? 'selected' : '' }}>S</option>
                                      <option value="M"{{(isset($details) && (in_array('M', $selected)))?'selected':''}}>M</option>
                                      <option value="L"{{(isset($details) && (in_array('L', $selected)))?'selected':''}}>L</option>
                                      <option value="XL"{{(isset($details) && (in_array('XL', $selected)))?'selected':''}}>XL</option>
                                      <option value="XS"{{(isset($details) && (in_array('XS', $selected)))?'selected':''}}>XS</option>
                                      <option value="XXL"{{(isset($details) && (in_array('XXL', $selected)))?'selected':''}}>XXL</option>
                                    {{--   @if(count($sizes)>0)
                                        @foreach ($sizes as $key => $value) --}}
                                     {{--  <option value="{{$value->id}}" {{ (in_array($value->id, $selected)) ? 'selected' : '' }}>
                                        {{$value->size}}
                                      </option>   --}}
                                       {{--  @endforeach
                                      @else
                                      <option value="">No Size Available!</option>
                                    @endif --}}
                                  </select>
                              </div>
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
  $(document).ready(function() {
   $('.myselect').select2();
});
</script>
@endsection
