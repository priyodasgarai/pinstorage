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
            <strong>{{$title}}</strong>
         </li>
      </ol>
   </div>
   <div class="col-sm-8">
      <div class="title-action">
         <a href="{{url('cms-management/company-details/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
         	Add Page
         </a>
      </div>
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
	            		<div class="col-md-4">
	            			<div class="form-group">
	            				<input type="text" name="searchFormDate" id="searchFormDate" class="form-control" placeholder="Search By Form Date ">
	            			</div>
	            		</div>
	            		<div class="col-md-4">
	            			<div class="form-group">
	            				<input type="text" name="searchToDate" id="searchToDate" class="form-control" placeholder="Search By To Date">
	            			</div>
	            		</div>
	            		<div class="col-md-4">
	            			<div class="form-group">
	            				<input type="text" name="searchTitle" id="searchTitle" class="form-control" placeholder="Search By Title...">
	            			</div>
	            		</div>
	            		{{-- <div class="col-md-3">
	            			<div class="form-group">
	            				<input type="text" name="searchEmail" id="searchEmail" class="form-control" placeholder="Search By Email...">
	            			</div>
	            		</div> --}}

	            	</div>
	            	<br>
	            	<br>
	            	
			            <div class="">
			                <table class="table table-bordered" id="cmsTable">
			                    <thead>
				                    <tr>
				                        <th>#</th>
				                        <th>Company Title</th>
				                        <th>Image</th>
				                        <th>Created On</th>
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
    	let searchTitle=searchFormDate=searchToDate = "";
		var dataTable = $('#cmsTable').DataTable({
			dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            searching:  false,
            ajax: {
		        url: baseUrl+'cms-management/ajax-cms-table',
		        type:'POST',
		        data: function(data){
		        	if($('#searchTitle').val()!=""){
			    		searchTitle = $('#searchTitle').val();
			    	}
			    	if($('#searchFormDate').val()!=""){
			    		searchFormDate = $('#searchFormDate').val();
			    	}
			    	if($('#searchToDate').val()!=""){
			    		searchToDate = $('#searchToDate').val();
			    	}
		        	data._token			= _token,
		        	data.searchTitle	= searchTitle,
		        	data.searchFormDate	= searchFormDate,
		        	data.searchToDate	= searchToDate
		        }
		    },
            columns: [
                 { data: 'id' },
                 { data: 'title' },
                 { data: 'image' },
                 { data: 'created_at'},
                 { data: 'status' },
                 { data: 'action' }
             ],
            columnDefs: [
			   { orderable: false, targets: -1 }
			],
			buttons: [
			    {
		            extend: 'excelHtml5',
		            text: 'Export Excel ',
		            filename: function () { return 'Company List ' + new Date(); },
		            exportOptions: {
		                columns: [0, 1, 2, 3, 4, 5],
		                modifier: {
		                    order: 'current',                       
		                    page: 	'all',
		                }                    
		            }
		        }
		        /*{
			      text: 'Export Full Data In Excel',
			      action: function ( e, dt, button, config ) {
			        window.location = baseUrl+'customer-management/export-customer-list';
			      }        
			    }*/
	        ]
        });
        $('#searchTitle').keyup(function(){
		    dataTable.draw();
		});
		/*$('#searchName').keyup(function(){
		    dataTable.draw();
		});*/
		$('#searchTitle').keydown(function(){
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
</script>
@endsection