<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerModel extends Model
{
    use HasFactory;
    protected $table = 'banner_master';
    protected $fillable = [
        'type',
    	'title',
    	'description',
    	'image',
    	'status',
    	'created_by',
    	'updated_by'
    ];
}
