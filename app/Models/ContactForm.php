<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    use HasFactory;
    protected $table = "contact_forms";
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'date',
        'from_time',
        'to_time',
        'message',
        'status',
        'created_by',
        'updated_by'
    ];
}
