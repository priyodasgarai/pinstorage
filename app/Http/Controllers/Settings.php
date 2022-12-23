<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\AppSettings;
use Hash;

class Settings extends Controller
{
    public function appSettings(Request $request)
    {
    	if($request->isMethod('post')):
    		$validator = Validator::make($request->all(), [
                'lining_cost' => 'required'
            ]);
            if($validator->fails()):
                return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Please Input Valid Credentials',
                                'redirect'  => ''
                        ]
                    ,200);
            else:
            	AppSettings::where('id','1')->update([
            		'company_mail'=>$request->input('company_mail'),
            		'company_phone'=>$request->input('company_phone'),
            		'lining_cost'=>$request->input('lining_cost'),
        		    'alteration_cost'=>$request->input('alteration_cost'),
                    'padded_cost'=>$request->input('padded_cost'),
                    'order_id_prefix'=>$request->input('order_id_prefix'),
                    'assignment_status'=>$request->input('assignment_status')
            	]);
            	return response()->json([
                                'status'    => TRUE,
                                'message'   => 'App Settings Updated Successfully !!',
                                'redirect'  => 'app-settings'
                            ]
                        ,200);
            endif;
    	endif;
        $this->data['title']='App Settings';
        $this->data['appSettings']=AppSettings::select('*')->where('id','1')->first();
        //pr($this->data['appSettings']);
        return view('pages.app-settings')->with($this->data);
    }
}
