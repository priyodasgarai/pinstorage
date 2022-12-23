<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\CategoryModel;
use App\Models\Delivery;
use App\Models\Size;
use App\Models\ProductDesigns;
use App\Models\ProductDesignAddons;
use App\Models\ProductDesignImages;
use App\Models\ProductDesignAddonsImages;
//use Illuminate\Support\Facades\DB;
class ProductDesign extends Controller
{
    public function productDesignList(Request $request)
    {
        if ($request->isMethod('post')) :

            $validator = Validator::make($request->all(), [
                'category_id'     => 'required',
                'title'         => 'required',
                'price'         => 'required',
                'is_featured'     => 'required',
                //'file_name'     => 'mimes:png,jpg,jpeg,gif|max:2048'
            ]);
            if ($validator->fails()) :
                return response()->json(
                    [
                        'status'    => FALSE,
                        'message'   => 'Please Input Valid Credentials',
                        'redirect'  => ''
                    ],
                    200
                );
            else :
                if (is_null($request->input('productDesignId'))) :
                    if (ProductDesigns::whereRaw('LOWER(`title`) = "' . strtolower($request->input('title')) . '"')->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Title Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    else :
                        $productDesigns = ProductDesigns::create([
                            'title' => $request->input('title'),
                            'quantity' => $request->input('quantity'),
                            'size' => implode(",", $request->input('size')),
                            'price' => $request->input('price'),
                            'short_description' => $request->input('short_description'),
                            'category_id' => $request->input('category_id'),
                            'delivery_id' => $request->input('delivery_id'),
                            'is_featured' => $request->input('is_featured'),
                            'is_trending' => $request->input('is_trending'),
                            'created_by' => Auth::user()->id
                        ]);
                        if ($request->hasFile('file_name')) :
                            $images = $request->file('file_name');
                            foreach ($images as $key => $image) :
                                if ($key == 0) :
                                    $this->fileName = time() . '.' . $image->getClientOriginalName();
                                    $image->move(public_path('uploads/productDesignImages'), $this->fileName);
                                    ProductDesignImages::insert([
                                        'product_design_id' => $productDesigns->id,
                                        'file_name' => $this->fileName,
                                        'is_primary' => 1
                                    ]);
                                else :
                                    $this->fileName = time() . '.' . $image->getClientOriginalName();
                                    $image->move(public_path('uploads/productDesignImages'), $this->fileName);
                                    ProductDesignImages::insert([
                                        'product_design_id' => $productDesigns->id,
                                        'file_name' => $this->fileName,
                                        'is_primary' => 0
                                    ]);
                                endif;
                            endforeach;
                        endif;
                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Product Design Added Successfully !!',
                                'redirect'  => 'product-design-management/list'
                            ],
                            200
                        );
                    endif;
                else :
                    if (ProductDesigns::whereRaw('LOWER(`title`) = "' . strtolower($request->input('title')) . '"')->where('id', '<>', $request->input('productDesignId'))->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Title Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    else :
                        ProductDesigns::where('id', $request->input('productDesignId'))->update([
                            'title' => $request->input('title'),
                            'quantity' => $request->input('quantity'),
                            'size' => implode(",", $request->input('size')),
                            'price' => $request->input('price'),
                            'short_description' => $request->input('short_description'),
                            'category_id' => $request->input('category_id'),
                            'delivery_id' => $request->input('delivery_id'),
                            'is_featured' => $request->input('is_featured'),
                            'is_trending' => $request->input('is_trending'),
                            'updated_by' => Auth::user()->id
                        ]);
                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Product Design Updated Successfully !!',
                                'redirect'  => 'product-design-management/list'
                            ],
                            200
                        );
                    endif;
                endif;
            endif;
        endif;
        $this->data['title'] = 'Product Design List';
        $this->data['productDesignList'] = ProductDesigns::selectRaw('product_designs.*,category_master.name as categoryName ,(select file_name from product_design_images where product_design_id = product_designs.id and product_design_images.is_primary=1) image')->join('category_master', 'category_master.id', '=', 'product_designs.category_id', 'inner')->where('product_designs.status', '!=', '3')->orderby('product_designs.id', 'desc')->paginate($this->limit);
        //pr($this->data['productDesignList']);
        return view('pages.product_design.design-list')->with($this->data);
    }
    public function productDesignAdd($id = null)
    {
        if (!is_null($id)) :
            $this->data['details'] = ProductDesigns::find($id);
            //pr($this->data['details']);
            $this->data['title'] = 'Product Design Edit';
        else :
            $this->data['title'] = 'Product Design Add';
        endif;
        $this->data['catagoryList'] = CategoryModel::where('status', '!=', '3')->get();
        $this->data['Deliverytime'] = Delivery::where('status', '!=', '3')->get();
        return view('pages.product_design.design-add')->with($this->data);
    }
    public function productDesignGalleryView($id)
    {
        $this->data['gallery'] = ProductDesigns::selectRaw('product_designs.*,product_design_images.file_name as file_names,product_design_images.is_primary as is_primary')->join('product_design_images', 'product_designs.id', '=', 'product_design_images.product_design_id', 'INNER')->where('product_designs.id', '=', $id)->where('product_designs.status', '!=', '3')->get();
        //pr($this->data['gallery']);
        $this->data['title'] = 'Product Design Gallery';
        return view('pages.product_design.view-gallery')->with($this->data);
    }
    public function addonList(Request $request, $id)
    {
        if ($request->isMethod('post')) :
            $validator = Validator::make($request->all(), [
                'designId'          => 'required',
                'title'             => 'required',
                'price'             => 'required',
                'input_group'       => 'required',
                'addon_image.*'     => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            if ($validator->fails()) :
                return response()->json(
                    [
                        'status'    => FALSE,
                        'message'   => $validator->errors(),
                        'redirect'  => ''
                    ],
                    200
                );
            else :
              //  pr($request->input());
                if (is_null($request->input('addonId'))) :
                    if (ProductDesignAddons::whereRaw('LOWER(`input_group`) = "' . strtolower($request->input('input_group')) . '"')->where('status', '!=', '3')->exists()) :
                        return response()->json(
                            [
                                'status'    => FALSE,
                                'message'   => 'Group Already Exist !!',
                                'redirect'  => ''
                            ],
                            200
                        );
                    else :
                        $ProductDesignAddons = ProductDesignAddons::create([
                            'product_design_id' => $request->designId,
                            'input_group' => ucwords($request->input_group),
                            'custom_price' => $request->custom_price,
                            'created_by' => Auth::user()->id
                        ]);
                        if ($request->hasFile('addon_image')) :
                            $tempArray = [];
                            $images = $request->file('addon_image');
                            foreach ($images as $key => $image) :
                                $this->fileName = time() . '.' . $image->getClientOriginalName();
                                $image->move(public_path('uploads/productDesignImages'), $this->fileName);
                                $tempArray[] = [
                                    'product_design_addon_id' => $ProductDesignAddons->id,
                                    'addon_image' => $this->fileName,
                                    'title' => $request->title[$key] ?? 0,
                                    'price' => $request->price[$key] ?? 0,
                                    'created_by' => Auth::user()->id
                                ];
                            endforeach;
                            ProductDesignAddonsImages::insert($tempArray);
                        endif;

                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Product Design Addon Added Successfully !!',
                                'redirect'  => 'product-design-management/addon-list/' . $request->input('designId')
                            ],
                            200
                        );
                    endif;
                //print_r($request->file());
                else :

                        $ProductDesignAddons = ProductDesignAddons::where('id', $request->input('addonId'))->update([
                            'input_group' => ucwords($request->input_group),
                            'custom_price' => $request->custom_price,
                            'updated_by'    => Auth::user()->id,
                        ]);
                        if ($request->hasFile('addon_images')) :
                            $tempArr = [];
                            $images = $request->file('addon_images');
                            foreach ($images as $key => $image) :
                                $this->fileName = time() . '.' . $image->getClientOriginalName();
                                $image->move(public_path('uploads/productDesignImages'), $this->fileName);
                                $tempArr[] = [
                                    'product_design_addon_id' => $request->input('designid'),
                                    'add_on_images' => $this->fileName,
                                    'title' => $request->title[$key] ?? 0,
                                    'price' => $request->price[$key] ?? 0,
                                    'created_by' => auth()->id()
                                ];
                            endforeach;
                            ProductDesignAddons::insert($tempArr);
                        endif;
                        if ($request->has('flag') && $request->flag == 1) :
                            return response()->json(
                                [
                                    'status'    => TRUE,
                                    'message'   => 'Addon Image Removed Successfully!',
                                    'redirect'  => 'product-design-management/addon-edit/' . $request->input('addonId')
                                ],
                                200
                            );
                        endif;
                        return response()->json(
                            [
                                'status'    => TRUE,
                                'message'   => 'Product Design Addon Updated Successfully !!',
                                'redirect'  => 'product-design-management/addon-list/' .  $request->designId
                            ],
                            200
                        );

                endif;
            endif;
        endif;
        $this->data['addonId'] = $id;
        $this->data['title'] = 'Product Design Addons List';
        $this->data['addonList'] = ProductDesignAddons::where('status', '!=', '3')->where('product_design_id', '=', $id)->get();
        // dd($this->data['addonList']);
        return view('pages.product_design.addon-list')->with($this->data);
    }
    public function addonAdd($productDesignId = null, $id = null)
    {
        $this->data['productDesignId'] = $productDesignId;
        //echo $this->data['productDesignId'];die;
        if (!is_null($id)) :
            $this->data['details'] = ProductDesignAddons::find($id);
            $this->data['title'] = 'Product Design Addon Edit';
        else :
            //pr($productDesignId);
            $this->data['title'] = 'Product Design Addon Add';
        //$this->data['designdetails']=ProductDesignAddons::find($productDesignId);
        //pr($this->data['designdetails']);
        endif;
        //return $this->data;
        return view('pages.product_design.addon-add')->with($this->data);
    }
}
