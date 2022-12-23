<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupScheduling extends Model
{
    use HasFactory;
    protected $table = "pickup_schedulings";
    protected $fillable = [
    	'order_id',
    	'pickup_date',
    	'pickup_time',
    	'exp_delivery_date',
    	'pickup_address',
    	'contact_person_name',
    	'contact_person_number',
        'contact_person_email',
    	/*'alternative_contact_person_name',*/
    	'contact_person_alternative_number',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
