<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentLaser extends Model
{
    use HasFactory;    
    protected $table = "payment_laser";    
    protected $fillable = [
        'id',
        'payment_id',
        'order_id',
        'signature_hash',
        'created_at',        
        'updated_at',
        ];
}
