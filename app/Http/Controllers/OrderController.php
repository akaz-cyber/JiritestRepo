<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
// use App\Models\Shipping;
use App\User;
use PDF;
use Notification;
use Helper;
use Illuminate\Support\Str;
use App\Notifications\StatusNotification;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\ProductVariant;
use App\Models\Coupon;
use App\Models\Address;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders=Order::orderBy('id','DESC')->paginate(10);
        return view('backend.order.index')->with('orders',$orders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
{
    $request->validate([
        'payment_method' => 'required|in:cod,midtrans,manual_transfer',
        'address_option' => 'required|in:existing,new',
        'address_id'     => 'required_if:address_option,existing|nullable|integer',
    ]);

    $user = auth()->user();
    $userHasAddresses = false;
    if ($user && $user instanceof \App\User) {
        $userHasAddresses = $user->addresses()->exists();
    }
    if ($request->address_option === 'new' && $userHasAddresses) {
        throw ValidationException::withMessages([
            'address_id' => 'Silakan pilih alamat yang sudah anda buat.',
        ]);
    }

    // =========================
    // [ADDR-VALIDATE-2] Validasi detail alamat BARU hanya jika 'new'
    // =========================
    if ($request->address_option === 'new') {
        $request->validate([
            // 'first_name'    => 'required|string',
            // 'last_name'     => 'required|string',
            'email'         => 'required|email',
            'phone'         => 'required',
           'address1'      => 'required_unless:payment_method,cod|nullable|string',
            'post_code'     => 'required_unless:payment_method,cod|nullable',
            'province_id'   => 'required_unless:payment_method,cod|nullable',
            'province_name' => 'required_unless:payment_method,cod|nullable',
            'city_id'       => 'required_unless:payment_method,cod|nullable',
            'city_name'     => 'required_unless:payment_method,cod|nullable',
            'district_id'   => 'required_unless:payment_method,cod|nullable',
            'district_name' => 'required_unless:payment_method,cod|nullable',
            'sub_district_id'   => 'required_unless:payment_method,cod|nullable',
            'sub_district_name' => 'required_unless:payment_method,cod|nullable',
            'label'         => 'nullable|string|max:100',
            'save_address'  => 'nullable|boolean',
            'set_default'   => 'nullable|boolean',
        ],
         [
            'first_name.required' => 'Silakan buat dulu alamat terlebih dahulu.',
            'last_name.required'  => 'Silakan buat dulu alamat terlebih dahulu.',
            'email.required'      => 'Silakan buat dulu alamat terlebih dahulu.',
            'phone.required'      => 'Silakan buat dulu alamat terlebih dahulu.',
          ]);
    }

    // Untuk metode yang butuh pengiriman, validasi elemen ongkirnya
    if (in_array($request->payment_method, ['midtrans','manual_transfer'], true)) {
        $request->validate([
            'shipping_cost'    => 'required|numeric',
            'shipping_service' => 'required|string',
            'shipping_courier' => 'required|string',
        ],

    [
        'shipping_cost.required'    => 'Silakan pilih kurir dan layanan pengiriman terlebih dahulu.',
        'shipping_service.required' => 'Silakan pilih kurir dan layanan pengiriman terlebih dahulu.',
        'shipping_courier.required' => 'Silakan pilih kurir dan layanan pengiriman terlebih dahulu.',
    ]);
    }

    // =========================
    // [ADDR-FETCH] Ambil data alamat sesuai opsi
    // =========================
    if ($request->address_option === 'existing') {
        $address = Address::where('user_id', auth()->id())->findOrFail($request->address_id);

        $addrData = [
            'first_name'    => $address->first_name,
            'last_name'     => $address->last_name,
            'email'         => $address->email,
            'phone'         => $address->phone,
            'province_id'   => $address->province_id,
            'province_name' => $address->province_name,
            'sub_district_id'   => $address->sub_district_id,
            'sub_district_name' => $address->sub_district_name,
            'city_id'       => $address->city_id,
            'city_name'     => $address->city_name,
            'district_id'   => $address->district_id,
            'district_name' => $address->district_name,
            'post_code'     => $address->post_code,
            'address1'      => $address->address1,
        ];
    } else {
        $nameParts = explode(' ', auth()->user()->name, 2);
        $addrData = [
           'first_name'    => $nameParts[0],
            'last_name'     => isset($nameParts[1]) ? $nameParts[1] : '-',
            'email'         => $request->input('email'),
            'phone'         => $request->input('phone'),
            'province_id'   => $request->input('province_id', null),
            'province_name' => $request->input('province_name', null),
            'city_id'       => $request->input('city_id', null),
            'city_name'     => $request->input('city_name', null),
            'district_id'   => $request->input('district_id', null),
            'district_name' => $request->input('district_name', null),
            'sub_district_id'   => $request->input('sub_district_id', null),
            'sub_district_name' => $request->input('sub_district_name', null),
            'post_code'     => $request->input('post_code', null),
            'address1'      => $request->input('address1', null),
        ];

        // [ADDR-SAVE] Simpan ke buku alamat jika diminta
        if ($request->boolean('save_address') && $request->payment_method !== 'cod') {
            $new = Address::create([
                'user_id'     => auth()->id(),
                'label'       => $request->input('label'),
                'is_default'  => $request->boolean('set_default'),
            ] + $addrData);

            if ($request->boolean('set_default')) {
                Address::where('user_id', auth()->id())
                    ->where('id','!=',$new->id)
                    ->update(['is_default' => false]);
            }
        }
    }

    // =========================
    // [CART] Ambil hanya item yang dicentang (sesuai punyamu) + relasi product & variant
    // =========================
    $checkedCarts = Cart::with(['product','variant'])
        ->where('user_id', auth()->id())
        ->whereNull('order_id')
        ->where('is_checked', true)
        ->get();

    if ($checkedCarts->isEmpty()) {
        return response()->json(['status' => 'error', 'message' => 'Keranjang Anda kosong atau tidak ada item yang dipilih.'], 404);
    }
    $totalWeightGrams = (int) $checkedCarts->sum('line_weight_gram');

    // =========================
    // [SUBTOTAL & QTY]
    // =========================
    $subtotal = '0.00'; // string untuk BCMath
    $qty      = 0;
    foreach ($checkedCarts as $row) {
        // harga final ambil dari cart (umumnya sudah dibekukan saat add-to-cart).
        $price = (string) number_format((float) ($row->price ?? 0), 2, '.', '');
        $q     = (int) ($row->quantity ?? 0);
        $line  = bcmul($price, (string) $q, 2);
        $subtotal = bcadd($subtotal, $line, 2);
        $qty     += $q;
    }

    // =========================
    // [ONGKIR] dari request
    // =========================
    $shippingCost = (string) number_format((float) $request->input('shipping_cost', 0), 2, '.', '');

    // =========================
    // [COUPON] samakan dengan checkout
    // =========================
    $discount = '0.00';
    if (session()->has('coupon')) {
        $couponModel = session('coupon.id') ? Coupon::find(session('coupon.id')) : null;

        if ($couponModel && method_exists($couponModel, 'calculateDiscount')) {
            $eligibleSubtotal = $subtotal;
            $calc = (string) $couponModel->calculateDiscount($eligibleSubtotal);
            $discount = (float)$calc > (float)$subtotal ? $subtotal : $calc;
        } else {
            $type  = session('coupon.type', 'fixed');
            $value = (float) session('coupon.value', 0);
            $min   = (float) session('coupon.min_spend', 0);
            $cap   = (float) session('coupon.max_discount', 0);

            $calc = 0.0;
            if ((float)$subtotal >= $min) {
                if ($type === 'percent') {
                    $calc = round((float)$subtotal * ($value / 100), 2);
                } else {
                    $calc = round($value, 2);
                }
                if ($cap > 0 && $calc > $cap) $calc = $cap;
                if ($calc > (float)$subtotal)  $calc = (float)$subtotal;
            }
            $discount = (string) number_format($calc, 2, '.', '');
        }
    }

    // =========================
    // [TOTAL]
    // =========================
    $pretot = bcadd($subtotal, $shippingCost, 2);
    $total  = bcsub($pretot, $discount, 2);
    $baseOrderData = [
        'order_number'     => 'ORD-'.strtoupper(Str::random(10)),
        'user_id'          => auth()->id(),

        // snapshot nama/kontak dari addrData
        'first_name'       => $addrData['first_name'],
        'last_name'        => $addrData['last_name'],
        'email'            => $addrData['email'],
        'phone'            => $addrData['phone'],

        // snapshot alamat
        'address1'         => $addrData['address1'],
        'post_code'        => $addrData['post_code'],
        // buka jika kolom ada di tabel orders
        'province_id'      => $addrData['province_id'],
        'province_name'    => $addrData['province_name'],
        'city_id'          => $addrData['city_id'],
        'city_name'        => $addrData['city_name'],
        'district_id'      => $addrData['district_id'],
        'district_name'    => $addrData['district_name'],
        'sub_district_id'   => $addrData['sub_district_id'],
        'sub_district_name' => $addrData['sub_district_name'],

        // pengiriman
        'shipping_courier' => $request->input('shipping_courier'),
        'shipping_service' => $request->input('shipping_service'),

        'sub_total'        => $subtotal,
        'delivery_charge'  => $shippingCost,
        'coupon'           => $discount,
        'total_amount'     => $total,
        'quantity'         => $qty,
        'total_weight_gram' => $totalWeightGrams,

        'payment_method'   => $request->payment_method,
        'payment_status'   => 'unpaid',
        'status'           => $request->payment_method === 'cod' ? 'process' : 'new',
    ];

    // COD: override ongkir & alamat
    if ($request->payment_method === 'cod') {
        $baseOrderData['delivery_charge']  = '0.00';
        $baseOrderData['shipping_courier'] = null;
        $baseOrderData['shipping_service'] = null;
        // $baseOrderData['address1']         = 'Pengambilan di Tempat';
        $baseOrderData['total_amount']     = bcsub($subtotal, $discount, 2);
    }

    DB::beginTransaction();
    try {
        // Buat order
        $order = new Order();
        $order->fill($baseOrderData);
        $order->save();

        // Kaitkan cart -> order
        foreach ($checkedCarts as $cart) {
            $cart->order_id  = $order->id;
            $cart->is_checked = false;
            $cart->save();
        }

        // Cabang pembayaran
        if ($request->payment_method === 'midtrans') {
            // item_details untuk Snap (tetap pakai variant punyamu)
            $item_details = [];
            foreach ($checkedCarts as $cart) {
                $item_details[] = [
                    'id'       => $cart->variant->id ?? $cart->id,
                    'price'    => (int) round((float) $cart->price),
                    'quantity' => (int) $cart->quantity,
                    'name'     => (string) ($cart->product->title ?? 'Item'),
                ];
            }
            if ((float)$shippingCost > 0) {
                $item_details[] = [
                    'id' => 'SHIPPING_COST',
                    'price' => (int) round((float) $shippingCost),
                    'quantity' => 1,
                    'name' => "Ongkos Kirim ({$request->shipping_courier} - {$request->shipping_service})",
                ];
            }
            if ((float)$discount > 0) {
                $item_details[] = [
                    'id' => 'COUPON_DISCOUNT',
                    'price' => -(int) round((float) $discount),
                    'quantity' => 1,
                    'name' => "Diskon Kupon",
                ];
            }

            // Konfigurasi Midtrans
            Config::$serverKey    = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized  = config('midtrans.is_sanitized');
            Config::$is3ds        = config('midtrans.is_3ds');

            $params = [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => (int) round((float) $order->total_amount),
                ],
                'customer_details' => [
                    'first_name' => $order->first_name,
                    'last_name'  => $order->last_name,
                    'email'      => $order->email,
                    'phone'      => $order->phone,
                ],
                'item_details' => $item_details,
            ];

            $snapToken = Snap::getSnapToken($params);

            DB::commit();
            session()->forget('coupon');

            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new StatusNotification([
                    'title'     => 'Pesanan baru telah dibuat',
                    'actionURL' => route('order.show', $order->id),
                    'fas'       => 'fa-file-alt',
                ]));
            }

            return response()->json([
                'status'       => 'success',
                'snap_token'   => $snapToken,
                'order_number' => $order->order_number,
                'redirect_url' => route('user.order.index'),
            ]);
        }

        if ($request->payment_method === 'manual_transfer') {
            DB::commit();
            session()->forget('coupon');

            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new StatusNotification([
                    'title'     => 'Pesanan baru Bank Transfer Manual telah dibuat',
                    'actionURL' => route('order.show', $order->id),
                    'fas'       => 'fa-file-alt',
                ]));
            }

            return response()->json([
                'status'       => 'success',
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'redirect_url' => route('user.order.index'),
            ]);
        }

        // COD
        DB::commit();
        session()->forget('coupon');

        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new StatusNotification([
                'title'     => 'Pesanan baru (Ambil di Tempat) telah dibuat',
                'actionURL' => route('order.show', $order->id),
                'fas'       => 'fa-file-alt',
            ]));
        }

        return response()->json([
            'status'       => 'success',
            'redirect_url' => route('user.order.index'),
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error($e);
        return response()->json([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan pada server: '.$e->getMessage(),
        ], 500);
    }
}


        // if(empty(Cart::where('user_id',auth()->user()->id)->where('order_id',null)->first())){
        //     request()->session()->flash('error','Cart is Empty !');
        //     return back();
        // }



        // $cart=Cart::get();
        // // return $cart;
        // $cart_index='ORD-'.strtoupper(uniqid());
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

        // $total_prod=0;
        // if(session('cart')){
        //         foreach(session('cart') as $cart_items){
        //             $total_prod+=$cart_items['quantity'];
        //         }
        // }

        // $order=new Order();
        // $order_data=$request->all();
        // $order_data['order_number']='ORD-'.strtoupper(Str::random(10));
        // $order_data['user_id']=$request->user()->id;
        // $order_data['shipping_id']=$request->shipping;
        // $shipping=Shipping::where('id',$order_data['shipping_id'])->pluck('price');

        // return session('coupon')['value'];
        // $order_data['sub_total']=Helper::totalCartPrice();
        // $order_data['quantity']=Helper::cartCount();
        // if(session('coupon')){
        //     $order_data['coupon']=session('coupon')['value'];
        // }
        // if($request->shipping){
        //     if(session('coupon')){
        //         $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0]-session('coupon')['value'];
        //     }
        //     else{
        //         $order_data['total_amount']=Helper::totalCartPrice()+$shipping[0];
        //     }
        // }
        // else{
        //     if(session('coupon')){
        //         $order_data['total_amount']=Helper::totalCartPrice()-session('coupon')['value'];
        //     }
        //     else{
        //         $order_data['total_amount']=Helper::totalCartPrice();
        //     }
        // }
        // return $order_data['total_amount'];
        // $order_data['status']="new";
        // if(request('payment_method')=='paypal'){
        //     $order_data['payment_method']='paypal';
        //     $order_data['payment_status']='paid';
        // }
        // else{
        //     $order_data['payment_method']='cod';
        //     $order_data['payment_status']='Unpaid';
        // }
        // $order->fill($order_data);
        // $status=$order->save();
        // if($order)
        // dd($order->id);
        // $users=User::where('role','admin')->first();
        // $details=[
        //     'title'=>'New order created',
        //     'actionURL'=>route('order.show',$order->id),
        //     'fas'=>'fa-file-alt'
        // ];
        // Notification::send($users, new StatusNotification($details));
        // if(request('payment_method')=='paypal'){
        //     return redirect()->route('payment')->with(['id'=>$order->id]);
        // }
        // else{
        //     session()->forget('cart');
        //     session()->forget('coupon');
        // }
        // Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);

        // dd($users);
        // request()->session()->flash('success','Your product successfully placed in order');
        // return redirect()->route('home');


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order=Order::find($id);
        // return $order;
        return view('backend.order.show')->with('order',$order);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // cek stok pada variant cart dan variant produk
        $order = Order::with('cart.variant', 'cart.product')->find($id);

        if (!$order) {
            request()->session()->flash('error', 'Order tidak ditemukan');
            return redirect()->route('order.index');
        }

        $isStockSufficient = true; // Anggap stok cukup
        $outOfStockItems = [];     // Untuk menyimpan item mana saja yang stoknya habis

        // Cek stok HANYA jika status order masih 'new' atau 'process'
        // Jika sudah 'delivered' atau 'cancel', tidak perlu cek stok lagi.
        if (in_array($order->status, ['new', 'process'])) {
            foreach ($order->cart as $item) {
                $stockAvailable = 0;
                $itemName = 'Item tidak dikenal';
                $quantityNeeded = (int)$item->quantity;

                // Prioritaskan cek stok varian
                if (!empty($item->variant_id) && $item->variant_id != 0 && $item->variant) {
                    $stockAvailable = (int)$item->variant->stock;
                    $itemName = $item->variant->variant_name ?? $item->product->title; // Ambil nama
                }
                // Fallback: cek stok produk utama (jika tidak pakai varian)
                else if (!empty($item->product) && property_exists($item->product, 'stock')) {
                    $stockAvailable = (int)$item->product->stock;
                    $itemName = $item->product->title;
                }

                // Jika stok yang ada LEBIH KECIL dari yang diminta
                if ($stockAvailable < $quantityNeeded) {
                    $isStockSufficient = false; // Set flag jadi false
                    $outOfStockItems[] = $itemName . " (Stok: " . $stockAvailable . ", Diminta: " . $quantityNeeded . ")";
                    // Kita bisa 'break' di sini jika hanya butuh tahu "cukup atau tidak"
                    // Tapi jika kita lanjut, kita bisa kumpulkan semua item yg stoknya kurang
                }
            }
        }

        // 4. Kirim data hasil pengecekan ke view
        return view('backend.order.edit')
                    ->with('order', $order)
                    ->with('isStockSufficient', $isStockSufficient)
                    ->with('outOfStockItems', $outOfStockItems);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
        public function update(Request $request, $id)
        {
            $order = Order::findOrFail($id);

            $this->validate($request, [
                'status' => 'required|in:new,process,delivered,cancel',
                'payment_status' => 'required|in:paid,unpaid',
                'resi_number' => 'nullable|string|max:100',
                'jne_phone_verify' => 'nullable|string|max:10'
            ]);

            $oldStatus = $order->status;
            $oldPaymentStatus = $order->payment_status;

            $newStatus = $request->input('status');
            $newPaymentStatus = $request->input('payment_status');
            $paymentMethod = $order->payment_method;

            $shouldSendInvoice = false;

            DB::beginTransaction();

            try {

                $updateData = [
                    'status' => $newStatus,
                    'payment_status' => $newPaymentStatus,
                    'resi_number' => $request->input('resi_number'),
                    'jne_phone_verify' => $request->input('jne_phone_verify')
                ];

                // Jika delivered dan COD/manual_transfer → otomatis jadi paid
                if (
                    in_array($newStatus, ['delivered']) &&
                    in_array($paymentMethod, ['cod', 'manual_transfer'])
                ) {
                    $updateData['payment_status'] = 'paid';
                }

                $order->fill($updateData)->save();

                /*
                =========================================
                CEK PERUBAHAN PAYMENT STATUS
                =========================================
                */
                if ($oldPaymentStatus !== 'paid' && $order->payment_status === 'paid') {
                    $shouldSendInvoice = true;
                }

                /*
                =========================================
                LOGIKA PENGURANGAN STOK
                =========================================
                */
                if ($oldStatus !== 'delivered' && $newStatus === 'delivered') {

                    $order->loadMissing('cart.variant', 'cart.product');

                    foreach ($order->cart as $item) {

                        // Jika ada varian
                        if (!empty($item->variant_id) && $item->variant) {

                            if ($item->variant->stock < $item->quantity) {
                                throw ValidationException::withMessages([
                                    'stock' => "Stok varian '{$item->variant->variant_name}' tidak mencukupi."
                                ]);
                            }

                            $item->variant->decrement('stock', $item->quantity);

                        }
                        // Jika tidak pakai varian
                        elseif (!empty($item->product) && property_exists($item->product, 'stock')) {

                            if ($item->product->stock < $item->quantity) {
                                throw ValidationException::withMessages([
                                    'stock' => "Stok produk '{$item->product->title}' tidak mencukupi."
                                ]);
                            }

                            $item->product->decrement('stock', $item->quantity);
                        }
                    }
                }

                DB::commit();

                /*
                =========================================
                KIRIM INVOICE SETELAH COMMIT
                =========================================
                */
                if ($shouldSendInvoice) {
                    try {
                        Mail::to($order->email)->send(new OrderInvoiceMail($order));
                        \Log::info('Invoice sent for order ' . $order->order_number);
                    } catch (\Exception $e) {
                        \Log::error('Invoice sending failed: ' . $e->getMessage());
                    }
                }

                request()->session()->flash('success', 'Successfully updated order');
                return redirect()->route('order.index');

            } catch (\Throwable $e) {

                DB::rollBack();

                request()->session()->flash('error', 'Error while updating order: ' . $e->getMessage());
                return redirect()->route('order.index');
            }
        }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order=Order::find($id);
        if($order){
            $status=$order->delete();
            if($status){
                request()->session()->flash('success','Order Successfully deleted');
            }
            else{
                request()->session()->flash('error','Order can not deleted');
            }
            return redirect()->route('order.index');
        }
        else{
            request()->session()->flash('error','Order can not found');
            return redirect()->back();
        }
    }

    public function orderTrack(){
        return view('frontend.pages.order-track');
    }

    public function productTrackOrder(Request $request){
        // return $request->all();
        $order=Order::where('user_id',auth()->user()->id)->where('order_number',$request->order_number)->first();
        if($order){
            if($order->status=="new"){
            request()->session()->flash('success','Your order has been placed. please wait.');
            return redirect()->route('home');

            }
            elseif($order->status=="process"){
                request()->session()->flash('success','Your order is under processing please wait.');
                return redirect()->route('home');

            }
            elseif($order->status=="delivered"){
                request()->session()->flash('success','Your order is successfully delivered.');
                return redirect()->route('home');

            }
            else{
                request()->session()->flash('error','Your order canceled. please try again');
                return redirect()->route('home');

            }
        }
        else{
            request()->session()->flash('error','Invalid order numer please try again');
            return back();
        }
    }

    // PDF generate
    public function pdf(Request $request){
        $order=Order::getAllOrder($request->id);
        // return $order;
        $file_name=$order->order_number.'-'.$order->first_name.'.pdf';
        // return $file_name;
        $pdf=PDF::loadview('backend.order.pdf',compact('order'));
        return $pdf->download($file_name);
    }
    // Income chart
    public function incomeChart(Request $request){
        $year=\Carbon\Carbon::now()->year;
        // dd($year);
        $items=Order::with(['cart_info'])->whereYear('created_at',$year)->where('status','delivered')->get()
            ->groupBy(function($d){
                return \Carbon\Carbon::parse($d->created_at)->format('m');
            });
            // dd($items);
        $result=[];
        foreach($items as $month=>$item_collections){
            foreach($item_collections as $item){
                $amount=$item->cart_info->sum('amount');
                // dd($amount);
                $m=intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount :$result[$m]=$amount;
            }
        }
        $data=[];
        for($i=1; $i <=12; $i++){
            $monthName=date('F', mktime(0,0,0,$i,1));
            $data[$monthName] = (!empty($result[$i]))? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        return $data;
    }

    public function cancelUnpaidOrder(Request $request)
{
    $request->validate([
        'order_number' => 'required|string',
    ]);

    // Cari pesanan berdasarkan nomor order, milik user yang sedang login, dan statusnya belum dibayar
    $order = Order::where('order_number', $request->order_number)
                  ->where('user_id', auth()->id())
                  ->where('payment_status', 'unpaid')
                  ->first();

    if ($order) {
        DB::beginTransaction();
        try {
            // 1. Ubah status pesanan menjadi 'cancel'
            $order->status = 'cancel';
            $order->save();

            // 2. Kembalikan item yang terikat pada pesanan ini ke keranjang
            // dengan cara meng-update order_id menjadi null
            Cart::where('order_id', $order->id)
                ->where('user_id', auth()->id())
                ->update(['order_id' => null]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Pesanan berhasil dibatalkan.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membatalkan pesanan: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal membatalkan pesanan.'], 500);
        }
    }

    // Jika pesanan tidak ditemukan atau sudah dibayar, kirim respons error
    return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan atau sudah diproses.'], 404);
}
}
