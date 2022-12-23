<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'image',
        'country_id',
        'otp',
        'device_type',
        'device_token',
        'email_validate',
        'phone_validate',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'device_token',
    ];
    public function country() {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // public function state() {
    //     return $this->belongsTo(State::class, 'state_id');
    // }

    // public function city() {
    //     return $this->belongsTo(City::class, 'city_id');
    // }
    //  public function other_address() {
    //     return $this->hasMany(UserAddress::class, 'user_id', 'id');
    // }
    //  public function order() {
    //     return $this->hasMany(Order::class, 'user_id', 'id');
    // }
    // public function alteration() {
    //     return $this->hasMany(Order::class, 'user_id', 'id')->where('alteration_id','<>', NULL);;
    // }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
}
