<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchData extends Model
{
    use HasFactory;
    protected $table = 'search_data';
    protected $fillable = [
    	'user_id',
    	'search_key',
        'status'
    ];
}
