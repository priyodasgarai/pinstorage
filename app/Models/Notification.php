<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
    	'user_id', 
    	'title',
    	'message',
    	'is_read',
    	'status'
    ];
     public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
