<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;
use Illuminate\Http\Request;


class PageController extends Controller
{
    public function pageList()
    {
		$this->data['title']='Page List';
	  	return view('pages.page.list')->with($this->data);
    }
    public function ajaxPageDataTable(Request $request)
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

            $recordsQuery = Page::orderBy($columnName,$columnSortOrder);
            if($request->post('searchTitle')!='' || !is_null($request->post('searchTitle'))):
                $recordsQuery->where('name', 'like', '%' .$request->post('searchTitle'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $result =$recordsQuery->skip($start)->take($rowperpage);
            $totalRecordswithFilter = $result->count();
            $records =$result->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="page_master" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                elseif($value->status == 0):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="page_master" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                else:
                    $status = '<a href="javascript:void(0)" class="badge badge-danger">Deleted</a>';
                endif;
                if(isset($value) && checkFileDirectory($value->image,'uploads/pageImages')):
                    $image='<img src="'.(asset('uploads/pageImages/'.$value->image)).'" id="pageImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="pageImg" alt="your image" width="80px" height="80" />';
                endif;

                $action = '<a href="'.route('admin.page-management.edit', $value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="page_master" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';

                $tempArr[] = [
                           "id"             => ($key+1),
                           "name"   => $value->name,
                           "title"   => $value->title,
                           "description"=> ($value->description!="")?substr($value->description, 0,50).'...':'',
                            "image"          =>  $image,
                           "created_at"     => date('d-m-Y h:i:s',strtotime($value->created_at)),
                           "status"         => $status,
                           "action"         => $action
                        ];
            endforeach;
            return response()->json([
                                   "draw" => intval($draw),
                                   "recordsTotal" => intval($totalRecordswithFilter),
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
    public function pageAdd($id=null)
    {
    	if(!is_null($id)):
    		$this->data['title']='page edit';
    		$this->data['details']=Page::find($id);
    	else:
    		$this->data['title']='page add';
    	endif;
        return view('pages.page.add')->with($this->data);
    }
    public function pageSave(Request $request)
    {
        //pr($request->all());
    	$validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'description' => 'required',
           // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if($validator->fails()):
        	return response()->json([
        					'status'	=> FALSE,
        					'message'	=> $validator->errors()->first(),
       						'redirect'	=> ''
        			]
        		,200);
        else:

            if(is_null($request->input('id'))):
                if(Page::where('name', $request->input('name'))->where('status','!=','3')->exists()):
                    return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Name Already Exist !!',
                                'redirect'  => ''
                        ]
                    ,200);
                else:
                    if($request->hasFile('image')):
                        $this->fileName = time().'.'.$request->file('image')->extension();
                        $request->file('image')->move(public_path('uploads/pageImages'), $this->fileName);
                    endif;
                    Page::create([
                        'name'=>$request->input('name'),
                        'title'=>$request->input('title'),
                        'description'=>$request->input('description'),
                        'image'             => $this->fileName,
                        'created_by'        =>Auth::guard('Admin')->user()->id
                    ]);
                    return response()->json([
                                'status'    => TRUE,
                                'message'   => 'Page Added Successfully !!',
                                'redirect'  => route('admin.page-management.list')
                        ]
                    ,200);
                endif;
            else:

                if(Page::where('name', $request->input('name'))->where('id','<>',$request->id)->where('status','!=','3')->exists()):
                    return response()->json([
                            'status'    => FALSE,
                            'message'   => 'Name Already Exist !!',
                            'redirect'  => ''
                    ]
                ,200);
                else:
                    if($request->hasFile('image')):
                            if($request->has('old_file') && $request->input('old_file')!=""):
                                $filePath = public_path('uploads/pageImages'.$request->input('old_file'));
                               if(file_exists($filePath)):
                                    unlink($filePath);
                                endif;
                            endif;
                            $this->fileName = time().'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/pageImages'), $this->fileName);
                    endif;
                    $page = Page::where('id', $request->input('id'))->first();
                    Page::where('id',$request->input('id'))->update([
                            'name'=>$request->input('name')?$request->input('name'):$page->title,
                            'title'=>$request->input('title')?$request->input('title'):$page->title,
                            'description'=>$request->input('description')?$request->input('description'):$page->description,
                            'image'             => $this->fileName?$this->fileName:$page->image,
                            'updated_by'    =>Auth::guard('Admin')->user()->id
                    ]);

                    return response()->json([
                            'status'    => TRUE,
                            'message'   => 'Page Updated Successfully !!',
                            'redirect'  =>  route('admin.page-management.list')
                    ]
                ,200);
                endif;
            endif;
        endif;
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
                Page::where($request->keyId,$request->id)->update(['status'=>$request->status]);
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
