<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Notification;

class PushNotification extends Controller
{
    public function notification(Request $request)
    {
    	if($request->isMethod('post')):
            if (!is_null($request->post('notificationTo'))):
                $messageData = array(
                    //'message'       => $this->input->post('message'),
                    'body'          => $request->post('message'),
                    'title'         => $request->post('title'),
                    'vibrate'       => 1,
                    'sound'         => 1,
                    'click_action'  => ""
                );
                foreach ($request->post('notificationTo') as $val):
                    $tmpData = explode('@#@', $val);
                    //pr($tmpData);
                    if ($tmpData[1] != '' || $tmpData[1] != NULL) :
                        $this->pushNotification($tmpData[1], $messageData);
                        Notification::create([
                        	"user_id"   => $tmpData[0],
                            "title"     => $request->post('title'),
                            "message"   => $request->post('message'),
                            'status'	=>1,
                            'created_by'=>Auth::user()->id
                        ]);                        /*$this->cm->insert('notifications', array(
                            "user_id"   => $tmpData[0],
                            "title"     => $this->input->post('title'),
                            "message"   => $this->input->post('message')
                        ));*/
                        send_notification($tmpData[1],  $request->post('title'), $request->post('message')); 
                    endif;
                endforeach;
                return redirect()->back()->with('success', 'Push Notification Added Successfully !!');
                // return response()->json([
                //                     'status'    => TRUE,
                //                     'message'   => 'Push Notification Added Successfully !!',
                //                     'redirect'  => 'push-notification'
                //             ]
                // ,200);
            else:
                return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'You must select Right Block\'s Users !!!',
                                    'redirect'  => ''
                            ]
                ,200);
            endif;
        endif;
    	$this->data['title']='Push Notification';
    	$this->data['users']=User::select('users.*')->where('users.role_id','!=','1')->get();
    	//pr($this->data['users']);
        return view('pages.push-notification')->with($this->data);
    }
     public function notificationList(Request $request) {        
        $this->data['title'] = 'Notification List';
        return view('pages.notification.list')->with($this->data);
    }
     public function ajaxNotificationDataTable(Request $request) {
        if ($request->ajax()):
            $draw = $request->post('draw');
            $start = $request->post("start");
            $rowperpage = $request->post("length"); // Rows display per page
            $columnIndexArr = $request->post('order');
            $columnNameArr = $request->post('columns');
            $orderArr = $request->post('order');
            $searchArr = $request->post('search');
            $columnIndex = $columnIndexArr[0]['column']; // Column index
            $columnName = $columnNameArr[$columnIndex]['data']; // Column name
            $columnSortOrder = $orderArr[0]['dir']; // asc or desc
            $searchValue = $searchArr['value']; // Search value
            // Total records
            $Records = Notification::with(['user'=> function ($query1) {
                                $query1->select('id','name','phone');
                            }]);            
            if($request->post('searchTitel') != ''){
                $Records->where('title', 'like', '%' . $request->post('searchTitel') . '%');
            }
            if (($request->post('searchFormDate') != '' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate') != '' || !is_null($request->post('searchToDate')))){
                $Records->whereBetween('created_at', [date('Y-m-d', strtotime($request->post('searchFormDate'))), date('Y-m-d', strtotime($request->post('searchToDate')))]);
            }
            $totalRecordswithFilter = $Records->count();
            //dd($totalRecordswithFilter);
            $recordsQuery = $Records->orderBy($columnName, $columnSortOrder);           
            $result = $recordsQuery->skip($start)->take($rowperpage)->get();
           // dd($result);
            $tempArr = [];
            foreach ($result as $key => $value):
                if ($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-success">Active</a>';
                elseif ($value->status == 0):
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" class="badge badge-danger">Block</a>';
                   endif;
                 $action = '<a href="javascript:void(0)" data-table="notifications" data-status="0" data-key="id" data-id="' . $value->id . '" class="btn btn-danger change-status"><i class="fa fa-times-circle-o" aria-hidden="true"></i></a>';
                     
                     
                $tempArr[] = [
                    "id" => ($key + 1),
                    "name" => $value->user->name,
                    "phone" => $value->user->phone,
                    "title" => $value->title,
                    "message" => $value->message,
                    "created_at" => date('d-m-Y h:i:s', strtotime($value->created_at)),
                    "status" => $status,
                    "action" => $action
                ];
            endforeach;
            return response()->json([
                        "draw" => intval($draw),
                        "recordsTotal" => intval($totalRecordswithFilter),
                        "recordsFiltered" => intval($totalRecordswithFilter),
                        "data" => $tempArr
                            ]
                            , 200);
        else:
            return response()->json([
                        'status' => FALSE,
                        'message' => 'Bad Request !!',
                        'redirect' => ''
                            ]
                            , 400);
        endif;
    }
}
