<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Session;
use App\Models\CategoryModel;
use App\Models\ProductDesignMeasurements;
use Hash;

class Category extends Controller
{
    public function categoryList(Request $request)
    {
        if($request->isMethod('post')):
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'mimes:png,jpg,jpeg,gif|max:2048',
                'banner_image' => 'mimes:png,jpg,jpeg,gif'
            ]);
            if($validator->fails()):
                return response()->json([
                                'status'    => FALSE,
                                'message'   => 'Please Input Valid Credentials',
                                'redirect'  => ''
                        ]
                    ,200);
            else:
                $catImage='';
                $catBannerImage='';
                if(is_null($request->input('updateId'))):
                    if(CategoryModel::whereRaw('LOWER(`name`) = "'.strtolower($request->input('name')).'"')->where('status','!=','3')->exists()):
                        return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Category Name Already Exist !!',
                                    'redirect'  => ''
                            ]
                        ,200);
                    else:
                        if($request->hasFile('image')):
                            $catImage = (time()+10).'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/category'), $catImage);
                        endif;
                        if($request->hasFile('banner_image')):
                            $catBannerImage = time().'.'.$request->file('banner_image')->extension();
                            $request->file('banner_image')->move(public_path('uploads/category'), $catBannerImage);
                        endif;
                        CategoryModel::create([
                            'name'          =>  $request->input('name'),
                            'description'   =>  $request->input('description'),
                            'image'         =>  $catImage,
                            'banner_image'  =>  $catBannerImage,
                            'parent'        =>  ($request->input('is_parent') == 1)?'0':$request->input('parent_id'),
                            'created_by'    =>Auth::user()->id,
                        ]);
                        return response()->json([
                                    'status'    => TRUE,
                                    'message'   => 'Category Added Successfully !!',
                                    'redirect'  => 'category-management/list'
                            ]
                        ,200);
                    endif;
                else:
                    if(CategoryModel::whereRaw('LOWER(`name`) = "'.strtolower($request->input('name')).'"')->where('id','<>',$request->updateId)->where('status','!=','3')->exists()):
                        return response()->json([
                                    'status'    => FALSE,
                                    'message'   => 'Category Name Already Exist !!',
                                    'redirect'  => ''
                            ]
                        ,200);
                    else:
                       
                        $updateArr = [
                            'name'          =>$request->input('name'),
                            'description'   =>$request->input('description'),
                            
                            'parent'        =>($request->input('is_parent') == 1)?'0':$request->input('parent_id'),
                            'updated_by'    =>Auth::user()->id,

                        ];
                        if($request->hasFile('image')):
                            $updateArr['image'] =  $catImage = (time()+10).'.'.$request->file('image')->extension();
                            $request->file('image')->move(public_path('uploads/category'), $catImage);
                        endif;
                        if($request->hasFile('banner_image')):
                            $updateArr['banner_image'] = $catBannerImage = time().'.'.$request->file('banner_image')->extension();
                            $request->file('banner_image')->move(public_path('uploads/category'), $catBannerImage);
                        endif;
                        CategoryModel::where('id',$request->input('updateId'))->update($updateArr);
                        return response()->json([
                                'status'    => TRUE,
                                'message'   => 'Category Updated Successfully !!',
                                'redirect'  => 'category-management/list'
                            ]
                        ,200);
                    endif;
                endif;
            endif;
        endif;
    	$this->data['title']='Category List';
	  	return view('pages.category.list')->with($this->data);
    }
    public function ajaxDataTable(Request $request)
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
            $totalRecords=CategoryModel::select('category_master.*','parent.name as parent_category')
            ->join('category_master as parent','category_master.parent','=','parent.id','left')
            ->where('category_master.status','!=','3')->count();
            //pr($totalRecords);
            /*$totalRecords = CategoryModel::select('count(*) as allcount')->where('status','!=',3)->where('role_id',3)->count();*/
            //$totalRecordswithFilterQuery = CategoryModel::select('category_master.*','parent.name as parent_category')
            //->join('category_master as parent','category_master.parent','=','parent.id','left')->count();
            $totalRecordswithFilterQuery = CategoryModel::select('count(*) as allcount');
            //pr($totalRecordswithFilterQuery);
            if($request->post('searchCategory')!=''):
                $totalRecordswithFilterQuery->where('name', 'like', '%' .$request->post('searchCategory'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $totalRecordswithFilterQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $totalRecordswithFilter = $totalRecordswithFilterQuery->where('status','!=','3')->count();
            //pr($totalRecordswithFilter);
            $recordsQuery = CategoryModel::orderBy($columnName,$columnSortOrder);
            if($request->post('searchCategory')!=''):
                $recordsQuery->where('name', 'like', '%' .$request->post('searchCategory'). '%');
            endif;
            if(($request->post('searchFormDate')!='' || !is_null($request->post('searchFormDate'))) && ($request->post('searchToDate')!='' || !is_null($request->post('searchToDate')))):
                $recordsQuery->whereBetween('created_at', [date('Y-m-d',strtotime($request->post('searchFormDate'))),date('Y-m-d',strtotime($request->post('searchToDate')))]);
            endif;
            $records =$recordsQuery->where('status','!=',3)->skip($start)->take($rowperpage)->get();
            $tempArr = [];
            foreach($records as $key => $value):
                if($value->status == 1):
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="category_master" data-status="0" data-key="id" data-id="'.$value->id.'" class="badge badge-primary change-status">Active</a>';
                else:
                    $status = '<a href="javascript:void(0)" id="'.$value->id.'" data-table="category_master" data-status="1" data-key="id" data-id="'.$value->id.'" class="badge badge-danger change-status">Inactive</a>';
                endif;
                if($value->image && checkFileDirectory($value->image,'uploads/category')):
                    $image='<img src="'.(asset('uploads/category/'.$value->image)).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                else:
                    $image='<img src="'.(asset('assets/images/no-img-available.png')).'" id="bannerImg" alt="your image" width="80px" height="80" />';
                endif;
                $action = '<a href="'.url('category-management/edit/'.$value->id).'" class="btn btn-info"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                    <a href="javascript:void(0)" id="'.$value->id.'" data-table="category_master" data-status="3" data-key="id" data-id="'.$value->id.'" class="btn btn-danger change-status"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                     <a href="' . url('category-management/measurement-list/' . $value->id) . '" class="btn btn-warning" title="Add Measurement"><i class="fa fa-plus" aria-hidden="true"></i></a>';
                $tempArr[] = [
                           "id"             => ($key+1),
                           "name"           => $value->name,
                           "description"    => ($value->description!="")?substr($value->description, 0,50).'...':'',
                           "image"          => $image,
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
    public function categoryAdd($id=null)    {
    	if(!is_null($id)):
    		$this->data['title']='Category edit';
    		$this->data['details']=CategoryModel::find($id);
    	else:
    		$this->data['title']='Category add';
    	endif;
        $this->data['categoryList'] = CategoryModel::where('status','=','1')->where('parent','=','0')->get();
    	return view('pages.category.add')->with($this->data);
    }
    public function measurementList(Request $request, $id) {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                        'product_category_id' => 'required',
                        'label' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                            'status' => FALSE,
                            'message' => $validator->errors(),
                            'redirect' => ''
                                ]
                                , 200);
            }else{
                $ProductDesignMeasurements = new ProductDesignMeasurements();
                $ProductDesignMeasurements->label=$request->label;
                 $ProductDesignMeasurements->product_category_id=$request->product_category_id;
                  $ProductDesignMeasurements->save();
            }
        }
        $this->data['addonId'] = $id;
        $this->data['title'] = 'Measurement List';
        $this->data['addonList'] = ProductDesignMeasurements::where('product_category_id', '=', $id)->orderBy('id','DESC')->get();
        // return $this->data;
        return view('pages.category.measurement-list')->with($this->data);
    }

    public function measurementAdd($id) {
        $this->data['title'] = 'Measurement Add';
        $this->data['productDesignId'] = $id;
        return view('pages.category.measurement-add')->with($this->data);
    }

    public function measurementDelete(Request $request) {
        $validator = Validator::make($request->all(), [
                    'id' => 'required',
                    'keyId' => 'required',
                    'status' => 'required',
                    'table' => 'required',
        ]);
        if ($validator->fails()):
            return response()->json([
                        'status' => FALSE,
                        'message' => 'Please Input Valid Credentials',
                        'redirect' => ''
                            ]
                            , 200);
        else:
            try {
                DB::table($request->table)->where($request->keyId, $request->id)->delete();
                return response()->json([
                            'status' => TRUE,
                            'message' => 'Request processed Successfully!.',
                            'redirect' => '',
                            'postStatus' => $request->status
                                ]
                                , 200);
            } catch (\Exception $e) {
                // print $e->getMessage();die;
                return response()->json([
                            'status' => FALSE,
                            'message' => 'Oops Sank! Something Went Terribly Wrong !',
                            'redirect' => ''
                                ], 500);
            }
        endif;
    }
}
