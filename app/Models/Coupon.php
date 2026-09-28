<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
      'code','type','value','status','starts_at','expires_at',
      'min_spend','max_discount','usage_limit','times_used',
      'per_user_limit','applies_to','free_shipping'
    ];

    protected $casts = [
        'starts_at'     => 'datetime',
        'expires_at'    => 'datetime',
        'usage_limit'   => 'int',
        'times_used'    => 'int',
        'per_user_limit'=> 'int',
        'min_spend'     => 'decimal:2',
        'max_discount'  => 'decimal:2',
        'free_shipping' => 'nullable|boolean',
        'value'         => 'decimal:2',

    ];

    public function products() { return $this->belongsToMany(Product::class,'coupon_products'); }
    public function categories() { return $this->belongsToMany(Category::class,'coupon_categories'); }
    public function usages(): HasMany { return $this->hasMany(CouponUsage::class); }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', trim($code))->first();
    }

    public function isUsableNow(): bool{
        $now = now();
        // kalau null → lulus
        $startOk  = !$this->starts_at  || $now->greaterThanOrEqualTo($this->starts_at instanceof Carbon ? $this->starts_at : Carbon::parse($this->starts_at));
        $expireOk = !$this->expires_at || $now->lessThanOrEqualTo($this->expires_at instanceof Carbon ? $this->expires_at : Carbon::parse($this->expires_at));

        return ($this->status ?? 'inactive') === 'active' && $startOk && $expireOk;
    }

    public function remainingGlobal(): ?int
    {
        return is_null($this->usage_limit) ? null : max(0, $this->usage_limit - $this->times_used);
    }

    public function remainingForUser(int $userId): ?int
    {
        if (is_null($this->per_user_limit)) return null;
        $used = $this->usages()->where('user_id',$userId)->value('used_count') ?? 0;
        return max(0, $this->per_user_limit - $used);
    }

    public function canBeUsedBy(int $userId): bool
    {
        if (!$this->isUsableNow()) return false;

        if (!is_null($this->per_user_limit)) {
            $used = $this->usages()->where('user_id',$userId)->value('used_count') ?? 0;
            if ($used >= $this->per_user_limit) return false;
        }
        return true;
    }

    public function calculateDiscount($eligibleSubtotal)
    {
        // Konversi semua nilai ke string atau gunakan BCMath untuk presisi tinggi
        $eligibleSubtotal = (string) $eligibleSubtotal;
        $value = (string) $this->value;
        $maxDiscount = !is_null($this->max_discount) ? (string) $this->max_discount : null;
        $discount = '0.00';
        if ($this->type === 'percent') {
            // Menggunakan BCMath untuk kalkulasi presisi tinggi
            $percentValue = bcdiv($value, '100', 10); // value / 100
            $discount = bcmul($percentValue, $eligibleSubtotal, 2); // (value/100) * subtotal
        } else {
            $discount = $value;
        }

        if (!is_null($maxDiscount) && bccomp($discount, $maxDiscount) === 1) {
            // Jika diskon > max_discount, gunakan max_discount
            $discount = $maxDiscount;
        }

        // Pastikan diskon tidak lebih besar dari subtotal itu sendiri
        if (bccomp($discount, $eligibleSubtotal) === 1) {
            $discount = $eligibleSubtotal;
        }

        // Pastikan hasilnya tidak negatif
        return (bccomp($discount, '0.00') === -1) ? '0.00' : $discount;
    }

    /**
     * Hitung subtotal item yang eligible berdasarkan applies_to.
     * $cartItems: Collection<CartItem|Cart> dengan field: product_id, category_id, price, quantity
     */
    public function eligibleSubtotal($cartItems): float
    {
        if ($this->applies_to === 'all') {
            return (float) $cartItems->sum(fn($c) => $c->price * $c->quantity);
        }

        $sum = 0.0;
        $productIds = $this->products()->pluck('products.id')->all();
        $categoryIds = $this->categories()->pluck('categories.id')->all();

        foreach ($cartItems as $c) {
            $line = $c->price * $c->quantity;
            $inProduct = in_array($c->product_id, $productIds ?? []);
            $inCategory = in_array($c->category_id, $categoryIds ?? []);

            switch ($this->applies_to) {
                case 'products':
                    if ($inProduct) $sum += $line;
                    break;
                case 'categories':
                    if ($inCategory) $sum += $line;
                    break;
                case 'exclude_products':
                    if (!$inProduct) $sum += $line;
                    break;
                case 'exclude_categories':
                    if (!$inCategory) $sum += $line;
                    break;
            }
        }
        return (float)$sum;
    }
}
