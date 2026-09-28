<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    protected $fillable=['user_id',
        'order_number',
        'sub_total',
        'quantity',
        'total_weight_gram',
        'delivery_charge',
        'shipping_courier',
        'shipping_service',
        'status',
        'total_amount',
        'first_name',
        'last_name',
        'post_code',
        'address1',
        'phone',
        'email',
        'payment_method',
        'payment_status',
        'coupon',
        'province_id',
        'city_id',
        'province_name',
        'city_name',
        'district_id',
        'district_name',
        'sub_district_id',
        'sub_district_name',
        'resi_number',
        'jne_phone_verify'
    ];

    public function cart_info(){
        return $this->hasMany('App\Models\Cart','order_id','id');
    }
    public static function getAllOrder($id){
        return Order::with('cart_info')->find($id);
    }
    public static function countActiveOrder(){
        $data=Order::count();
        if($data){
            return $data;
        }
        return 0;
    }
    public function cart(){
        return $this->hasMany(Cart::class);
    }

    // public function shipping(){
    //     return $this->belongsTo(Shipping::class,'shipping_id');
    // }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

}
