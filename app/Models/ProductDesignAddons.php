<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesignAddons extends Model
{
    use HasFactory;
    protected $table = "product_design_addons";
    protected $fillable = [
    	'product_design_id',
    	'input_group',
        'custom_price',
        'custom_image',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    public function addonsImages()
    {
        return $this->hasMany(ProductDesignAddonsImages::class,'product_design_addon_id');
    }
    
     public function productDesign()
    {
        return $this->belongsTo(ProductDesigns::class,'product_design_id','id');
    }
}
