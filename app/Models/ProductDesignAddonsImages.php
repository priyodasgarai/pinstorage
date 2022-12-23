<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesignAddonsImages extends Model
{
    use HasFactory;
    protected $table = "product_design_addons_images";
    protected $fillable = [
    	'product_design_addon_id',
        'title',
        'price',
        'addon_image',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
