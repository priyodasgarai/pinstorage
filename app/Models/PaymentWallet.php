<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentWallet extends Model
{
    use HasFactory;
     protected $table = "payment_wallet";
    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'wallet',
        'debit',
        'credit',
        'status',
        'created_at',
        'updated_at',
        ];
}
