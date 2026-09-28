<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class Address extends Model
{
    protected $fillable = [
        'user_id','label',
        'first_name','last_name','email','phone',
        'province_id','province_name','city_id','city_name',
        'post_code','address1','is_default','district_id',
        'district_name','sub_district_id', 'sub_district_name'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}