@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Coupon Management</h2>
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
         <a href="#" class="btn btn-primary" id="update_time"><i class="fa fa-plus"></i>
         	 Assign User
         </a>
      </div>       
    </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title"> 
                </div>
                <div class="ibox-content">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                               <th> <input type="checkbox" id="ckbCheckAll" /><input type="hidden" id="all_update" value="{{$coupon_id}}"></th>
                               <th>User Name</th>                                
                            </tr>
                        </thead>
                        <tbody> 
                            @if(count($userlist)>0)
                            @foreach ($userlist as $key => $value)	                    			
                            <tr>
                                <td>
                                    <p id="checkBoxes{{ $value->id  }}">
                                        <input name="selector" type="checkbox" class="checkBoxClass" id="{{ $value->id  }}"  value="{{ $value->id  }}"  />
                                    </p>
                                </td>
                                <td>{{$value->name}}</td>                                
                            </tr>
                            @endforeach
                            @else 
                            <tr>
                                <td colspan="10" align="center">No Data Available!</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">

                    </nav>

                </div>
            </div>
        </div> 
         <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title"> 
                </div>
                <div class="ibox-content">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                	                        
                                <th>User Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody> 
                            @if(count($couponList)>0)
                            @foreach ($couponList as $key => $value)	                    			
                            <tr>
                                <td>{{($key+1)}}</td>
                                

                                <td>{{$value->users->name}}</td>
                                <td>
                                    <a href="{{url('/coupon-management/assign-user-delete/'.$value->id)}}"  class="btn btn-danger "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                            @endforeach
                            @else 
                            <tr>
                                <td colspan="10" align="center">No Data Available!</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">

                    </nav>

                </div>
            </div>
        </div> 
    </div>
</div>
<div class="wrapper wrapper-content">
    <div class="row">
         
    </div>
</div>
<script>
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$("#ckbCheckAll").click(function () {
    $(".checkBoxClass").prop('checked', $(this).prop('checked'));
    //var all_update = $("#ckbCheckAll").val();
  //  $("#all_update").val('1');
   
});
$("#update_time").click(function(){
  var favorite = [];
            $.each($("input[name='selector']:checked"), function(){
                favorite.push($(this).val());
            });
            var coupon_id = $("#all_update").val();
            
            //alert("My favourite sports are: " + favorite.join(", "));
        console.log(favorite);
       $.ajax({
           
                url: "{{url('/coupon-management/assign-user/')}}",
                type: "post",
                data: { "_token": "{{ csrf_token() }}",user_id :favorite ,coupon_id:coupon_id},
                success: function(d) {
                    console.log(d);
                    location.reload();
                }
            });
});
</script>
@endsection