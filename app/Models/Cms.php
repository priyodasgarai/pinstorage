<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{
    use HasFactory;
    protected $table = 'cms';
    protected $fillable = [
    	'title', 
    	'slug',
    	'description',
    	'image',
    	'status',
    	'created_by',
    	'updated_by'
    ];
    
}
