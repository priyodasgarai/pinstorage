@extends('layouts.master')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Delivery Service Management</h2>
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
         <a href="{{url('delivery-management/list')}}" class="btn btn-primary"><i class="fa fa-list"></i>
            Delivery Service List
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
                    <form role="form" data-action="delivery-management/list" id="adminFrm" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{isset($data)?$data->id:''}}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Serevice Title<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Service Title" data-check="Service Title" name="delivery_title" id="delivery_title" class="form-control requiredCheck" value="{{isset($data)?$data->delivery_title:''}}">
 
                                </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                    <label>Delivery Day<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Day" data-check="Delivery Time" name="day" id="day"  class="form-control requiredCheck"  value="{{isset($data)?$data->day:''}}">
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                                    <label>Service Price<sup>*</sup></label>
                                    <input type="text" placeholder="Enter Price" data-check="Service Price" name="price" id="price"  class="form-control requiredCheck checkDecimal" step="0.01" value="{{isset($data)?$data->price:''}}">
                              </div>
                            </div>
                        </div>
                        @if(isset($data) && $data->delivery_description != '')
                        @php
                          $deliveryDescriptions = json_decode($data->delivery_description);
                        @endphp
                          <div class="row" id="dynamic_field">
                              @if(is_array($deliveryDescriptions) && count($deliveryDescriptions)>0)
                                @foreach ($deliveryDescriptions as $key => $value)
                                  @if($key == 0)
                                      <div class="col-md-11">
                                        <div class="form-group">
                                          <label>Service Description</label>
                                            <textarea style="resize: none;" id="delivery_description" name="delivery_description[]" placeholder="Enter Description here" class="form-control" rows="4" >{{$value}}</textarea>
                                        </div>
                                      </div>
                                      <div class="col-md-1">
                                        <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-success " id="add_button" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                      </div>
                                  @else
                                    <div id="row{{$key}}">
                                      <div class="col-md-11">
                                        <div class="form-group">
                                            <textarea style="resize: none;" id="delivery_description" name="delivery_description[]" placeholder="Enter Description here" class="form-control" rows="4" >{{$value}}</textarea>
                                        </div>
                                      </div>
                                      <div class="col-md-1">
                                        <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-danger btn_remove" id="{{$key}}" title="Remove field"><i class="fa fa-times" aria-hidden="true"></i></a>
                                      </div>
                                    </div>
                                  @endif
                                @endforeach
                              @else
                                <div class="col-md-11">
                                    <div class="form-group">
                                      <label>Service Description</label>
                                        <textarea style="resize: none;" id="delivery_description" name="delivery_description[]" placeholder="Enter Description here" class="form-control" rows="4" ></textarea>
                                    </div>
                                  </div>
                                  <div class="col-md-1">
                                    <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-success " id="add_button" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                  </div>
                              @endif
                          </div>
                        @else
                        <div class="row" id="dynamic_field">
                          <div class="col-md-11">
                            <div class="form-group">
                              <label>Service Description</label>
                                <textarea style="resize: none;" id="delivery_description" name="delivery_description[]" placeholder="Enter Description here" class="form-control" rows="4" ></textarea>
                            </div>
                          </div>
                          <div class="col-md-1">
                            <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-success " id="add_button" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>
                          </div>
                        </div>
                        @endif
                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>   
    </div>
</div>
<script type="text/javascript">
      var flag = "{{isset($data)?'1':'0'}}"
      var fieldCounter = 5;
      var currentFieldCounter = "{{isset($data)?count($deliveryDescriptions):1}}"
      var i=(flag==1)?(parseInt(currentFieldCounter)):1;  
      $(document).on('click','#add_button',function(e){ 
           e.preventDefault();
           if(i < fieldCounter){ //max input box allowed
                i++; //text box increment
                $('#dynamic_field').append('<div id="row'+i+'">\
                                              <div class="col-md-11">\
                                                <div class="form-group">\
                                                    <textarea style="resize: none;" id="delivery_description" name="delivery_description[]" placeholder="Enter Description here" class="form-control" rows="4"></textarea>\
                                                </div>\
                                              </div>\
                                              <div class="col-md-1">\
                                                  <a href="javascript:void(0);" style="margin-top: 26px;" class="btn btn-danger btn_remove" name="remove" id="'+i+'" title="Remove field"><i class="fa fa-times" aria-hidden="true"></i></a>\
                                              </div>\
                                            </div>');
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
