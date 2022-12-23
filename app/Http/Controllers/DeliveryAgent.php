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
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Mail\Mailer;
use Hash;
use Mail;
use DB;

class DeliveryAgent extends Controller
{
    public function deliveryAgentList(Request $request)
    {
    	//return $request->all();
    	if($request->ajax()):
			$validator = Validator::make($request->all(), [
           'name' => 'required|max:255|regex:/^[a-zA-ZÑñ\s]+$/',
            'email'=> 'required|email',
            'phone' => 'required|digits:10',
           // 'password' => 'required',
            'address' => 'required',
            'pincode' => 'required'
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
       						'redirect'	=> ''
        			]
        		,200);
        else:
        	$requestData = $request->all();
            $id = $requestData['id'];
            if(is_null($id)):
                if(User::where('email', $requestData['email'])->where('status','!=','3')->exists()):
                    return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Email Already Exist !!',
                                'redirect'  => ''
                        ]
                    ,200);
                elseif(User::where('phone', $requestData['phone'])->where('status','!=','3')->exists()):
                	return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Phone No Already Exist !!',
                                'redirect'  => ''
                        ]
                    ,200);
                else:
                    if($request->hasFile('image')):
                        $this->fileName = time().'.'.$request->file('image')->extension();
                        $request->file('image')->move(public_path('uploads/userImages'), $this->fileName);
                    endif;
                	$user = User::create([
                        'role_id'       => 2,
                        'name'          => $requestData['name'],
                        'email'         => $requestData['email'],
                        'phone'         => $requestData['phone'],
                        'password'      => Hash::make($requestData['password']),
                        'address'       => $requestData['address'],
                        'pincode'       => $requestData['pincode'],
                       // 'country_id'    => $requestData['country_id'],
                       // 'state_id'      => $requestData['state_id'],
                        'city_id'       => $requestData['city_id'],
                        'image'         => $this->fileName,
                        'email_validate'=> 1,
                        'phone_validate'=>1,
                        'status'=>1,
                        'created_by'=>Auth::user()->id
                        
                    ]);
                    $mailDetails = [
                        'email'     => $requestData['email'],
                        'subject'   => 'Welcome to'.env('APP_NAME'),
                        'html'      => 'emails.customer-email-template',
                        'userName'  => $requestData['name'],
                        'password'	=> $requestData['password']
                    ]; 
                    Mail::to($requestData['email'])->send(new Mailer($mailDetails));
                    $pincode=explode(',',$request->allocated_pincode);
                    for($i=0; $i<count($pincode); $i++){
                    DB::table('deliveryAgent_pincode')->insert(['user_id'=>$user->id,'zipcode'=>$pincode[$i],'status'=>1]);
                    }
                    return response()->json([
                                'status'    => TRUE,
                                'message'   => 'Agent Added Successfully !!',
                                'redirect'  => 'delivery-agent/list'
                        ]
                    ,200);
                endif;
            else:
                if(User::where('email', $requestData['email'])->where('id','<>',$id)->where('status','!=','3')->exists()):
                    return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Email Already Exist !!',
                            'redirect'  => ''
                    ]
                	,200);
               	elseif(User::where('phone', $requestData['phone'])->where('id','<>',$id)->where('status','!=','3')->exists()):
               		return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Phone Already Exist !!',
                            'redirect'  => ''
                    ]
                	,200);
                else:
                    if($request->hasFile('image')):
                            if($request->has('old_file')):
                                $filePath = public_path('uploads/userImages'.$request->input('old_file'));
                                if(File::exists($filePath)):
                                    File::delete($filePath);
                                    //unlink($filePath);
                                endif;
                            endif;
                            $this->fileName = time().'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/userImages'), $this->fileName);
                    endif;
                	 User::where('id',$id)->update([
                        'role_id'       => 2,
                        'name'          => $requestData['name'],
                        'email'         => $requestData['email'],
                        'phone'         => $requestData['phone'],
                       // 'password'      => Hash::make($requestData['password']),
                        'address'       => $requestData['address'],
                        'pincode'       => $requestData['pincode'],
                       // 'country_id'    => $requestData['country_id'],
                       // 'state_id'      => $requestData['state_id'],
                        'city_id'       => $requestData['city_id'],
                        'image'         => $this->fileName,
                        'email_validate'=> 1,
                        'phone_validate'=>1,
                        'status'=>1,
                        'updated_by'=>Auth::user()->id
                        ]);
                        $pincodes=DB::table('deliveryAgent_pincode')->where('user_id',$id)->delete();
                        $pincode=explode(',',$request->allocated_pincode);
                         for($i=0; $i<count($pincode); $i++){
                             //echo $pincode[$i],
                        DB::table('deliveryAgent_pincode')->insert(['user_id'=>$id,'zipcode'=>$pincode[$i],'status'=>1]);
                        }
                    return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Agent Updated Successfully !!',
                            'redirect'  => 'delivery-agent/list'
                    ]
                ,200);
                endif;
            endif;
        endif; 
        endif;
    	$this->data['title']='Delivery Agent List';
		/*$this->data['deliveryAgentList']=User::where('status','!=','3')->where('role_id','=','2')->paginate(5);*/
	  	return view('pages.agent.list')->with($this->data);
    }
    public function deliveryAgentChangePassword(Request $request,$id=null)
    {
        if($request->ajax()):
			$validator = Validator::make($request->all(), [
           'id' => 'required',
            'password' => 'required',
            'confirmed_password' => 'required',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
       						'redirect'	=> ''
        			]
        		,200);
        else:

        	$requestData = $request->all();
            if($requestData['password'] <> $requestData['confirmed_password']):
                return response()->json([
                    'status'	=> FALSE,
                    'message'	=> 'confirm password does not match',
                    'redirect'	=> ''
            ]
        ,200);
            endif;
            User::where('id',$requestData['id'])->update([
                'password'      => Hash::make($requestData['password']),
                'updated_by'=>Auth::user()->id
                ]);
                return response()->json([
                    'status'    => TRUE,
                    'message'   => 'Agent Password Updated Successfully !!',
                    'redirect'  => 'delivery-agent/list'
            ]
        ,200);
        endif;
    endif;
    $this->data['id'] = $id;
    $this->data['title']='Delivery Agent Change Password';
    return view('pages.agent.changePassword')->with($this->data);
    }
    
    public function ajaxDataTable(Request $request)
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
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="users" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="users" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                endif;
                $action = '<a href="'.url('delivery-agent/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="users" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    <a href="'.url('delivery-agent/change-password/'.$value->id).'" class="btn btn-success"><i class="fa fa-key" aria-hidden="true"></i></a>';
                $tempArr[] = [
                           "id"             => ($key+1),
                           "name"           => $value->name,
                           "email"          => $value->email,
                           "created_at"     => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"         => $status,
                           "action"         => $action
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
    public function deliveryAgentAdd($id=null)
    {
    	if(!is_null($id)):
    		$this->data['title']='Delivery Agent Edit';
			$this->data['data']=User::find($id);
			$pin=DB::table('deliveryAgent_pincode')->where('user_id',$id)->get();
			$this->data['pin']='';
			for($i=0;$i<count($pin);$i++){
			   $this->data['pin'].=$pin[$i]->zipcode;
			   if($this->data['pin'] !='' && $i<count($pin)-1){
			       $this->data['pin'].=',';
			   }
			}
			
    	else:
    		$this->data['title']='Delivery Agent Add';
    	endif;
        $this->data['cityList']=City::where('is_popular',1)->orderBy('name','asc')->get();
    	return view('pages.agent.add')->with($this->data);
    }
    public function getState(Request $request)
    {
        $this->data['states'] = State::where("country_id",$request->country_id)
                    ->get(["name","id"]);
        return response()->json($this->data);
    }
    public function getCity(Request $request)
    {
        $this->data['cities'] = City::where("state_id",$request->state_id)
                    ->get(["name","id"]);
        return response()->json($this->data);
    }
     public function exportFile(Request $request) {
        $agents = User::with(['other_address'=> function ($query) {
                                $query->select('id', 'user_id','full_name','phone','address');
                            },'country', 'state', 'city'])->where('status', '!=', '3')->where('role_id', '=', '2')->get();
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Sl. No.');
        $sheet->setCellValue('B1', 'Agent Name');
        $sheet->setCellValue('C1', 'Agent Email');
        $sheet->setCellValue('D1', 'Created On');
        $sheet->setCellValue('E1', 'Status');
        $sheet->setCellValue('F1', 'Address');
        $sheet->setCellValue('G1', 'Country');
        $sheet->setCellValue('H1', 'State');
        $sheet->setCellValue('I1', 'City');
        $sheet->setCellValue('J1', 'Pincode');
        $sheet->setCellValue('K1', 'Landmark');
        $sheet->setCellValue('L1', 'Phone');
         $sheet->setCellValue('M1', 'My Address');
        if (count($agents) > 0):
            $i = 2;
            foreach ($agents as $key => $value):
                $sheet->setCellValue('A' . $i, ($key + 1));
                $sheet->setCellValue('b' . $i, $value->name);
                $sheet->setCellValue('C' . $i, $value->email);
                $sheet->setCellValue('D' . $i, date('d-m-y h:i:s', strtotime($value->created_at)));
                $sheet->setCellValue('E' . $i, (($value->status == 1) ? 'Active' : 'Inactive'));
                $sheet->setCellValue('F' . $i, $value->address);
                $sheet->setCellValue('G' . $i, ($value->country) ? $value->country->name : "");
                $sheet->setCellValue('H' . $i, ($value->state) ? $value->state->name : "");
                $sheet->setCellValue('I' . $i, ($value->city) ? $value->city->name : "");
                $sheet->setCellValue('J' . $i, $value->pincode);
                $sheet->setCellValue('K' . $i, $value->landmark);
                $sheet->setCellValue('L' . $i, $value->phone);
                $sheet->setCellValue('M' . $i, $value->other_address);
                $i++;
            endforeach;
        endif;
        $fileName = "Delivery Agent List [" . date('d-m-Y h:i a') . "].xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}
