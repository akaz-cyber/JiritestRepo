<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cart;
use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model
{
    use SoftDeletes;
    // protected $fillable=['title','slug','summary','description','cat_id','child_cat_id','price','brand_id','discount','status','photo','size','stock','is_featured','condition'];
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'photo',
        'video',
        'status',
        'cat_id',
        'child_cat_id',
        'view_count',
    ];
    public function cat_info()
    {
        return $this->hasOne(Category::class, 'id', 'cat_id');
    }
    public function sub_cat_info()
    {
        return $this->hasOne(Category::class, 'id', 'child_cat_id');
    }
    public static function getAllProduct(){
        return Product::with(['cat_info','sub_cat_info'])->orderBy('id','desc')->paginate(10);
    }
    public function rel_prods()
    {
        return $this->hasMany(Product::class, 'cat_id', 'cat_id')
                    ->where('status', 'active')
                    ->orderBy('id', 'DESC')
                    ->limit(8);
    }
    public function getReview()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')
                    ->with('user_info')
                    ->where('status', 'active')
                    ->orderBy('id', 'DESC');
    }
    public static function getProductBySlug($slug)
    {
        return Product::with(['variants', 'cat_info', 'rel_prods', 'getReview'])
                      ->where('slug', $slug)
                      ->firstOrFail();
    }
    // untuk terlaris(bestseller)
    public function orderItems()
    {
        return $this->hasMany(Cart::class, 'product_id')->whereNotNull('order_id');
    }

    // public static function countActiveProduct(){
    //     $data=Product::where('status','active')->count();
    //     if($data){
    //         return $data;
    //     }
    //     return 0;
    // }

    public static function countActiveProduct()
    {
    return Product::where('status', 'active')->count();
    }

    public function carts(){
        return $this->hasMany(Cart::class)->whereNotNull('order_id');
    }

    public function variants()
    {
    return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    // buat sorting best seller
    public function views()
    {
    return $this->hasMany(ProductView::class);
    }

    public function getPhotosArrayAttribute()
    {
    if (empty($this->photo)) {
        return [];
    }

    return array_filter(explode('|', $this->photo));
    }

    public function getMainPhotoAttribute()
    {
        return $this->photos_array[0] ?? null;
    }

}
