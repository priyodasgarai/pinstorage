@extends('layouts.master')
@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
   <div class="col-sm-4">
      <h2>Contacts</h2>
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
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Message</th>
                          <th>Created On</th>
                          <th>Status</th>
                          <th>Action</th>
                      </tr>
                      </thead>
                      <tbody> 
                        @if(count($contacts)>0)
                          @foreach ($contacts as $key => $value)
                            @php
                              if($value->status == 1):
                                $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="contact_forms" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                              else:
                                $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="contact_forms" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                              endif;
                            @endphp

                        <tr>
                            <td>{{($key+1)}}</td>
                            <td>{{$value->name}}</td>
                            <td>{{$value->email}}</td>
                            <td>{{$value->phone}}</td>
                            <td>{{ ($value->message!='')?substr($value->message, 0,100).'...':''}}</td>
                            <td>{{date('d-m-Y h:i:s',strtotime($value->created_at))}}</td>
                            <td>{!!$status!!}</td>
                            <td>
                              <a href="javascript:void(0)" id="{{$value->id}}" data-table="contact_forms" data-status="3" data-key="id" data-id="{{$value->id}}" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else 
                        <tr>
                          <td colspan="8" align="center">No Data Available!</td>
                        </tr>
                        @endif
                      </tbody>
                  </table>
                  <nav aria-label="Page navigation example">
                      {!! $contacts->links() !!}
                </nav>

              </div>
          </div>
      </div>   
  </div>
</div>
@endsection