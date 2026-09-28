<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;
// use App\Models\Wishlist;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Support\Str;

use Helper;
class CartController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    // public function __construct()
    // {
    //     // Pastikan user login untuk aksi yang mengubah data penting
    //     $this->middleware('auth')->only(['placeOrder', 'clear', 'add', 'updateQuantity', 'remove']);
    // }

    // protected function userCartQuery(int $userId)
    // {
    //     return Cart::where('user_id', $userId)->whereNull('order_id');
    // }

    // protected function computeSubtotal(int $userId): float
    // {
    //     $q = $this->userCartQuery($userId);

    //     if (Schema::hasColumn('carts', 'amount')) {
    //         return (float) $q->sum('amount');
    //     }

    //     $items = $q->get(['price', 'quantity']);
    //     return (float) $items->sum(function ($i) {
    //         $price = (float) ($i->price ?? 0);
    //         $qty   = (int)   ($i->quantity ?? 0);
    //         return $price * $qty;
    //     });
    // }

    public function cartIndex()
    {
        // Ambil view-nya
        $view = view('frontend.pages.cart');

        // Kembalikan response DENGAN header no-cache
        return response($view)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }


    public function addToCart(Request $request){
        if (!auth()->check()) {
            return redirect()->route('login.form')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Silakan verifikasi email terlebih dahulu.');
        }
        if (empty($request->slug)) {
            return back()->with('error', 'Invalid Products');
        }

        $product = \App\Models\Product::where('slug', $request->slug)->first();
        if (!$product) {
            return back()->with('error', 'Invalid Products');
        }

        // [NEW] baca varian dari request (opsional)
        $variant = null;
        if ($request->filled('variant_id')) {
            $variant = \App\Models\ProductVariant::where('id', $request->variant_id)
                        ->where('product_id', $product->id)
                        ->first();
            if (!$variant) {
                return back()->with('error', 'Varian tidak valid.');
            }
        }

        // [NEW] hitung harga & stok berdasarkan varian bila ada
        $unitPrice      = $variant->price ?? ($product->price ?? 0);
        $availableStock = $variant ? (int)$variant->stock : (int)($product->stock ?? 0);

        // [NEW] cari cart item unik per (product_id, variant_id)
        $already = \App\Models\Cart::where('user_id', auth()->id())
            ->whereNull('order_id')
            ->where('product_id', $product->id)
            ->when($variant, fn($q) => $q->where('variant_id', $variant->id),
                            fn($q) => $q->whereNull('variant_id'))
            ->first();

        if ($already) {
            $already->quantity = $already->quantity + 1;

            // [NEW] cek stok sesuai varian/produk
            if ($availableStock < $already->quantity || $availableStock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }

            // [NEW] pastikan price adalah harga unit tetap
            $already->price  = $already->price ?? $unitPrice;
            $already->amount = $already->price * $already->quantity;
            $already->save();
        } else {
            $cart = new \App\Models\Cart();
            $cart->user_id    = auth()->id();
            $cart->product_id = $product->id;
            if ($variant) $cart->variant_id = $variant->id;  // [NEW]

            $cart->price    = $unitPrice;                    // [NEW]
            $cart->quantity = 1;

            if ($availableStock < $cart->quantity || $availableStock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $cart->is_checked = false;
            $cart->amount = $cart->price * $cart->quantity;  // [NEW]
            $cart->save();
        }

        return back()->with('success', 'Product successfully added to cart');
    }


    public function singleAddToCart(Request $request){
        if (!auth()->check()) {
            return redirect()->route('login.form')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->with('warning', 'Silakan verifikasi email terlebih dahulu.');
        }
    
        $request->validate([
            'slug'       => 'required',
            'quant'      => 'required',
            'variant_id' => 'nullable|integer|exists:product_variants,id', // [NEW]
        ]);

        $qty = (int)($request->quant[1] ?? 1);
        $product = \App\Models\Product::where('slug', $request->slug)->first();
        if (!$product || $qty < 1) {
            return back()->with('error', 'Invalid Products');
        }

        // [NEW] baca varian dari request (opsional)
        $variant = null;
        if ($request->filled('variant_id')) {
            $variant = \App\Models\ProductVariant::where('id', $request->variant_id)
                        ->where('product_id', $product->id)
                        ->first();
            if (!$variant) {
                return back()->with('error', 'Varian tidak valid.');
            }
        }

        // [NEW] harga & stok berdasarkan varian
        $unitPrice      = $variant->price ?? ($product->price ?? 0);
        $availableStock = $variant ? (int)$variant->stock : (int)($product->stock ?? 0);

        if ($availableStock < $qty) {
            return back()->with('error', 'Out of stock, You can add other products.');
        }

        // [NEW] unik per (product_id, variant_id)
        $already = \App\Models\Cart::where('user_id', auth()->id())
            ->whereNull('order_id')
            ->where('product_id', $product->id)
            ->when($variant, fn($q) => $q->where('variant_id', $variant->id),
                            fn($q) => $q->whereNull('variant_id'))
            ->first();

        if ($already) {
            $already->quantity = $already->quantity + $qty;

            if ($availableStock < $already->quantity || $availableStock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }

            $already->price  = $already->price ?? $unitPrice; // [NEW]
            $already->amount = $already->price * $already->quantity;
            $already->save();
        } else {
            $cart = new \App\Models\Cart();
            $cart->user_id    = auth()->id();
            $cart->product_id = $product->id;
            if ($variant) $cart->variant_id = $variant->id;     // [NEW]

            $cart->price    = $unitPrice;                       // [NEW]
            $cart->quantity = $qty;

            if ($availableStock < $cart->quantity || $availableStock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $cart->is_checked = false;
            $cart->amount = $cart->price * $cart->quantity;     // [NEW]
            $cart->save();
        }

        return back()->with('success', 'Product successfully added to cart.');
    }


    public function cartDelete(Request $request){
        $cart = Cart::find($request->id);
        if ($cart) {
            $cart->delete();
            request()->session()->flash('success','Cart successfully removed');
            return back();
        }
        request()->session()->flash('error','Error please try again');
        return back();
    }


public function cartUpdate(Request $request)
{

    if (!$request->quant) {
        return back()->with('error', 'Keranjang tidak valid!');
    }
    $stockErrors = [];
    $success = '';

    $selectedCartIds = $request->selected_carts ?? [];

    foreach ($selectedCartIds as $cart_id) {
        $cart = Cart::find($cart_id);
        // Ambil kuantitas dari input yang sesuai dengan cart_id
        $quantity = $request->quant[$cart_id] ?? null;
        $qty = (int)$quantity;

        if (!$cart || $qty <= 0) {
            continue; // Abaikan jika cart tidak valid atau kuantitas 0
        }

        // Cek stok
        $availableStock = $cart->variant ? (int)$cart->variant->stock : (int)($cart->product->stock ?? 0);

        if ($availableStock < $qty) {
            $productName = $cart->product->title;
            $variantName = $cart->variant ? ' varian ' . $cart->variant->variant_name : '';
            $stockErrors[] = 'Stok untuk item "' . $productName . $variantName . '" tidak mencukupi (hanya tersisa ' . $availableStock . '). Mohon tunggu sampai stok nya tersedia lagi';
        }
    }
        // Cek stok (logika Anda yang sudah ada)
        if (!empty($stockErrors)) {
            $fullErrorMessage = implode('<br>', $stockErrors);
            if ($request->expectsJson()) {
                return response()->json(['message' => $fullErrorMessage], 422);
            }
            return back()->with('error', $fullErrorMessage);
        }


        foreach ($request->quant as $cart_id => $quantity) {
            $cart = Cart::find($cart_id);
            $qty = (int)$quantity;

            if (!$cart) continue;

            if ($qty <= 0) {
                $cart->delete();
                $success = 'Item berhasil dihapus.';
                continue;
            }

            $cart->quantity = $qty;
            $cart->notes = $request->notes[$cart_id] ?? null;
            $cart->amount = ($cart->price ?? 0) * $cart->quantity;
            $cart->save();
        }

    $success = 'Keranjang berhasil diperbarui!';

    if ($request->expectsJson()) {
        if (empty($selectedCartIds)) {
            return response()->json(['message' => 'Silakan pilih produk terlebih dahulu untuk melanjutkan ke checkout.'], 422);
        }
        return response()->json(['status' => true, 'message' => $success]);
    }

    return back()->with('success', $success);
}


    // public function addToCart(Request $request){
    //     // return $request->all();
    //     if(Auth::check()){
    //         $qty=$request->quantity;
    //         $this->product=$this->product->find($request->pro_id);
    //         if($this->product->stock < $qty){
    //             return response(['status'=>false,'msg'=>'Out of stock','data'=>null]);
    //         }
    //         if(!$this->product){
    //             return response(['status'=>false,'msg'=>'Product not found','data'=>null]);
    //         }
    //         // $session_id=session('cart')['session_id'];
    //         // if(empty($session_id)){
    //         //     $session_id=Str::random(30);
    //         //     // dd($session_id);
    //         //     session()->put('session_id',$session_id);
    //         // }
    //         $current_item=array(
    //             'user_id'=>auth()->user()->id,
    //             'id'=>$this->product->id,
    //             // 'session_id'=>$session_id,
    //             'title'=>$this->product->title,
    //             'summary'=>$this->product->summary,
    //             'link'=>route('product-detail',$this->product->slug),
    //             'price'=>$this->product->price,
    //             'photo'=>$this->product->photo,
    //         );

    //         $price=$this->product->price;
    //         if($this->product->discount){
    //             $price=($price-($price*$this->product->discount)/100);
    //         }
    //         $current_item['price']=$price;

    //         $cart=session('cart') ? session('cart') : null;

    //         if($cart){
    //             // if anyone alreay order products
    //             $index=null;
    //             foreach($cart as $key=>$value){
    //                 if($value['id']==$this->product->id){
    //                     $index=$key;
    //                 break;
    //                 }
    //             }
    //             if($index!==null){
    //                 $cart[$index]['quantity']=$qty;
    //                 $cart[$index]['amount']=ceil($qty*$price);
    //                 if($cart[$index]['quantity']<=0){
    //                     unset($cart[$index]);
    //                 }
    //             }
    //             else{
    //                 $current_item['quantity']=$qty;
    //                 $current_item['amount']=ceil($qty*$price);
    //                 $cart[]=$current_item;
    //             }
    //         }
    //         else{
    //             $current_item['quantity']=$qty;
    //             $current_item['amount']=ceil($qty*$price);
    //             $cart[]=$current_item;
    //         }

    //         session()->put('cart',$cart);
    //         return response(['status'=>true,'msg'=>'Cart successfully updated','data'=>$cart]);
    //     }
    //     else{
    //         return response(['status'=>false,'msg'=>'You need to login first','data'=>null]);
    //     }
    // }

    // public function removeCart(Request $request){
    //     $index=$request->index;
    //     // return $index;
    //     $cart=session('cart');
    //     unset($cart[$index]);
    //     session()->put('cart',$cart);
    //     return redirect()->back()->with('success','Successfully remove item');
    // }
// checkout baru
// public function checkout()
// {
//     $userId = auth()->id();
//     $cartItems = $userId ? $this->getCartItems($userId) : collect();
//     $subtotal  = $userId ? $this->computeSubtotal($userId) : 0.0;

//     // Hitung estimasi diskon untuk tampilan saja (keamanan tetap dihitung ulang saat POST)
//     $discount = 0.0;
//     $coupon   = null;

//     if ($userId && session()->has('coupon')) {
//         $couponData = session('coupon');
//         $coupon     = Coupon::find($couponData['id'] ?? null);

//         if ($coupon && (($coupon->status ?? 'active') === 'active')) {
//             $eligibleSubtotal = method_exists($coupon, 'eligibleSubtotal')
//                 ? (float) $coupon->eligibleSubtotal($cartItems)
//                 : (float) $subtotal;

//             if (method_exists($coupon, 'calculateDiscount')) {
//                 $discount = (float) $coupon->calculateDiscount($eligibleSubtotal);
//             } else {
//                 $discount = (float) $coupon->discount($eligibleSubtotal);
//             }
//             $discount = max(0, min($discount, $eligibleSubtotal));
//         }
//     }

//     $shipping = 0.00; // atur sesuai kebijakanmu
//     $total    = max(0, $subtotal - $discount + $shipping);

//     return view('frontend.pages.checkout', [
//         'cartItems' => $cartItems,
//         'subtotal'  => $subtotal,
//         'discount'  => $discount,
//         'shipping'  => $shipping,
//         'total'     => $total,
//         'coupon'    => $coupon,
//     ]);
// }

    public function checkout(Request $request){
        $selectedIds = [];
    if ($request->has('items')) {
        $selectedIds = explode(',', $request->items);
    }

    if (empty($selectedIds)) {
        return redirect()->route('cart')->with('error', 'Silakan pilih produk terlebih dahulu.');
    }
    Cart::where('user_id', auth()->id())
        ->whereNull('order_id')
        ->update(['is_checked' => false]);
    Cart::where('user_id', auth()->id())
        ->whereNull('order_id')
        ->whereIn('id', $selectedIds)
        ->update(['is_checked' => true]);
    $cartItems = Helper::getAllProductFromCart();

    if($cartItems->isEmpty()){
        request()->session()->flash('error','Tidak ada produk yang dipilih untuk checkout!');
        return redirect()->route('cart');
    }

    return view('frontend.pages.checkout');





        // $cart=session('cart');
        // $cart_index=\Str::random(10);
        // $sub_total=0;
        // foreach($cart as $cart_item){
        //     $sub_total+=$cart_item['amount'];
        //     $data=array(
        //         'cart_id'=>$cart_index,
        //         'user_id'=>$request->user()->id,
        //         'product_id'=>$cart_item['id'],
        //         'quantity'=>$cart_item['quantity'],
        //         'amount'=>$cart_item['amount'],
        //         'status'=>'new',
        //         'price'=>$cart_item['price'],
        //     );

        //     $cart=new Cart();
        //     $cart->fill($data);
        //     $cart->save();
        // }
        // return view('frontend.pages.checkout');
    }


    public function updateSelection(Request $request)
{
    $request->validate([
        'cart_id' => 'required|integer|exists:carts,id',
        'is_checked' => 'required|boolean',
    ]);

    $cart = Cart::where('id', $request->cart_id)
                ->where('user_id', auth()->id())
                ->first();

    if ($cart) {
        $cart->is_checked = $request->is_checked;
        $cart->save();

        $newSubTotal = Cart::where('user_id', auth()->id())
                            ->whereNull('order_id')
                            ->where('is_checked', true)
                            ->sum('amount');

        return response()->json([
            'status' => true,
            'new_sub_total' => $newSubTotal,
        ]);
    }

    return response()->json(['status' => false, 'message' => 'Cart not found'], 404);
}

public function placeOrder(Request $request)
{
    $userId = auth()->id();
    if (!$userId) {
        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    return DB::transaction(function () use ($userId) {
        // Kunci baris cart supaya konsisten selama proses
        $cartItems = Cart::where('user_id', $userId)
            ->whereNull('order_id')
            ->lockForUpdate()
            ->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // Subtotal akurat
        $subtotal = $this->computeSubtotal($userId);

        // Kebijakan ongkir
        $shipping     = 0.00;
        $freeShipping = false;

        $discount = 0.00;
        $couponId = null;

        // Kupon dari session (jika ada)
        if (session()->has('coupon')) {
            $couponData = session('coupon');

            // Kunci baris kupon (hindari race condition)
            $coupon = Coupon::where('id', $couponData['id'] ?? null)
                ->lockForUpdate()
                ->first();

            $isActive = $coupon && (
                method_exists($coupon, 'isUsableNow')
                    ? $coupon->isUsableNow()
                    : (($coupon->status ?? 'active') === 'active')
            );

            if ($isActive) {
                // (Opsional) per-user limit
                $usage = null;
                $perUserOk = true;

                if (class_exists(\App\Models\CouponUsage::class)) {
                    $usage = \App\Models\CouponUsage::where('coupon_id', $coupon->id)
                        ->where('user_id', $userId)
                        ->lockForUpdate()
                        ->first();

                    if (!$usage) {
                        $usage = \App\Models\CouponUsage::create([
                            'coupon_id'  => $coupon->id,
                            'user_id'    => $userId,
                            'used_count' => 0,
                        ]);
                        $usage = \App\Models\CouponUsage::where('id', $usage->id)->lockForUpdate()->first();
                    }

                    if (!is_null($coupon->per_user_limit ?? null)) {
                        $perUserOk = ($usage->used_count ?? 0) < $coupon->per_user_limit;
                    }
                }

                $globalOk = is_null($coupon->usage_limit ?? null)
                    || ($coupon->times_used ?? 0) < $coupon->usage_limit;

                $minOk = is_null($coupon->min_spend ?? null)
                    || $subtotal >= (float) $coupon->min_spend;

                if ($globalOk && $perUserOk && $minOk) {
                    // Eligible subtotal → jika ada helper scope, gunakan; jika tidak subtotal penuh
                    $eligibleSubtotal = method_exists($coupon, 'eligibleSubtotal')
                        ? (float) $coupon->eligibleSubtotal($cartItems)
                        : (float) $subtotal;

                    // Hitung diskon (pakai method yang tersedia di model)
                    if (method_exists($coupon, 'calculateDiscount')) {
                        $discount = (float) $coupon->calculateDiscount($eligibleSubtotal);
                    } else {
                        $discount = (float) $coupon->discount($eligibleSubtotal);
                    }

                    $discount = max(0, min($discount, $eligibleSubtotal));
                    $freeShipping = (bool) ($coupon->free_shipping ?? false);
                    $couponId     = $coupon->id;
                } else {
                    session()->forget('coupon'); // syarat tak terpenuhi
                }
            } else {
                session()->forget('coupon'); // tidak aktif/kadaluarsa
            }
        }

        $shippingToUse = $freeShipping ? 0.00 : $shipping;
        $total         = max(0, $subtotal - $discount + $shippingToUse);

        // Buat order
        $order = Order::create([
            'user_id'   => $userId,
            'subtotal'  => $subtotal,
            'discount'  => $discount,
            'shipping'  => $shippingToUse,
            'total'     => $total,
            'coupon_id' => $couponId,
        ]);

        // Simpan item order
        // foreach ($cartItems as $item) {
        //     OrderItem::create([
        //         'order_id'   => $order->id,
        //         'product_id' => $item->product_id,
        //         'price'      => $item->price,
        //         'quantity'   => $item->quantity,
        //         'total'      => $item->price * $item->quantity,
        //     ]);
        // }

        // Kaitkan cart → order
        Cart::whereIn('id', $cartItems->pluck('id'))
            ->update(['order_id' => $order->id]);

        // Jika kupon dipakai → naikkan counter (global & per-user)
        if ($couponId) {
            Coupon::where('id', $couponId)->increment('times_used', 1);

            if (class_exists(\App\Models\CouponUsage::class)) {
                \App\Models\CouponUsage::where('coupon_id', $couponId)
                    ->where('user_id', $userId)
                    ->increment('used_count', 1);
            }

            session()->forget('coupon');
        }

        // Selesai
        return redirect()->route('orders.show', $order)
                         ->with('success', 'Pesanan dibuat.');
    });
}

public function cartDeleteSelected(Request $request)
{
    $request->validate([
        'ids'   => 'required|array|min:1',
        'ids.*' => 'integer|exists:carts,id',
    ]);

    $ids = $request->input('ids');
    $userId = auth()->id();

    // Menghapus item yang ID-nya ada di array DAN milik user yang sedang login
    $deletedCount = Cart::where('user_id', $userId)
                        ->whereIn('id', $ids)
                        ->delete();

    if ($deletedCount > 0) {
        //
        // Mengembalikan respon JSON karena ini dipanggil oleh AJAX
        return response()->json([
            'status'  => true,
            'message' => $deletedCount . ' item berhasil dihapus.'
        ]);
    }

    return response()->json([
        'status'  => false,
        'message' => 'Tidak ada item yang dipilih atau item tidak ditemukan.'
    ], 400);
}
}
