<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    use HasFactory;
    protected $table = 'app_settings';
    protected $fillable = [
    	'company_mail',
    	'company_phone',
    	'lining_cost',
	    'alteration_cost',
        'padded_cost',
        'assignment_status',
        'order_id_prefix',
    	'status',
    	'created_by',
    	'updated_by',
        'created_at',
        'updated_at'
    	
    ];
}
