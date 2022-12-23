<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDesignImages extends Model
{
    use HasFactory;
    protected $table = "product_design_images";
    protected $fillable = [
    	'product_design_id',
        'file_name',
        'is_primary'
    ];

    /*public function item()
	{
	return $this->belongsTo('App\Item');
	}*/

}
