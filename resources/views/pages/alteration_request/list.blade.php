@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Alteration Request</h2>
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
         <a href="{{url('delivery-agent/add')}}" class="btn btn-primary"><i class="fa fa-plus"></i>
          Add Delivery Agent
         </a>
      </div> --}}
   </div>
</div>
<div class="wrapper wrapper-content">
  <div class="row">
      <div class="col-lg-12">
          <div class="ibox float-e-margins">
              <div class="ibox-title"> 
              </div>
              <div class="ibox-content">

                  <table class="table table-bordered">
                      <thead>
                      <tr>
                          <th>#</th>
                          <th>Customer Name</th>
                          <th>Customer Email</th>
                          <th>Alteration Type</th>
                          <th>Job Title</th>
                          <th>Job Description</th>
                          <th>Created On</th>
                          <th>Status</th>
                          <th>Action</th>
                      </tr>
                      </thead>
                      <tbody> 
                        @if(count($alterationList)>0)
                          @foreach ($alterationList as $key => $value)
                            @php
                              if($value->status == 1):
                                $status = '<a href="javascript:void(0)" class="badge badge-primary ">Accepted</a>';
                              elseif($value->status == 0):
                                $status = '<a href="javascript:void(0)"  class="badge badge-danger ">Denied</a>';
                              elseif($value->status == 2):
                                $status = '<a href="javascript:void(0)"  class="badge badge-warning ">Pending</a>';
                               elseif($value->status == 3):
                                $status = '<a href="javascript:void(0)"  class="badge badge-success ">Processing</a>';
                              endif;
                            @endphp

                        <tr>
                            <td>{{($key+1)}}</td>
                            <td>{{$value->userName}}</td>
                            <td>{{$value->userEmail}}</td>
                            @if($value->alteration_type=='1')
                            <td><span>New Stiching</span></td>
                            @elseif($value->alteration_type=='2')
                            <td><span>Alteration</span></td>
                            @endif
                            <td>{{$value->job_title}}</td>
                            <td>{{ ($value->job_description!='')?substr($value->job_description, 0,100).'...':''}}</td>
                            <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
                            <td>{!!$status!!}</td>
                            <td>
                             <a href="{{url('customer-alteration-request/view-details/'.$value->id)}}" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else 
                        <tr>
                          <td colspan="9" align="center">No Data Available!</td>
                        </tr>
                        @endif
                      </tbody>
                  </table>
                  <nav aria-label="Page navigation example">
                      {!! $alterationList->links() !!}
                </nav>

              </div>
          </div>
      </div>   
  </div>
</div>
@endsection