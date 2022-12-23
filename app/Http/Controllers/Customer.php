<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Hash;
use Mail;

class Customer extends Controller
{
    public function customerList(Request $request)
    {
        if ($request->isMethod('post')) :
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email',
                'phone' => 'required|digits:10',
                'country_id' => 'required'
            ]);
            if ($validator->fails()) :
                return response()->json(
                    [
                        'status'    => FALSE,
                        'message'    => $validator->errors()->first(),
                        'redirect'    => ''
                    ],
                    200
                );
            else :
                $requestData = $request->all();
                $id = $requestData['id'];
                if (is_null($id)) :
                    if (User::where('email', $requestData['email'])->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Email Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    elseif (User::where('phone', $requestData['phone'])->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Phone No Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    else :
                        $this->fileName = "";
                        if ($request->hasFile('image')) :
                            $this->fileName = time() . '.' . $request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/userImages'), $this->fileName);
                        endif;
                        $otp = rand(100000, 999999);
                        $user = User::create([
                            'first_name'          => $requestData['first_name'],
                            'last_name'          => $requestData['last_name'],
                            'email'         => $requestData['email'],
                            'phone'         => $requestData['phone'],
                            'password'      => Hash::make($otp),
                            'otp'       => $otp,
                            'country_id'    => $requestData['country_id'],
                            'image'         => $this->fileName,
                            'email_validate' => 1,
                            'phone_validate' => 1,
                            'status' => 1,
                            'created_by' => Auth::guard('Admin')->user()->id

                        ]);
                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Customer Added Successfully !!',
                                'redirect'  => route('admin.customer-management.list')
                            ],
                            200
                        );
                    endif;
                else :
                    if (User::where('email', $requestData['email'])->where('id', '<>', $id)->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Email Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    elseif (User::where('phone', $requestData['phone'])->where('id', '<>', $id)->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Phone Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    else :
                        if ($request->hasFile('image')) :
                            if ($request->has('old_file')) :
                                $filePath = public_path('uploads/userImages' . $request->input('old_file'));
                                if (File::exists($filePath)) :
                                    File::delete($filePath);
                                //unlink($filePath);
                                endif;
                            endif;
                            $this->fileName = time() . '.' . $request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/userImages'), $this->fileName);
                        endif;
                        User::where('id', $id)->update([
                            'first_name'          => $requestData['first_name'],
                            'last_name'          => $requestData['last_name'],
                            'email'         => $requestData['email'],
                            'phone'         => $requestData['phone'],
                            'country_id'    => $requestData['country_id'],
                            'image'         => $this->fileName,
                            'email_validate' => 1,
                            'phone_validate' => 1,
                            'status' => 1,
                            'updated_by' => Auth::guard('Admin')->user()->id
                        ]);
                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Customer Updated Successfully !!',
                                'redirect'  => route('admin.customer-management.list')
                            ],
                            200
                        );
                    endif;
                endif;
            endif;
        endif;
        $this->data['title'] = 'Customer List';
        return view('pages.customer.list')->with($this->data);
    }

    public function ajaxDataTable(Request $request)
    {
        if ($request->ajax()) :
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

            $recordsQuery = User::orderBy($columnName, $columnSortOrder);
            if ($request->post('searchEmail') != '' || !is_null($request->post('searchEmail'))) :
                $recordsQuery->where('email', 'like', '%' . $request->post('searchEmail') . '%');
            endif;
            if ($request->post('searchName') != '' || !is_null($request->post('searchName'))) :
                $recordsQuery->where('first_name', 'like', '%' . $request->post('searchName') . '%');
            endif;
            if (($request->post('searchFormDate') != '' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate') != '' || !is_null($request->post('searchToDate')))) :
                $recordsQuery->whereBetween('created_at', [date('Y-m-d', strtotime($request->post('searchFormDate'))), date('Y-m-d', strtotime($request->post('searchToDate')))]);
            endif;
            $result = $recordsQuery->skip($start)->take($rowperpage);
            $totalRecordswithFilter =$result->count();
            $records = $result->get();
            $tempArr = [];
            foreach ($records as $key => $value) :
                if ($value->status == 1) :
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" data-table="users" data-status="0" data-key="id" data-id="' . $value->id . '" class="badge badge-primary change-status">Active</a>';
                elseif($value->status == 0):
                    $status = '<a href="javascript:void(0)" id="' . $value->id . '" data-table="users" data-status="1" data-key="id" data-id="' . $value->id . '" class="badge badge-danger change-status">Inactive</a>';
                else:
                    $status = '<a href="javascript:void(0)" class="badge badge-danger">Deleted</a>';
                endif;
                $action = '<a href="' . route('admin.customer-management.edit', $value->id) . '" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="' . $value->id . '" data-table="users" data-status="3" data-key="id" data-id="' . $value->id . '" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                $tempArr[] = [
                                "id"                => ($key + 1),
                                "first_name"        => $value->first_name,
                                "last_name"         => $value->last_name,
                                "email"             => $value->email,
                                "phone"             => $value->phone,
                                "otp"               => $value->otp,
                                "country"           => $value->country ? $value->country->name : "",
                                "status"            => $status,
                                "action"            => $action
                            ];
            endforeach;
            return response()->json(
                [
                    "draw" => intval($draw),
                    "recordsTotal" => intval($totalRecordswithFilter),
                    "recordsFiltered" => intval($totalRecordswithFilter),
                    "data" => $tempArr
                ],
                200
            );
        else :
            return response()->json(
                [
                    'status'    => FALSE,
                    'message'   => 'Bad Request !!',
                    'redirect'  => ''
                ],
                400
            );
        endif;
    }
    public function customerAdd($id = null)
    {
        if (!is_null($id)) :
            $this->data['title'] = 'Customer Edit';
            $this->data['data'] = User::find($id);
        else :
            $this->data['title'] = 'Customer Add';
        endif;
        $this->data['countryList'] = Country::orderBy('name', 'asc')->get();
        return view('pages.customer.add')->with($this->data);
    }
    public function getState(Request $request)
    {
        $this->data['states'] = State::where("country_id", $request->country_id)
            ->get(["name", "id"]);
        return response()->json($this->data);
    }
    public function getCity(Request $request)
    {
        $this->data['cities'] = City::where("state_id", $request->state_id)
            ->get(["name", "id"]);
        return response()->json($this->data);
    }
    public function exportFile(Request $request)
    {
        $customers = User::get();
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Sl. No.');
        $sheet->setCellValue('B1', 'First Name');
        $sheet->setCellValue('C1', 'Last Name');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Country');
        if (count($customers) > 0) :
            $i = 2;
            foreach ($customers as $key => $value) :
                $sheet->setCellValue('A' . $i, ($key + 1));
                $sheet->setCellValue('B' . $i, $value->first_name);
                $sheet->setCellValue('C' . $i, $value->last_name);
                $sheet->setCellValue('D' . $i,$value->email);
                $sheet->setCellValue('E' . $i,$value->phone);
                $sheet->setCellValue('F' . $i, $value->country->name);
                $i++;
            endforeach;
        endif;
        $fileName = "Customer List [" . date('d-m-Y h:i a') . "].xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
    public function statusChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'        => 'required',
            'keyId'     => 'required',
            'status'    => 'required',
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
                User::where($request->keyId,$request->id)->update(['status'=>$request->status]);
                return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Request processed Successfully!.',
                            'redirect'  => '',
                            'postStatus'=>$request->status
                    ]
                ,200);
            }catch(\Exception $e){
                // print $e->getMessage();die;
                return response()->json([
                    'status'    => FALSE,
                    'message'   => 'Oops Sank! Something Went Terribly Wrong !',
                    'redirect'  => ''
                ], 500);
            }
        endif;
    }
}
