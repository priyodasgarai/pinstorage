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
         <a href="{{url('product-design-management/addon-list/'.$productDesignId)}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Addon List
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
                    <form role="form" data-action="product-design-management/addon-list/{{$productDesignId}}" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="addonId" value="{{isset($details)?$details->id:''}}">
                        <input type="hidden" name="designId" value="{{$productDesignId}}">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Group<sup>*</sup></label>
                                 <input type="text" placeholder="Enter Group" data-check="Group" name="input_group" id="input_group"  class="form-control requiredCheck" value="{{isset($details)?$details->input_group:''}}">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>Custom Price<sup>*</sup></label>
                                 <input type="text"  name="custom_price" id="custom_price"  class="form-control checkDecimal requiredCheck" data-check="Custom Price"  value="{{isset($details)?$details->custom_price:''}}">
                            </div>
                          </div>
                        </div>
                        <div class="row" id="dynamic_field">
                          <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Title<sup>*</sup></label>
                                             <input type="text"  name="title[]" id="title"  class="form-control requiredCheck" data-check="Title"  >
                                        </div>
                                      </div>

                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Price<sup>*</sup></label>
                                             <input type="text"  name="price[]" id="price"  class="form-control checkDecimal requiredCheck" data-check="Price">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Addon<sup>*</sup></label>
                                           <input type="file"  name="addon_image[]" id="addon_image"  class="form-control requiredCheck" accept="image/*" data-check="Image">
                                        </div>
                                      </div>
                                      <div class="col-md-2">
                                        <div class="form-group">
                                        <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="bannerImg" height="150px" width="150px"  />
                                          </div>
                                        </div>

                                    <div class="col-md-1">
                            <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-success " id="add_button" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>
                          </div>
                        </div>
                        <button class="btn btn-primary" type="submit">Save</button>
                        <button class="btn btn-primary" type="submit">Save & Next</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
      var flag = "{{isset($detailsImage)?'1':'0'}}"
      var fieldCounter = 5;
      var currentFieldCounter = "{{isset($detailsImage)?count($detailsImage):1}}"
      console.log(currentFieldCounter);
      var i=(flag==1)?(parseInt(currentFieldCounter)):1;
      $(document).on('click','#add_button',function(e){
           e.preventDefault();
           if(i < fieldCounter){ //max input box allowed
                i++; //text box increment
                $('#dynamic_field').append(`<div class="row" id="row${i}">
                                              <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Title</label>
                                             <input type="text"  name="title[]" id="title"  class="form-control requiredCheck" data-check="Title">
                                        </div>
                                      </div>
                                      {{-- </div> --}}
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Price</label>
                                             <input type="text"  name="price[]" id="price"  class="form-control checkDecimal requiredCheck" data-check="Price">
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="form-group">
                                          <label>Addon</label>
                                           <input type="file"  name="addon_image[]" id="addon_image"  class="form-control requiredCheck" accept="image/*" data-check="Image">
                                        </div>
                                      </div>
                                      <div class="col-md-2">
                                        <div class="form-group">
                                      <img src="{{ asset('assets/images') . '/' . 'no-img-available.png' }}" id="bannerImg" height="150px" width="150px"  />
                                        </div>
                                      </div>
                                              <div class="col-md-1">
                                                  '<a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-danger btn_remove" name="remove" id="${i}" title="Remove field"><i class="fa fa-times" aria-hidden="true"></i></a>
                                              </div>
                                            </div>`);
            }

      });
      $(document).on('click', '.btn_remove', function(){
           var button_id = $(this).attr("id");
           $('#row'+button_id+'').remove();
           i --;
        console.log(flag)
        if(flag=='1' && !$('#adminFrm').hasClass('flag')){
          $('#adminFrm').append('<input type="hidden" class="flag" name="flag" value="1">');
          $('#adminFrm').submit();
        }
      });
</script>
@endsection
