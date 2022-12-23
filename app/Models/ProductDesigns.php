<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesigns extends Model
{
    use HasFactory;
    protected $table = "product_designs";
    protected $fillable = [
    	'category_id',
        'delivery_id',
        'size_id',
        'title',
        'size',
        'quantity',
        'size',
        'price',
        'short_description',
        'is_featured',
        'is_trending',
        'inner_type',
        'type_banner_img',
        'status',
        'created_by',
        'updated_by'
    ];
         public function productimg()
    {
        return $this->hasOne(ProductDesignImages::class,'product_design_id')->where('is_primary','=','1');;
    }
      public function category() {
        return $this->belongsTo(CategoryModel::class, 'category_id')->select('id','name','parent','description','image','banner_image');
    }
    
}
