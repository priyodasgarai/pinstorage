<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Review;
use Hash;
class ReviewManagement extends Controller
{
    public function reviewList()
    {
    	$this->data['title']='Review List';
		$this->data['reviewList']=Review::select('reviews.*','users.name as userName','users.email as userEmail','users.role_id')->join('users','users.id','=','reviews.user_id','INNER')->where('reviews.status','!=','3')->where('users.role_id','=','3')->orderby('reviews.id','desc')->paginate($this->limit);
		//pr($this->data['reviewList']);
	  	return view('pages.review.list')->with($this->data);
    }
}
