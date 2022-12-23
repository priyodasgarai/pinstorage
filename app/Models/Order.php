<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    
    protected $casts = [
        'measurement' => 'json',
    ];
    protected $fillable = [
        'id',
    	'user_id',
    	'size_id',
    	'design_id',
        'is_customize',
        'order_prefix_id',
        'stiching_info',
        'additional_info',
    	'price',
    	'quantity',
    	'delivery_id',
    	'shipping_address_id',
        'measurement_address_id',
        'status',
        'alteration_id',
        'order_type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'measurement',
        'otp',
        'payment_status',
        'payment_type',
        'due_price',
        'extra_charge',
        'is_lining',
        'is_alteration',
        'is_padded',
        'payment_method',
    ];
    
    public function shipping()
    {
        return $this->belongsTo(UserAddress::class,'shipping_address_id');
    }

    public function measurement_address()
    {
        return $this->belongsTo(UserAddress::class,'measurement_address_id');
    }
    public function product()
    {
        return $this->belongsTo(ProductDesigns::class,'design_id','id');
    }
    public function orders()
    {
        return $this->hasOne(OrderStatus::class,'order_id')->OrderBy('created_at','DESC')->limit(1);
    }
     public function alteration()
      {
        return $this->belongsTo(AlterationRequest::class,'alteration_id');
    }
      public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function productDesign()
    {
        return $this->hasOne(ProductDesigns::class,'id','design_id');//->select('id','category_id','delivery_id','title','quantity','price','inner_type','type_banner_img');
    }
     public function pickup_schedulings()
    {
        return $this->hasOne(PickupScheduling::class,'order_id','id');
    }
     public function order_adresses()
    {
        return $this->belongsTo(OrderAdress::class,'id','order_id');
    }
     public function product_design_images()
    {
        return $this->hasMany(ProductDesignImages::class,'product_design_id','id');
    }
     public function order_by_agent_assigns()
    {
        return $this->hasOne(OrderByAgentAssign::class,'order_id','id');
    }
     function DesignImages () {
       return $this->hasOne(ProductDesignImages::class,'product_design_id','id')->where('is_primary', 1);
    }

    public function CustomizeOrder()
    {
        return $this->hasMany(CustomizeOrder::class,'order_id','id');
    }
     public function OrderStatus() {
         return $this->hasMany(OrderStatus::class,  'order_id','id');
    }
    public function OrderPayment() {
        return $this->hasMany(PaymentStatus::class,  'order_id','id');
    }

    public function getMeasurementAttribute() {
        return json_decode($this->attributes['measurement']);
    }
    

    public function deliveryDetails() {
          return $this->hasOne(Delivery::class,'id','delivery_id');
    }


}
