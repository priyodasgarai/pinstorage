<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderByAgentAssign;
use App\Models\AppSettings;
use App\Mail\Mailer;
use Illuminate\Support\Facades\DB;
use Hash;
use Mail;
use PDF;
use Dompdf\Dompdf;

class OrderManagement extends Controller
{
   public function orderList(Request $request)
    {
        if($request->isMethod('post')):
            $validator = Validator::make($request->all(), [
                'order_prefix_id' => 'required'
            ]);
            if($validator->fails()):
                return response()->json([
                                'status'    => FALSE,
                                'message'   => $validator->errors(),
                                'redirect'  => ''
                        ]
                    ,200);
            else:

                if(!is_null($request->input('updateId'))):
                        Order::where('id','=',$request->input('updateId'))->update([
                            'created_by'    =>  Auth::user()->id
                        ]);
                        //DB::enableQueryLog();

                        OrderByAgentAssign::updateOrCreate(['order_id'=>$request->input('updateId')],[
                            'order_id'       => $request->input('updateId'),
                            'user_id'        =>  !is_null($request->input('user_id'))?$request->input('user_id'):'',
                            'created_by'     => Auth::user()->id
                        ]);
                        //dd(DB::getQueryLog());
                        return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Order Status Updated Successfully !!',
                                    'redirect'  => 'orders/list'
                            ]
                        ,200);

                endif;
            endif;
        endif;
    	$this->data['title']='Order List';
	  	return view('pages.orders.list')->with($this->data);
    }
    public function ajaxOrderDataTable(Request $request)
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
            $totalRecords = Order::count();
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
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            if($request->post('searchStatus')!=''):
                $recordsQuery->where('status', $request->post('searchStatus'));
            endif;
            $totalRecordswithFilter = $recordsQuery->count();
            $records =$recordsQuery->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-success">Placed</a>';
                elseif($value->status == 0):
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-danger">Canceled</a>';
                elseif($value->status == 2):
                $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-primary">Out For Measurement</a>';
            	elseif($value->status == 3):
                $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-info">Arrived Tomorrow</a>';
            	elseif($value->status == 4):
                $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-warining">Out For Delivery</a>';
                elseif($value->status == 5):
                    $status = '<a href="javascript:void(0)" class="badge badge-warining">Deliverd</a>';
                endif;
                /*if($value->image && checkFileDirectory($value->image,'uploads/category')):
                    $image='<img src="'.(asset('uploads/category/'.$value->image)).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                endif;*/
                $action = '<a href="' . url('orders/edit/' . $value->id) . '" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> | <a href="' . url('orders/show/' . $value->id) . '" class="btn btn-info"><i class="fa fa-eye" aria-hidden="true"></i></a>
                <a href="javascript:void(0)" data-table="orders" data-status="0" data-key="id" data-id="' . $value->id . '" class="btn btn-danger change-status"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>';
                      if($value->due_price <> $value->price){
                  $action .= ' | <a href="' . url('orders/invoice/' . $value->id) . '" class="btn btn-info"><i class="fa fa-print" aria-hidden="true"></a>';
                      }
                $tempArr[] = [
                    "id" => ($key + 1),
                    "order_prefix_id" => $value->order_prefix_id,
                    "design" =>  $value->product?$value->product->title : "",
                    "name" => $value->user->name,
                    "email" => $value->user->email,
                    "phone" => $value->user->phone,
                    "price" => '₹'.$value->price,
                    "paid_amount" => '₹'.($value->price -$value->due_price),
                    "created_at" => date('d-m-Y h:i:s', strtotime($value->created_at)),
                    "status" => $status,
                    "action" => $action
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
        public function CustomerOrderDetailsInvoice($id) {
        $orderDetails = Order::with([
            'OrderStatus'=>function ($query1) {
                                $query1->orderby('created_at', 'DESC');
                                $query1->select('id','created_at','order_status','order_id');
                                 $query1->take(1);
                            }
            ,'user'=>function ($query1) {
                                $query1->select('id','name','phone','role_id','email','address','pincode','city_id','state_id','country_id');
                            }
            ,'user.city'=>function ($query1) {
                                $query1->select('id','name');
                            }
            ,'user.state'=>function ($query1) {
                                $query1->select('id','name');
                            }
            ,'user.country'=>function ($query1) {
                                $query1->select('id','name');
                            }
            ])->find($id);
          //  dd($orderDetails);
        if ($orderDetails->due_price == $orderDetails->amount) {
            return back();
        } elseif ($orderDetails->due_price < $orderDetails->amount && $orderDetails->due_price > 0) {
            $orderDetails->amount = $orderDetails->due_price;
            $data['title'] = 'Porforma Invoice';
            $data['orderDetails'] = $orderDetails;
        } else {
            $data['title'] = 'INVOICE';
            $data['orderDetails'] = $orderDetails;
        }
        $pdf = PDF::loadView('pages.orders.order_invoice', $data);
        $file_name = "orders_".$orderDetails->order_prefix_id.".pdf";
        return $pdf->download($file_name);
    }

    public function orderAdd($id=null)    {
    	if(!is_null($id)):
    		$this->data['title']='Order Edit';
            $this->data['details'] = Order::find($id);
            $this->data['orderAssignDetails'] = OrderByAgentAssign::where('order_id','=',$id)->first();
    	else:
    		$this->data['title']='Order Add';
    	endif;
        $this->data['settings']=AppSettings::where('id','=','1')->first();
        //pr($this->data['settings']);
        $this->data['agentList']=User::where('role_id','=','2')->get();
    	return view('pages.orders.add')->with($this->data);
    }


    public function ordershow($id){
        $data['title']='Order Details';
        $orderDetails=Order::with(['productDesign','shipping','deliveryDetails','measurement_address','user'])->find($id);
         $data['orderDetails']=$orderDetails;
         $data['designImage']=DB::table('product_design_images')->where('product_design_id',$orderDetails->design_id)->where('is_primary',1)->first();
        return view('pages.orders.details',$data);
        //return $orderDetails;
    }
    public function exportFile(Request $request) {
        $customers = order::with([
            'OrderStatus' => function ($query) {
                $query->select('id', 'order_id', 'order_status', 'created_at');
                $query->OrderBy('order_statuses.created_at', 'DESC');
                $query->first();
            },
            'user'=> function ($query) {
                $query->select('id', 'name', 'role_id', 'email','phone','address');
            },
            'product'=> function ($query) {
                $query->select('id', 'title');
            },
            'pickup_schedulings'=> function ($query) {
                $query->select('id', 'order_id','exp_delivery_date');
            },
        ])
            ->select('id', 'user_id', 'order_prefix_id','price','design_id')
            ->orderBy('id','DESC')
            ->get();
        // return $customers;
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Sl. No.');
        $sheet->setCellValue('B1', 'Order Id');
        $sheet->setCellValue('C1', 'Amount');
        $sheet->setCellValue('D1', 'Design Name');
        $sheet->setCellValue('E1', 'Expected Delivery Date');
        $sheet->setCellValue('F1', 'Customer Name');
        $sheet->setCellValue('G1', 'Customer Email');
        $sheet->setCellValue('H1', 'Customer Phone');
        $sheet->setCellValue('I1', 'Customer Address');
        $sheet->setCellValue('J1', 'Created On');
        $sheet->setCellValue('K1', 'Status');
        if (count($customers) > 0):
            $i = 2;
            foreach ($customers as $key => $value):
                if(isset($value->OrderStatus[0])){
                $orderStatus = $value->OrderStatus[0];
                }else{
                    $orderStatus = array();
                }
                $sheet->setCellValue('A' . $i, ($key + 1));
                $sheet->setCellValue('B' . $i, $value->order_prefix_id);
                $sheet->setCellValue('C' . $i, '₹'.$value->price);
                $sheet->setCellValue('D' . $i, isset($value->product)?$value->product->title:'');
                $sheet->setCellValue('E' . $i, isset($value->pickup_schedulings)?date('d-m-y h:i:s', strtotime($value->pickup_schedulings->exp_delivery_date)):'');
                $sheet->setCellValue('F' . $i, isset($value->user)?$value->user->name:'');
                $sheet->setCellValue('G' . $i, isset($value->user)?$value->user->email:'');
                $sheet->setCellValue('H' . $i, isset($value->user)?$value->user->phone:'');
                $sheet->setCellValue('I' . $i, isset($value->user)?$value->user->address:'');
                $sheet->setCellValue('J' . $i, isset($orderStatus->created_at)?date('d-m-y h:i:s', strtotime($orderStatus->created_at)):'');
                if(isset($orderStatus->order_status)){
                    if($orderStatus->order_status == 1):
                        $status = 'Placed';
                    elseif($orderStatus->order_status == 0):
                        $status = 'Canceled';
                    elseif($orderStatus->order_status == 2):
                        $status = 'Out For Measurement';
                    elseif($orderStatus->order_status == 3):
                        $status = 'Arrived Tomorrow';
                    elseif($orderStatus->order_status == 4):
                        $status = 'Out For Delivery';
                    elseif($orderStatus->order_status == 5):
                        $status = 'Deliverd';
                    endif;
                    $sheet->setCellValue('K' . $i, $status);
                }else{
                    $sheet->setCellValue('K' . $i, 'Order Not Placed');
                }
                $i++;
            endforeach;
        endif;
        $fileName = "Order List [" . date('d-m-Y h:i a') . "].xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
     public function exportTransactionFile(Request $request) {
        $customers = order::with([
            'OrderStatus' => function ($query) {
                $query->select('id', 'order_id', 'order_status', 'created_at');
                $query->OrderBy('order_statuses.created_at', 'DESC');
                $query->first();
            },
            'user'=> function ($query) {
                $query->select('id', 'name', 'role_id', 'email','phone','address');
            },
            'product'=> function ($query) {
                $query->select('id', 'title');
            },
            'pickup_schedulings'=> function ($query) {
                $query->select('id', 'order_id','exp_delivery_date');
            },
        ])
            ->select('id', 'user_id', 'order_prefix_id','price','design_id','created_at','due_price')
            ->whereRaw('orders.price > orders.due_price')
            ->orderBy('id','DESC')
            ->get();
        // return $customers;
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Sl. No.');
        $sheet->setCellValue('B1', 'Order Id');
        $sheet->setCellValue('C1', 'Order Date');
        $sheet->setCellValue('D1', 'Amount');
        $sheet->setCellValue('E1', 'Design Name');
        $sheet->setCellValue('F1', 'Expected Delivery Date');
        $sheet->setCellValue('G1', 'Customer Name');
        $sheet->setCellValue('H1', 'Customer Email');
        $sheet->setCellValue('I1', 'Customer Phone');
        $sheet->setCellValue('J1', 'Customer Address');
        $sheet->setCellValue('K1', 'Paid Amount');
        $sheet->setCellValue('L1', 'Due Amount');
        $sheet->setCellValue('M1', 'Payment Method');

        if (count($customers) > 0):
            $i = 2;
            foreach ($customers as $key => $value):
                $sheet->setCellValue('A' . $i, ($key + 1));
                $sheet->setCellValue('B' . $i, $value->order_prefix_id);
                $sheet->setCellValue('C' . $i, isset($value->created_at)?date('d-m-y', strtotime($value->created_at)):'');
                $sheet->setCellValue('D' . $i, '₹'.$value->price);
                $sheet->setCellValue('E' . $i, isset($value->product)?$value->product->title:'');
                $sheet->setCellValue('F' . $i, isset($value->pickup_schedulings)?date('d-m-y h:i:s', strtotime($value->pickup_schedulings->exp_delivery_date)):'');
                $sheet->setCellValue('G' . $i, isset($value->user)?$value->user->name:'');
                $sheet->setCellValue('H' . $i, isset($value->user)?$value->user->email:'');
                $sheet->setCellValue('I' . $i, isset($value->user)?$value->user->phone:'');
                $sheet->setCellValue('J' . $i, isset($value->user)?$value->user->address:'');
                $sheet->setCellValue('K' . $i, '₹'.($value->price - $value->due_price));
                $sheet->setCellValue('L' . $i, '₹'.$value->due_price);
                    if($value->payment_method == 1):
                        $payment_method = 'Online';
                    elseif($value->payment_method == 0):
                        $payment_method = 'Offline';
                    endif;
                    $sheet->setCellValue('M' . $i, $payment_method);

                $i++;
            endforeach;
        endif;
        $fileName = "Transaction List [" . date('d-m-Y h:i a') . "].xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
