@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Order Management</h2>
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
      {{-- <div class="title-action">
         <a href="{{url('orders/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Category
         </a>
      </div> --}}
   </div>
</div>
<div class="wrapper wrapper-content">
	{{-- <div class="row">
		<div class="col-md-6">
			<a href="/all-tweets-csv" class="btn btn-primary">Export as CSV</a>
		</div>
		<div class="col-md-6">
			<a href="/all-tweets-csv" class="btn btn-primary">Export as CSV</a>
		</div>
	</div> --}}
	<div class="row">
	    <div class="col-lg-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title">
	            </div>
	            <div class="ibox-content">
	            	<div class="row">
	            		<div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchFormDate" id="searchFormDate" class="form-control" placeholder="Search By Form Date ">
	            			</div>
	            		</div>
	            		<div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchToDate" id="searchToDate" class="form-control" placeholder="Search By To Date">
	            			</div>
	            		</div>
	            		<div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchOrderId" id="searchOrderId" class="form-control" placeholder="Search By Order Id...">
	            			</div>
	            		</div>
	            		 <div class="col-md-3">
                            <div class="form-group">
                                <select class="form-control  select2 requiredCheck" name="searchStatus" id="searchStatus" >
                                  <option value="">Search By Status</option>
                                  <option value="1">Placed</option>
                                  <option value="2">Out For Measurement</option>
                                  <option value="3">Arrived Tomorrow</option>
                                  <option value="4">Out For Delivery</option>
                                  <option value="5">Deliverd</option>
                                  <option value="0">Canceled</option>
                                </select>
                          </div>
	            		</div>

	            	</div>
	            	<br>
	            	<br>

			            <div class="">
			                <table class="table table-bordered" id="orderTable">
			                    <thead>
				                    <tr>
				                        <th>#</th>
				                        <th>Order Id</th>
                                        <th>Design</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
				                        <th>Amount</th>
				                        <th>Paid Amount</th>
				                        <th>Order Date</th>
				                        <th>Status</th>
				                        <th>Action</th>
				                    </tr>
			                    </thead>
			                </table>

		            </div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#searchFormDate").datepicker({ maxDate:0});
		$("#searchToDate").datepicker({ maxDate:0});
    		// minDate: "+1M +10D"
    	let searchOrderId=searchFormDate=searchToDate =searchStatus= "";
		var dataTable = $('#orderTable').DataTable({
			dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            searching:  false,
            scrollX: true,
            ajax: {
		        url: baseUrl+'orders/ajax-order-table',
		        type:'POST',
		        data: function(data){
		        	if($('#searchOrderId').val()!=""){
			    		searchOrderId = $('#searchOrderId').val();
			    	}
			    	if($('#searchFormDate').val()!=""){
			    		searchFormDate = $('#searchFormDate').val();
			    	}
			    	if($('#searchToDate').val()!=""){
			    		searchToDate = $('#searchToDate').val();
			    	}
                    if($('#searchStatus').val()!=""){
			    		searchStatus = $('#searchStatus').val();
			    	}
		        	data._token			= _token,
		        	data.searchOrderId	= searchOrderId,
		        	data.searchFormDate	= searchFormDate,
                    data.searchStatus	= searchStatus,
		        	data.searchToDate	= searchToDate
		        }
		    },
            columns: [
                 { data: 'id' },
                 { data: 'order_prefix_id' },
                 { data: 'design' },
                 { data: 'name' },
                 { data: 'email' },
                 { data: 'phone' },
                 { data: 'price' },
                 { data: 'paid_amount' }, 
                 { data: 'created_at'},
                 { data: 'status' },
                 { data: 'action' }
             ],
            columnDefs: [
			   { orderable: false, targets: -1 }
			],
			buttons: [
			  /*  {
		            extend: 'excelHtml5',
		            text: 'Export Excel ',
		            filename: function () { return 'Order List ' + new Date(); },
		            exportOptions: {
		                columns: [0, 1, 2, 3, 4],
		                modifier: {
		                    order: 'current',
		                    page: 	'all',
		                }
		            }
		        },*/
		         {
                    text: '<a href="javascript:void(0)" class="btn btn-primary">Export Full Data</a>',
                             // className: 'btn btn-success',
			      action: function ( e, dt, button, config ) {
			        window.location = baseUrl+'orders/export-order-list';
			      }
			    }
	        ]
        });
        $('#searchOrderId').keyup(function(){
		    dataTable.draw();
		});
		$('#searchStatus').change(function(){
		    dataTable.draw();
		});
		$('#searchOrderId').keydown(function(){
		    dataTable.draw();
		});
		/*$('#searchName').keydown(function(){
		    dataTable.draw();
		});*/
		$('#searchToDate').change(function(){
			var startDate = $('#searchFormDate').val();
		    var endDate = $(this).val();
		    if ((Date.parse(startDate) > Date.parse(endDate))) {
		            $.alert({
		                   icon: 'fa fa-warning',
		                   title: 'Warning!',
		                   content: 'To date should be greater than From date !',
		                  type: 'orange',
		                  typeAnimated: true,
		               });
			        $(this).val('').focus();
			        return false;
		    }
		    dataTable.draw();
		});
	});
	 $(document).on('click','.change-order-status',function() {
  var id    = $(this).data('id');
  var keyId   = $(this).data('key');
  var table   = $(this).data('table');
  var status  = $(this).data('status');
  var dataJSON = {
                    id:id,
                    keyId:keyId,
                    table:table,
                    status:status,
                    _token : _token
                  };
  $.confirm({
  icon: 'fa fa-spinner fa-spin',
    title: 'Confirm!',
    content: 'Do you really want to do this ?',
    type: 'orange',
    typeAnimated: true,
    buttons: {
        confirm: function () {

          if(id && table){
            $.ajax({
            type: "POST",
            url: baseUrl+"generic-status-change-delete",
            data: dataJSON,
            dataType:"JSON",
            success:function(data) {
              if(data.status){
                if (data.postStatus == '3') {

                  $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: 'Data has been deleted !',
                    type: 'green',
                    typeAnimated: true,
                });
                setTimeout(function(){ location.reload() }, 1550);

                }else if(data.postStatus == '1'){
                $('#'+id).removeClass('badge-danger');
                $('#'+id).addClass('badge-primary');
                $('#'+id).html('Active');
                $('#'+id).data('status','0');
                $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: data.message,
                    type: 'green',
                    typeAnimated: true,
                });
                }else if(data.postStatus == '0'){

                  $('#'+id).removeClass('badge-primary');
                  $('#'+id).addClass('badge-danger');
                  $('#'+id).html('Inactive');
                  $('#'+id).data('status','1');

                $.alert({
                    icon: 'fa fa-check',
                    title: 'Success!',
                    content: data.message,
                    type: 'green',
                    typeAnimated: true,
                });

                }
              }




            }
        });

          }

        },
        cancel: function () {
            $.alert({
              icon: 'fa fa-times',
              title: 'Canceled!',
              content: 'Process canceled',
              type: 'purple',
              typeAnimated: true,
            });
        }
    }
});

});
</script>
@endsection
