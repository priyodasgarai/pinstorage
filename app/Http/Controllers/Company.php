<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Cms;
use Hash;

class Company extends Controller
{
    public function companyList()
    {
	$this->data['title']='Company details';
	//$this->data['companyDetails']=Cms::where('status','!=','3')->paginate(5);
  	return view('pages.company.list')->with($this->data);
    }
    public function ajaxCmsDataTable(Request $request)
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
            $totalRecords = Cms::select('count(*) as allcount')->where('status','!=',3)->count();
            $totalRecordswithFilterQuery = Cms::select('count(*) as allcount');
            if($request->post('searchTitle')!='' || !is_null($request->post('searchTitle'))):
                $totalRecordswithFilterQuery->where('title', 'like', '%' .$request->post('searchTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $totalRecordswithFilterQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecordswithFilter = $totalRecordswithFilterQuery->where('status','!=',3)->count();
            $recordsQuery = Cms::orderBy($columnName,$columnSortOrder);
            if($request->post('searchTitle')!='' || !is_null($request->post('searchTitle'))):
                $recordsQuery->where('title', 'like', '%' .$request->post('searchTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $records =$recordsQuery->where('status','!=',3)->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="cms" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="cms" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                endif;
                if($value->image && checkFileDirectory($value->image,'uploads/cmsImages')):
                    $image='<img src="'.(asset('uploads/cmsImages/'.$value->image)).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                endif;
                $action = '<a href="'.url('cms-management/company-details/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="cms" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                $tempArr[] = [
                           "id"             => ($key+1),
                           "title"          => $value->title,
                           "description"    => ($value->description!="")?substr($value->description, 0,50).'...':'',
                           'image'          =>  $image,
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
    public function companyDetailsAdd($id=null)
    {
        if(!is_null($id)):
            $this->data['title']='Company details | edit';
            $this->data['details']=Cms::find($id);
        else:
            $this->data['title']='Company details | add';
        endif;
    	return view('pages.company.add')->with($this->data);
    }
    public function companyDetailSave(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'mimes:png,jpg,jpeg,gif|max:2048'
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> 'Please Input Valid Credentials',
       						'redirect'	=> ''
        			]
        		,200);
        else:
            if(is_null($request->input('id'))):
                if(Cms::where('title', $request->input('title'))->where('status','!=','3')->exists()):
                    return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Title Already Exist !!',
                                'redirect'  => ''
                        ]
                    ,200);
                else:
                    if($request->hasFile('image')):
                            $this->fileName = time().'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/cmsImages'), $this->fileName);
                    endif;
                    Cms::create([
                        'title'=>$request->input('title'),
                        'slug'=>preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($request->input('title')))),
                        'description'=>$request->input('description'),
                        'image'         =>$this->fileName,
                        'created_by'    =>Auth::user()->id
                    ]);
                    return response()->json([
                                'status'    => TRUE,
                                'message'   => 'Company Details Added Successfully !!',
                                'redirect'  => 'cms-management/company-details'
                        ]
                    ,200);
                endif;
            else:
                if(Cms::where('title', $request->input('title'))->where('id','<>',$request->input('id'))->where('status','!=','3')->exists()):
                    return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Title Already Exist !!',
                            'redirect'  => ''
                    ]
                ,200);
                else:
                    if($request->hasFile('image')):
                            if($request->has('old_file') && $request->input('old_file')!=""):
                                $filePath = public_path('uploads/cmsImages'.$request->input('old_file'));
                                if(file_exists($filePath)):
                                    unlink($filePath);
                                endif;
                            endif;
                            $this->fileName = time().'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/cmsImages'), $this->fileName);
                    endif;
                    Cms::where('id',$request->input('id'))->update([
                        'title'=>$request->input('title'),
                        'slug'=>preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($request->input('title')))),
                        'description'=>$request->input('description'),
                        'image'      =>$this->fileName,
                        'updated_by' =>Auth::user()->id
                    ]);
                    return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Company Details Updated Successfully !!',
                            'redirect'  => 'cms-management/company-details'
                    ]
                ,200);
                endif;
            endif;
        endif;
    }  
}
