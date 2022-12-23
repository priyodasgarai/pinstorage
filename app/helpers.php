<?php
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

function get_admin(){
    $user = User::where(['role_id'=>1])->first();
    return $user;
}
function get_agent($id){
    $user = User::where(['id'=>$id])->first();
    return $user;
}
function couponCode(){
    return 'WAH' . rand(10000, 99999);
}
function save_notification($data){
        $query = new Notification();
        $query->user_id = $data['user_id'];
        $query->title = $data['title'];
        $query->message = $data['message'];       
        $result = $query->save();
        return $result;
}
function send_notification($device_token,  $title, $message) {
    $result_noti = 0;
    // $accesstoken = getenv('FCM_KEY');
    $accesstoken = 'AAAAnRLCHBg:APA91bEY0-ndEhSYns5dm6JrKCvMzm7Kdxmh1KXe57dWbu_h0MXbO9mPzQgznyrZL3ZNUlDT4s39ge98YDWIFTrNHAu_p_BE9s6LZWuodTqXAVuwRumZmyMohAXo1cH5aNWIPqfnMzg4';
  //  $device_token="cK1rdobrQEKIt8tEi8ahDQ:APA91bG9BEbzRTdlxTWFZyVieFZF_rOSMrf2tJoljyzUvPhsrFUml6UTzZSJZAefcMfx3s16OYilkc-fvu-I4Mf-ePYEQbGTYA3R3zzpDPbg1ePQXMIVMWpzAA2jEB2z6-_Kqa6JQ6or";
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $post_data = '{
          "to" : "' . $device_token . '",
          "data" : {
            "body" : {
              "title" : "' . $title . '",
              "object_type" : "$object_type",
              "object_id" : "object_id",
              "message" : "' . $message . '",
              "user_type" : "user_type",
              "content_available" : true,
              "priority" : "high"
            }
          },
          "notification" : {
              "body" : "' . $message . '",
              "title" : "' . $title . '",
              "object_type" : "object_type",
              "object_id" : "object_id",
              "message" : "' . $message . '",
              "user_type" : "user_type",
              "icon" : "new",
              "sound" : "default"
            },

        }';
        // print_r($post_data);die;
        Log::info('Request URL: ' . $URL);
        Log::info('Request Method: POST');
        Log::info('Request Body: ' . json_encode($post_data));

        $crl = curl_init();

        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: key=' . $accesstoken;

        Log::info('Request Header: ' . json_encode($headr));

        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);

        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $rest = curl_exec($crl);

        Log::info('Response: ' . json_encode($rest));

        if ($rest === false) {
            // throw new Exception('Curl error: ' . curl_error($crl));
            //print_r('Curl error: ' . curl_error($crl));
            $result_noti = 0;
        } else {

            $result_noti = 1;
        }

        curl_close($crl);
 
   
    return $result_noti;
}
function sendWhatsappSms($data) {
        return true;
        $message = $data['message'];
        $key="f6a389c7bd2a4cda829ed940a830b517";
        $mobile= $data['mobile'];            
        $url = "http://whatsappapi.fastsmsindia.com/wapp/api/send?apikey={$key}&mobile={$mobile}&msg={$message}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);           
        return $result;      
    }
function sendSms($data) {
        $message = $data['message'];
        $key="0753fc2de37a4dcd9976da8b79190534";
        $mobile= $data['mobile'];            
        $url = "http://whatsappapi.fastsmsindia.com/wapp/api/send?apikey={$key}&mobile={$mobile}&msg={$message}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);           
        return $result;      
    }
    
function pr($arr=[],$mode=FALSE){
	if(!$mode):
		echo "<pre>";
		print_r($arr);
		die;
	else:
		print_r($arr);
	endif;
    }

function checkFileDirectory($fileName,$filePath){
	if($fileName!='' && $filePath !=''):
		$file ='./public/'.$filePath.'/'.$fileName;
		if(file_exists($file)):
			return TRUE;
		else:
			return FALSE;
		endif;
	else:
		return FALSE;
	endif;
    }