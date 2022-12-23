<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlterationRequest extends Model
{
    use HasFactory;
    protected $table = "alteration_requests";
    protected $fillable = [
    	'user_id',
    	'alteration_type',
    	'length',
    	'job_title',
    	'job_description',
    	'alteration_image',
    	'alteration_price',
    	'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    
 public function order()
    {
        return $this->hasOne(Order::class,'alteration_id','id');
    }
}
