<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Cart extends Model
{
    protected $fillable = [
        'user_id','product_id','variant_id','price','quantity','amount','order_id','notes','is_checked',
    ];

    protected $casts = [
        'user_id'=>'int','product_id'=>'int','variant_id'=>'int','order_id'=>'int',
        'quantity'=>'int','price'=>'float','amount'=>'float','is_checked' => 'boolean',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function product() {


        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }
    public function variant() {
        return $this->belongsTo(ProductVariant::class, 'variant_id')->withTrashed();
         }

    // Hapus method ini kalau kamu tidak punya model Order
    public function order()   { return $this->belongsTo(Order::class); }

    public function getComputedAmountAttribute(): float
    {
        $price = $this->price ?? 0;
        $qty   = $this->quantity ?? 0;
        return (float) ($this->amount ?? ($price * $qty));
    }

    public function getLineWeightGramAttribute(): int {
    return (int) round(($this->variant->weight ?? 0) * $this->quantity); // gram
    }
}
