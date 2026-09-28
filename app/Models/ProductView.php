<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductView extends Model
{
    public $timestamps = false;
    protected $fillable = ['product_id', 'viewed_at'];
}
