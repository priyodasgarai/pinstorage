<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected $data = [];
    protected $response = [];
    protected $userId;
    protected $roleId;
    protected $object = [];
    protected $limit  = 10;
    protected $fileName = null;
    protected $fileName1 = null;
    protected $fileArr = [];

    protected function validateAppkey($keyInRequest)
    {
    	$appkey = env('APP_KEY');
    	$getKey = explode(':',$appkey);
    	if($getKey[1] != $keyInRequest):
    		return FALSE;
    	else:
    		return TRUE;
    	endif;
    }
    protected function pushNotification($deviceToken = '', $messageData =[]){
        $fields = [
            'to'            => $deviceToken,
            'notification'  => $messageData
        ];
        $headers = [
            'Authorization: key=' .env('API_ACCESS_KEY'),
            'Content-Type: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('FCM_URL'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
    }


}
