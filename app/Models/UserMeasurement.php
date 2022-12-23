<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeasurement extends Model
{
    use HasFactory;
     protected $table = "user_measurement";
  protected $casts = [
        'measurement' => 'json',
    ];
    protected $fillable = [
        'category_id',
        'user_id',
        'measurement',            
        'created_at',
        'updated_at'
    ];
     public function users() {
        return $this->hasMany(User::class,  'id','user_id');
    }
}
