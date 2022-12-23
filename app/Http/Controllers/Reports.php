<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Mail\Mailer;
use App\Models\Order;
use Hash;
use Mail;

class Reports extends Controller
{
	public function customerReportList(Request $request)
    {
	    $this->data['title']='Customer Report';
	  	return view('pages.reports.customer-report')->with($this->data);
    }
    public function ajaxCustomerReportDataTable(Request $request)
    {
        if($request->ajax()):
            $draw               = $request->post('draw');
            $start              = $request->post("start");
            $rowperpage         = $request->post("length"); // Rows display per page
            $columnIndexArr     = $request->post('order');
            $columnNameArr      = $request->post('columns');
            $orderArr           = $request->post('order');
            $searchArr          = $request->post('search');
            $columnIndex        = $columnIndexArr[0]['column']; // Column index
            $columnName         = $columnNameArr[$columnIndex]['data']; // Column name
            $columnSortOrder    = $orderArr[0]['dir']; // asc or desc
            $searchValue        = $searchArr['value']; // Search value
            // Total records
            $totalRecords = User::select('count(*) as allcount')->where('status','!=',3)->where('role_id',3)->count();
            $totalRecordswithFilterQuery = User::select('count(*) as allcount');
            if($request->post('searchEmail')!='' || !is_null($request->post('searchEmail'))):
                $totalRecordswithFilterQuery->where('email', 'like', '%' .$request->post('searchEmail'). '%');
            endif;
            if($request->post('searchName')!='' || !is_null($request->post('searchName'))):
                $totalRecordswithFilterQuery->where('name', 'like', '%' .$request->post('searchName'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $totalRecordswithFilterQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecordswithFilter = $totalRecordswithFilterQuery->where('status','!=',3)->where('role_id',3)->count();
            $recordsQuery = User::orderBy($columnName,$columnSortOrder);
            if($request->post('searchEmail')!='' || !is_null($request->post('searchEmail'))):
                $recordsQuery->where('email', 'like', '%' .$request->post('searchEmail'). '%');
            endif;
            if($request->post('searchName')!='' || !is_null($request->post('searchName'))):
                $recordsQuery->where('name', 'like', '%' .$request->post('searchName'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $records =$recordsQuery->where('status','!=',3)->where('role_id',3)->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" class="badge badge-primary">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)"  class="badge badge-danger">Inactive</a>';
                endif;
               /* $action = '<a href="'.url('customer-management/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="users" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';*/
                $tempArr[] = [
                           "id"             => ($key+1),
                           "name"           => $value->name,
                           "email"          => $value->email,
                           "created_at"     => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"         => $status
                        ];
            endforeach;
            return response()->json([
                                   "draw" => intval($draw),
                                   "recordsTotal" => intval($totalRecords),
                                   "recordsFiltered" => intval($totalRecordswithFilter),
                                   "data" => $tempArr
                                ]
                        ,200);
        else:
            return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Bad Request !!',
                                    'redirect'  => ''
                            ]
                        ,400);
        endif;
    }
    public function agentReportList(Request $request)
    {
	    $this->data['title']='Agent Report';
	  	return view('pages.reports.agent-report')->with($this->data);
    }
    public function ajaxAgentReportDataTable(Request $request)
    {
        // pr($request->all());
        if($request->ajax()):
            $draw               = $request->post('draw');
            $start              = $request->post("start");
            $rowperpage         = $request->post("length"); // Rows display per page
            $columnIndexArr     = $request->post('order');
            $columnNameArr      = $request->post('columns');
            $orderArr           = $request->post('order');
            $searchArr          = $request->post('search');
            $columnIndex        = $columnIndexArr[0]['column']; // Column index
            $columnName         = $columnNameArr[$columnIndex]['data']; // Column name
            $columnSortOrder    = $orderArr[0]['dir']; // asc or desc
            $searchValue        = $searchArr['value']; // Search value
            // Total records
            $totalRecords = User::select('count(*) as allcount')->where('status','!=',3)->where('role_id',2)->count();
            $totalRecordswithFilterQuery = User::select('count(*) as allcount');
            $totalRecordswithFilterQuery = User::select('count(*) as allcount');
            if($request->post('searchEmail')!='' || !is_null($request->post('searchEmail'))):
                $totalRecordswithFilterQuery->where('email', 'like', '%' .$request->post('searchEmail'). '%');
            endif;
            if($request->post('searchName')!='' || !is_null($request->post('searchName'))):
                $totalRecordswithFilterQuery->where('name', 'like', '%' .$request->post('searchName'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $totalRecordswithFilterQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecordswithFilter = $totalRecordswithFilterQuery->where('status','!=',3)->where('role_id',2)->count();
            $recordsQuery = User::orderBy($columnName,$columnSortOrder);
            if($request->post('searchEmail')!='' || !is_null($request->post('searchEmail'))):
                $recordsQuery->where('email', 'like', '%' .$request->post('searchEmail'). '%');
            endif;
            if($request->post('searchName')!='' || !is_null($request->post('searchName'))):
                $recordsQuery->where('name', 'like', '%' .$request->post('searchName'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $records =$recordsQuery->where('status','!=',3)->where('role_id',2)->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)"  class="badge badge-primary">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)"  class="badge badge-danger">Inactive</a>';
                endif;
                /*$action = '<a href="'.url('delivery-agent/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="users" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';*/
                $tempArr[] = [
                           "id"             => ($key+1),
                           "name"           => $value->name,
                           "email"          => $value->email,
                           "created_at"     => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"         => $status
                        ];
            endforeach;
            return response()->json([
                                   "draw" => intval($draw),
                                   "recordsTotal" => intval($totalRecords),
                                   "recordsFiltered" => intval($totalRecordswithFilter),
                                   "data" => $tempArr
                                ]
                        ,200);
        else:
            return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Bad Request !!',
                                    'redirect'  => ''
                            ]
                        ,400);
        endif;
    }
     public function ordersReportList(Request $request) {
        $this->data['title'] = 'Order Report';
        return view('pages.reports.order-report')->with($this->data);
    }

    public function ajaxOrdersReportDataTable(Request $request) {
        //dd(1);

        if($request->ajax()):
            $draw               = $request->post('draw');
            $start              = $request->post("start");
            $rowperpage         = $request->post("length"); // Rows display per page
            $columnIndexArr     = $request->post('order');
            $columnNameArr      = $request->post('columns');
            $orderArr           = $request->post('order');
            $searchArr          = $request->post('search');
            $columnIndex        = $columnIndexArr[0]['column']; // Column index
            $columnName         = $columnNameArr[$columnIndex]['data']; // Column name
            $columnSortOrder    = $orderArr[0]['dir']; // asc or desc
            $searchValue        = $searchArr['value']; // Search value
            $totalRecords = array();
            $recordsQuery = Order::with([
                'product' => function ($query1) {
                    $query1->select('id', 'category_id', 'title');
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'role_id', 'email', 'phone', 'address');
                }
            ])->orderBy($columnName,$columnSortOrder);
             if($request->post('searchOrderId')!=''):
                $recordsQuery->where('order_prefix_id', 'like', '%' .$request->post('searchOrderId'). '%');
            endif;
            if($request->post('searchStatus')!=''):
                $recordsQuery->where('status', $request->post('searchStatus'));
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecords = $recordsQuery->count();
            $records =$recordsQuery->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" class="badge badge-success">Placed</a>';
                elseif($value->status == 0):
                    $status = '<a href="javascript:void(0)" class="badge badge-danger">Canceled</a>';
                elseif($value->status == 2):
                $status = '<a href="javascript:void(0)" class="badge badge-primary">Out For Measurement</a>';
            	elseif($value->status == 3):
                $status = '<a href="javascript:void(0)" class="badge badge-info">Arrived Tomorrow</a>';
            	elseif($value->status == 4):
                $status = '<a href="javascript:void(0)" class="badge badge-warining">Out For Delivery</a>';
                elseif($value->status == 5):
                    $status = '<a href="javascript:void(0)" class="badge badge-warining">Deliverd</a>';
                endif;
                /*if($value->image && checkFileDirectory($value->image,'uploads/category')):
                    $image='<img src="'.(asset('uploads/category/'.$value->image)).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                endif;*/
                $action = '<a href="'.url('orders/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';

                $tempArr[] = [
                    "id" => ($key + 1),
                    "order_prefix_id" => $value->order_prefix_id,
                    "design" =>  $value->product?$value->product->title : "",
                    "name" => $value->user->name,
                    "email" => $value->user->email,
                    "phone" => $value->user->phone,
                    "price" => '₹'.$value->price,
                    "due_price"  => '₹'.$value->due_price,
                    "created_at" => date('d-m-Y h:i:s', strtotime($value->created_at)),
                    "status" => $status,
                    "action" => $action
                ];
            endforeach;
            return response()->json([
                                   "draw" => intval($draw),
                                   "recordsTotal" => intval($totalRecords),
                                   "recordsFiltered" => intval($totalRecords),
                                   "data" => $tempArr
                                ]
                        ,200);
        else:
            return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Bad Request !!',
                                    'redirect'  => ''
                            ]
                        ,400);
        endif;
    }
       public function transactionList(Request $request) {
        $this->data['title'] = 'Transaction Report';
        return view('pages.reports.transaction-report')->with($this->data);
    }

    public function ajaxTransactionReportDataTable(Request $request) {
        if($request->ajax()):
            $draw               = $request->post('draw');
            $start              = $request->post("start");
            $rowperpage         = $request->post("length"); // Rows display per page
            $columnIndexArr     = $request->post('order');
            $columnNameArr      = $request->post('columns');
            $orderArr           = $request->post('order');
            $searchArr          = $request->post('search');
            $columnIndex        = $columnIndexArr[0]['column']; // Column index
            $columnName         = $columnNameArr[$columnIndex]['data']; // Column name
            $columnSortOrder    = $orderArr[0]['dir']; // asc or desc
            $searchValue        = $searchArr['value']; // Search value
            $totalRecords = array();
            $recordsQuery = Order::with([
                'product' => function ($query1) {
                    $query1->select('id', 'category_id', 'title');
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'role_id', 'email', 'phone', 'address');
                }
            ])
            ->whereRaw('orders.price > orders.due_price')
            ->orderBy($columnName,$columnSortOrder);
           // return $recordsQuery->toSql();
             if($request->post('searchOrderId')!=''):
                $recordsQuery->where('order_prefix_id', 'like', '%' .$request->post('searchOrderId'). '%');
            endif;
            if($request->post('searchStatus')!=''):
                $recordsQuery->where('status', $request->post('searchStatus'));
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecords = $recordsQuery->count();
            $records =$recordsQuery->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                // if($value->status == 1):
                //     $status = '<a href="javascript:void(0)" class="badge badge-success">Placed</a>';
                // elseif($value->status == 0):
                //     $status = '<a href="javascript:void(0)" class="badge badge-danger">Canceled</a>';
                // elseif($value->status == 2):
                // $status = '<a href="javascript:void(0)" class="badge badge-primary">Out For Measurement</a>';
            	// elseif($value->status == 3):
                // $status = '<a href="javascript:void(0)" class="badge badge-info">Arrived Tomorrow</a>';
            	// elseif($value->status == 4):
                // $status = '<a href="javascript:void(0)" class="badge badge-warining">Out For Delivery</a>';
                // elseif($value->status == 5):
                //     $status = '<a href="javascript:void(0)" class="badge badge-warining">Deliverd</a>';
                // endif;
                /*if($value->image && checkFileDirectory($value->image,'uploads/category')):
                    $image='<img src="'.(asset('uploads/category/'.$value->image)).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                endif;*/
                $action = '<a href="'.url('orders/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                if($value->payment_method == 1):
                    $payment_method = "Online";
                elseif($value->payment_method == 0):
                    $payment_method =  "Offline";
                endif;
                $tempArr[] = [
                    "id" => ($key + 1),
                    "order_prefix_id" => $value->order_prefix_id,
                    "design" =>  $value->product?$value->product->title : "",
                    "name" => $value->user->name,
                    "email" => $value->user->email,
                    "phone" => $value->user->phone,
                    "price" => '₹'.$value->price,
                    "paid_price" => '₹'.($value->price - $value->due_price),
                    "due_price" => '₹'.$value->due_price,
                    "paymentMethod" => $payment_method,
                    "created_at" => date('d-m-Y', strtotime($value->created_at)),
                   // "action" => $action
                ];
            endforeach;
            return response()->json([
                                   "draw" => intval($draw),
                                   "recordsTotal" => intval($totalRecords),
                                   "recordsFiltered" => intval($totalRecords),
                                   "data" => $tempArr
                                ]
                        ,200);
        else:
            return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Bad Request !!',
                                    'redirect'  => ''
                            ]
                        ,400);
        endif;
    }
}
