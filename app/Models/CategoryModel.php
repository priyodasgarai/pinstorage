<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasFactory;
    protected $table = "category_master";
    protected $fillable = [
        'name',
        'description',
        'image',
        'banner_image',
        'parent',
        'status',
        'created_by',
        'updated_by',
    ];

    public function parent()
    {
       return $this->hasOne('App\Models\CategoryModel','parent','id');
    }
     public function parentname()
    {
       return $this->belongsTo('App\Models\CategoryModel','parent','id')->select( 'id','name');
    }
    public function childiren()
    {
       return $this->hasMany('App\Models\CategoryModel','id','parent');
    }
     public function ProductDesignMeasurements() {
        return $this->hasMany(ProductDesignMeasurements::class, 'product_category_id', 'id');
    }
}
