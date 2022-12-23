<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\AlterationRequest;
use App\Models\User;
use Hash;

class Alteration extends Controller
{
    public function alterationList(Request $request)
    {
    	if($request->isMethod('post')):
    		$validator = Validator::make($request->all(), [
                'alteration_price' => 'required'
            ]);
            if($validator->fails()):
                return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Please Input Valid Credentials',
                                'redirect'  => ''
                        ]
                    ,200);
            else:
            	if(!is_null($request->input('alterationId'))):
            		 AlterationRequest::where('id','=',$request->input('alterationId'))->update([
                            'alteration_price'=>$request->input('alteration_price'),
                            'status'=>3
                        ]);
            		  return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Alteration Price Added Successfully !!',
                                    'redirect'  => 'customer-alteration-request/list'
                            ]
                        ,200);
                    
            	endif;
            endif;
    	endif;
    	$this->data['title']='Alteration List';
    	$this->data['alterationList']=AlterationRequest::select('alteration_requests.*','users.name as userName','users.email as userEmail','users.role_id')->join('users','users.id','=','alteration_requests.user_id','INNER')->where('users.role_id','=','3')->orderby('alteration_requests.id','desc')->paginate($this->limit);
    	//pr($this->data['alterationList']);
    	//pr($this->data['contactList']);
        return view('pages.alteration_request.list')->with($this->data);
    }
    public function alterationListEdit($id=null)
    {
    	if(!is_null($id)):
    		$this->data['title']='Alteration Request View';
    		$this->data['data'] = AlterationRequest::find($id);
    		//pr($this->data['data']);
    	else:
    		$this->data['title']='Alteration Request View';
    	endif;
        return view('pages.alteration_request.price-edit')->with($this->data);
    }
}
