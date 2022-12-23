<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
use App\Models\Contact;
use App\Models\ContactForm;
use App\Models\User;
use Hash;

class ContactUs extends Controller
{
    public function queries(Request $request)
    {
    	$this->data['title']='Queries';
    	$this->data['contactList']=Contact::select('contacts.*','users.name as userName','users.email as userEmail','users.role_id')->join('users','users.id','=','contacts.user_id','INNER')->where('contacts.status','!=','3')->where('users.role_id','=','3')->orderby('contacts.id','desc')->paginate($this->limit);
    	//pr($this->data['contactList']);
        return view('pages.contact-us')->with($this->data);
    }

    public function contactUs(Request $request)
    {
    	$this->data['title']='Contact Us';
    	$this->data['contacts']=ContactForm::selectRaw('contact_forms.*,users.name,users.email,users.phone')->join('users','users.id','=','contact_forms.user_id','inner')->paginate($this->limit);
    	//pr($this->data['contacts']);
        return view('pages.contacts')->with($this->data);
    }
}
