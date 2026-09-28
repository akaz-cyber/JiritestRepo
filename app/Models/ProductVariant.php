<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'product_id',
        'variant_name',
        'price',              // Harga Jual (wajib)
        'original_price',     // [NEW] Harga Asli (coret)
        'discount_percent',   // [NEW] Diskon (%) manual
        'stock',
        'sku',
        'weight',
        'photo',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price'            => 'float',
        'original_price'   => 'float',
        'discount_percent' => 'int',
        'stock'            => 'int',
        'weight'           => 'int',
        'is_active'        => 'bool',
        'sort_order'       => 'int',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Harga yang dipakai (Harga Jual). */
    public function getDisplayPriceAttribute(): float
    {
        return (float) ($this->price ?? 0);
    }

    /** Harga coret (jika ada). */
    public function getStrikePriceAttribute(): ?float
    {
        return $this->original_price ?: null;
    }

    /** Diskon % untuk badge (manual jika ada, fallback hitung dari strike vs display). */
    public function getDisplayDiscountAttribute(): ?int
    {
        if (!is_null($this->discount_percent)) {
            return (int) $this->discount_percent;
        }
        if ($this->original_price && $this->price && $this->original_price > $this->price) {
            return (int) round( (1 - ($this->price / $this->original_price)) * 100 );
        }
        return null;
    }

    /** [Compat] final_price → sekarang sama dengan Harga Jual (hindari diskon otomatis). */
    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->price ?? 0);
    }
}
