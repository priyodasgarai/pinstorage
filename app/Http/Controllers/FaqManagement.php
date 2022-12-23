<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Faq;
use Hash;

class FaqManagement extends Controller
{
    public function faqList(Request $request)
    {
    	if($request->isMethod('post')):
    		 $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer' => 'required'
            ]);
	    		if($validator->fails()):
	                return response()->json([
	                            'status'    => FALSE,
	                            'message'   => 'Please Input Valid Credentials',
	                            'redirect'  => ''
	                        ]
	                 ,200);
	            else:
	            	$requestData = $request->all();
                	$id = $requestData['id'];
                	if(is_null($id)):
                		Faq::create([
                			'question'=>$requestData['question'],
                			'answer'=>$requestData['answer']
                		]);
                		return response()->json([
	                            'status'    => TRUE,
	                            'message'   => 'Faq Added Successfully !!',
	                            'redirect'  => 'faq-management/list'
	                        ]
	                 	,200);
                	else:
                		Faq::where('id',$id)->update([
                			'question'=>$requestData['question'],
                			'answer'=>$requestData['answer']
                		]);
                		return response()->json([
	                            'status'    => TRUE,
	                            'message'   => 'Faq Updated Successfully !!',
	                            'redirect'  => 'faq-management/list'
	                        ]
	                 	,200);
                	endif;
	            endif;
    	endif;
    	$this->data['title']='Faq List';
		$this->data['faqList']=Faq::where('status','!=','3')->paginate(5);
	  	return view('pages.faq.list')->with($this->data);
    }
    public function ajaxDataTable(Request $request)
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

            $recordsQuery = Faq::orderBy($columnName,$columnSortOrder);
            if($request->post('searchFaqTitle')!='' || !is_null($request->post('searchFaqTitle'))):
                $recordsQuery->where('question', 'like', '%' .$request->post('searchFaqTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $query = $recordsQuery->where('status','!=',3)->skip($start)->take($rowperpage);
            $totalRecords =  $query->count();
            $records = $query->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="faqs" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="faqs" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                endif;

                $action = '<a href="'.url('faq-management/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="faqs" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';

                $tempArr[] = [
                           "id"             => ($key+1),
                           "question"   => $value->question,
                           'answer'=>$value->answer,
                            "created_at"     => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"         => $status,
                           "action"         => $action
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

    public function faqAdd($id=null)
    {
    	if(!is_null($id)):
    		$this->data['title']='Faq Edit';
			$this->data['data']=Faq::find($id);
    	else:
    		$this->data['title']='Faq Add';
    	endif;
    	return view('pages.faq.add')->with($this->data);
    }
}
