<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $table = 'pages';
    protected $fillable = [
        'name',
    	'title',
    	'description',
    	'image',
    	'status',
    	'created_by',
    	'updated_by'
    ];
}
