<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesignMeasurements extends Model
{
    use HasFactory;
     protected $table = "product_design_measurements";
    protected $fillable = [
    	'product_category_id',
        'label'
    ];

}
