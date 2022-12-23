@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Report</h2>
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
         <a href="{{url('customer-management/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Customer
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
	</div>  --}}
	<div class="row">
	    <div class="col-lg-12">
	        <div class="ibox float-e-margins">
	            <div class="ibox-title"> 
	            </div>
	            <div class="ibox-content">
	            	<div class="row">
	            		<div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchFormDate" id="searchFormDate" class="form-control" placeholder="Form Date ">
	            			</div>
	            		</div>
	            		<div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchToDate" id="searchToDate" class="form-control" placeholder="To Date">
	            			</div>
	            		</div>
	            		 <div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchEmail" id="searchEmail" class="form-control" placeholder="Search By Email">
	            			</div>
	            		</div>
                                 <div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchName" id="searchName" class="form-control" placeholder="Search By Name">
	            			</div>
	            		</div>
	            	</div>
	            	<br>
	            	<br>
	            	
			            <div class="">
			                <table class="table table-bordered" id="customerReportTable">
			                    <thead>
				                    <tr>
				                        <th>#</th>
				                        <th>Customer Name</th>
				                        <th>Customer Email</th>
				                        <th>Created On</th>
				                        <th>Status</th>
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
    	let searchFormDate=searchToDate=searchName=searchEmail="";
		var dataTable = $('#customerReportTable').DataTable({
			dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            searching:  false,
            ajax: {
		        url: baseUrl+'reports/ajax-customer-report-table',
		        type:'POST',
		        data: function(data){
			    	if($('#searchFormDate').val()!=""){
			    		searchFormDate = $('#searchFormDate').val();
			    	}
			    	if($('#searchToDate').val()!=""){
			    		searchToDate = $('#searchToDate').val();
			    	}
			    	  if($('#searchEmail').val()!=""){
			    		searchEmail = $('#searchEmail').val();
			    	}
                                if($('#searchName').val()!=""){
			    		searchName = $('#searchName').val();
			    	}
		        	data._token			= _token,
		        	data.searchFormDate	= searchFormDate,
		        	data.searchToDate	= searchToDate,
                    data.searchName	= searchName,
                    data.searchEmail	= searchEmail
		        }
		    },
            columns: [
                 { data: 'id' },
                 { data: 'name' },
                 { data: 'email' },
                 { data: 'created_at'},
                 { data: 'status' }
             ],
            columnDefs: [
			   { orderable: false, targets: -1 }
			],
			buttons: [
			 /*   {
		            extend: 'excelHtml5',
		            text: 'Export Excel ',
		            filename: function () { return 'Customer Report ' + new Date(); },
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
			        window.location = baseUrl+'customer-management/export-customer-list';
			      }        
			    }
	        ]
        });
        $('#searchEmail').keyup(function(){
		    dataTable.draw();
		});
		$('#searchName').keyup(function(){
		    dataTable.draw();
		});
		$('#searchEmail').keydown(function(){
		    dataTable.draw();
		});
		$('#searchName').keydown(function(){
		    dataTable.draw();
		});
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
		$('#searchToDate').blur(function(){
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
</script>
@endsection