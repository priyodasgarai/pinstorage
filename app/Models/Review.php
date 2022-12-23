<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = "reviews";
    protected $fillable = [
        'user_id',
        'style_id',
        'rating',
        'comment',
        'upload_image',
        'status',
        'approval',
        'created_by',
        'updated_by'
    ];
}
