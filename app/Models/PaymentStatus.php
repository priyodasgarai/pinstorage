<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;
    protected $table = "payment_status";
    protected $fillable = [
        'id',
        'order_id',
        'total_price',
        'paid_price',
        'due_price',
        'payment_mode',
        'payment_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        ];
}
