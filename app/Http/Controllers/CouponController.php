<?php

namespace App\Http\Controllers;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CouponController extends Controller{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupon=Coupon::orderBy('id','DESC')->paginate('10');
        return view('backend.coupon.index')->with('coupons',$coupon);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        return view('backend.coupon.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return $request->all();
    //     $this->validate($request,[
    //         'code'           => ['required','string','max:100'],
    //         'type'           => ['required','in:fixed,percent'],
    //         'value'          => ['required','numeric','min:0'],
    //         'starts_at'     => ['required','date'],
    //         'expires_at'       => ['required','date','after_or_equal:starts_at'],
    //         'usage_limit'    => ['nullable','integer','min:0'],
    //         'per_user_limit' => ['nullable','integer','min:0'],
    //         'times_used'    => ['nullable','integer','min:0'],
    //         'min_spend'      => ['nullable','numeric','min:0'],
    //         'max_discount'   => ['nullable','numeric','min:0'],
    //         'status'         => ['required','in:active,inactive']

    //     ], [
    //     'code.unique' => 'Kode voucher sudah tersedia.', // << pesan khusus kode unik
    // ]);
    //     $data=$request->all();
    //     $status=Coupon::create($data);
    //     if($status){
    //         request()->session()->flash('success','Coupon Successfully added');
    //     }
    //     else{
    //         request()->session()->flash('error','Please try again!!');
    //     }
    //     return redirect()->route('coupon.index');
    // }
        $data = $request->validate([
        'code'           => ['required','string','max:100', Rule::unique('coupons','code')],
        'type'           => ['required','in:fixed,percent'],
        'value'          => ['required','numeric','min:0'],
        'starts_at'      => ['required','date'],
        'expires_at'     => ['required','date','after_or_equal:starts_at'],
        'usage_limit'    => ['nullable','integer','min:0'],
        'per_user_limit' => ['nullable','integer','min:0'],
        'min_spend'      => ['nullable','numeric','min:0'],
        'max_discount'   => ['nullable','numeric','min:0'],
        'status'         => ['required','in:active,inactive'],
        // kalau ada checkbox:
        // 'free_shipping'  => ['nullable','boolean'],
    ], [
        'code.unique' => 'Kode voucher sudah tersedia.',
    ]);

    // Normalisasi checkbox (kalau ada)
    // $data['free_shipping'] = $request->boolean('free_shipping');

    // Jangan ambil dari form: set default aman
    $data['times_used'] = 0;

    try {
        Coupon::create($data);
    } catch (QueryException $e) {
        // Guard tambahan jika ada race-condition
        if ($e->getCode() === '23000' && Str::contains($e->getMessage(), 'coupons_code_unique')) {
            return back()->withInput()->withErrors(['code' => 'Kode voucher sudah tersedia.']);
        }
        throw $e;
    }

    return redirect()->route('coupon.index')->with('success','Coupon berhasil dibuat.');}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon=Coupon::find($id);
        if($coupon){
            return view('backend.coupon.edit')->with('coupon',$coupon);
        }
        else{
            return view('backend.coupon.index')->with('error','Coupon not found');
        }
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
    $coupon=Coupon::find($id);
    $data = $request->validate([
        'code'           => ['required','string','max:100', Rule::unique('coupons','code')->ignore($coupon->id)],
        'type'           => ['required','in:fixed,percent'],
        'value'          => ['required','numeric','min:0'],
        'starts_at'      => ['required','date'],
        'expires_at'     => ['required','date','after_or_equal:starts_at'],
        'usage_limit'    => ['nullable','integer','min:0'],
        'per_user_limit' => ['nullable','integer','min:0'],
        'min_spend'      => ['nullable','numeric','min:0'],
        'max_discount'   => ['nullable','numeric','min:0'],
        'status'         => ['required','in:active,inactive'],
        // 'applies_to'     => ['nullable','in:all,products,categories,exclude_products,exclude_categories'],
        // 'free_shipping'  => ['nullable','boolean'],
    ], [
        'code.unique' => 'Kode voucher sudah tersedia.',
    ]);

    // boolean checkbox (jika dipakai)
    // $data['free_shipping'] = $request->boolean('free_shipping');

    try {
        $coupon->update($data);
    } catch (QueryException $e) {
        if ($e->getCode()==='23000' && Str::contains($e->getMessage(),'coupons_code_unique')) {
            return back()->withInput()->withErrors(['code' => 'Kode voucher sudah tersedia.']);
        }
        throw $e;
    }

    return redirect()->route('coupon.index')->with('success','Perubahan kupon disimpan.');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon=Coupon::find($id);
        if($coupon){
            $status=$coupon->delete();
            if($status){
                request()->session()->flash('success','Coupon successfully deleted');
            }
            else{
                request()->session()->flash('error','Error, Please try again');
            }
            return redirect()->route('coupon.index');
        }
        else{
            request()->session()->flash('error','Coupon not found');
            return redirect()->back();
        }
    }

    public function couponStore(Request $request){
        // return $request->all();
        $coupon=Coupon::where('code',$request->code)->first();
        // dd($coupon);
        if(!$coupon){
            request()->session()->flash('error','Invalid coupon code, Please try again');
            return back();
        }
        if($coupon){
            $total_price=Cart::where('user_id',auth()->user()->id)->where('order_id',null)->sum('price');
            // dd($total_price);
            session()->put('coupon',[
                'id'=>$coupon->id,
                'code'=>$coupon->code,
                'value'=>$coupon->discount($total_price)
            ]);
            request()->session()->flash('success','Coupon successfully applied');
            return redirect()->back();
        }
    }

    public function apply(Request $request){
    $request->validate(['code' => 'required|string']);

    $userId = auth()->id();
    if (!$userId) {
        return back()->with('error', 'Silakan login terlebih dahulu.');
    }

    $code = strtoupper(trim($request->code));

    // cari robust terhadap spasi/kapital
    $coupon = \App\Models\Coupon::whereRaw('UPPER(TRIM(code)) = ?', [$code])->first();
    if (!$coupon) {
        return back()->with('error', 'Kode kupon tidak ditemukan.');
    }

    // cek aktif + masa berlaku saja di tahap APPLY
    if (! $coupon->isUsableNow()) {
        return back()->with('error', 'Kupon tidak aktif atau kadaluarsa.');
    }

    // SIMPAN HANYA id & code (bukan value)
    session()->put('coupon', ['id' => $coupon->id, 'code' => $coupon->code]);

    return back()->with('success', 'Kupon diterapkan. Diskon akan dihitung saat checkout.');}

        public function remove(){
            session()->forget('coupon');
            return back()->with('success','Kupon dihapus.');
        }

}
