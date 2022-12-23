<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Delivery;
class DeliveryManagement extends Controller
{
    public function deliveryList(Request $request)
    {
    	if($request->isMethod('post')):
    		$validator = Validator::make($request->all(), [
                'delivery_title' => 'required',
                'price' => 'required'
            ]);
            if($validator->fails()):
                return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Please Input Valid Credentials',
                                'redirect'  => ''
                        ]
                    ,200);
            else:
            	if(is_null($request->input('id'))):
            	     if(Delivery::where('delivery_title', $request['delivery_title'])->where('status','!=','3')->exists()):
                        return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Delivery Title Already Exist !!',
                                    'redirect'  => ''
                            ]
                        ,200);
                    endif;
            		Delivery::create([
                            'delivery_title'		=>  $request->input('delivery_title'),
                            'day'                   =>  $request->input('day'),
                            'price'					=>	$request->input('price'),
                            'delivery_description' 	=>  json_encode($request->input('delivery_description')),
                            'created_by'    =>Auth::user()->id
                        ]);
                        return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Delivery Service Added Successfully !!',
                                    'redirect'  => 'delivery-management/list'
                            ]
                        ,200);
                else:
                    if(Delivery::where('delivery_title', $request['delivery_title'])->where('id','<>',$request->input('id'))->where('status','!=','3')->exists()):
                        return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Delivery Title Already Exist !!',
                                'redirect'  => ''
                        ]
                        ,200);
                    endif;
                    Delivery::where('id',$request->input('id'))->update([
                            'delivery_title'        =>  $request->input('delivery_title'),
                            'day'                   =>  $request->input('day'),
                            'price'                 =>  $request->input('price'),
                            'delivery_description'  =>  json_encode($request->input('delivery_description')),
                            'updated_by'            =>  Auth::user()->id
                        ]);
                    if($request->has('flag') && $request->flag == 1):
                        return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Service Description Removed Successfully!',
                                    'redirect'  => 'delivery-management/edit/'.$request->input('id')
                            ]
                        ,200);
                    endif;
                    return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Delivery Service Updated Successfully !!',
                                    'redirect'  => 'delivery-management/list'
                            ]
                    ,200);
            	endif;
            endif;
    	endif;
    	$this->data['title']='Delivery List';
		//$this->data['deliveryList'] = Delivery::where('status','!=','3')->paginate($this->limit);
	  	return view('pages.delivery.list')->with($this->data);
    }
    public function ajaxDeliveryDataTable(Request $request)
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
            $totalRecords = Delivery::select('count(*) as allcount')->where('status','!=',3)->count();
            $totalRecordswithFilterQuery = Delivery::select('count(*) as allcount');
            if($request->post('searchTitle')!='' || !is_null($request->post('searchTitle'))):
                $totalRecordswithFilterQuery->where('delivery_title', 'like', '%' .$request->post('searchTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $totalRecordswithFilterQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecordswithFilter = $totalRecordswithFilterQuery->where('status','!=',3)->count();
            $recordsQuery = Delivery::orderBy($columnName,$columnSortOrder);
            if($request->post('searchTitle')!='' || !is_null($request->post('searchTitle'))):
                $recordsQuery->where('delivery_title', 'like', '%' .$request->post('searchTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $records =$recordsQuery->where('status','!=',3)->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="deliveries" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="deliveries" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                endif;
                $action = '<a href="'.url('delivery-management/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                <a href="javascript:void(0)" id="'.$value->id.'" data-table="deliveries" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                $deliveryDescriptions = json_decode($value->delivery_description);
                $html = "";
                if(!empty($deliveryDescriptions)):
                    $html .='<ol>';
                            foreach ($deliveryDescriptions as $key1 => $value1):
                               $html .='<li>'.($value1!='')?substr($value1, 0,100).'...':''.'</li>';
                            endforeach;
                    $html .='</ol>';
                endif;
                $tempArr[] = [
                           "id"                 => ($key+1),
                           "delivery_title"     => $value->delivery_title,
                           "delivery_description"=> $html,
                           "price"               => $value->price,
                           "day"                =>$value->day,
                           "created_at"          => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"              => $status,
                           "action"              => $action
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
    public function deliveryAdd($id=null)
    {
       	if(!is_null($id)):
       		$this->data['title']='Delivery Service Edit';
			$this->data['data'] = Delivery::find($id);
       	else:
       		$this->data['title']='Delivery Service Add';
       	endif;
       	return view('pages.delivery.add')->with($this->data);	
    }   
}
