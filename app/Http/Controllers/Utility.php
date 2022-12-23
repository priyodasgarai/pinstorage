<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\User;
use Hash;


class Utility extends Controller
{





    public function genericApprovalChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required',
            'keyId'     => 'required',
            'approval'    => 'required',
            'table'     => 'required',
        ]);
        if($validator->fails()):
            return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Please Input Valid Credentials',
                            'redirect'  => ''
                    ]
                ,200);
        else:
            try{
                DB::table($request->table)->where($request->keyId,$request->id)->update(['approval'=>$request->approval]);
                return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Request processed Successfully!',
                            'redirect'  => '',
                            'postApproval'=>$request->approval
                    ]
                ,200);
            }catch(\Exception $e){
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'redirect'  => ''
                ], 500);
            }
        endif;
    }
}
