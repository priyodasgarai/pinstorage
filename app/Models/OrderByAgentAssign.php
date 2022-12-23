<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderByAgentAssign extends Model
{
    use HasFactory;
    protected $table = "order_by_agent_assigns";
    protected $fillable = [
    	'order_id',
    	'user_id',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
