@extends('frontend.layouts.master')

@section('title', 'Checkout page')

@section('main-content')


    {{-- Format Rupiah --}}
    @php
        $formatRp = fn($n) => 'Rp' . number_format($n, 0, ',', '.');
    @endphp
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Home<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="javascript:void(0)">Checkout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <!-- Start Checkout -->
    <section class="shop checkout section">
        <div class="container">
            <h2 class="mb-3">Checkout</h2>
            <form class="form" id="checkout-form">
                @csrf
                <input type="hidden" name="address_option" id="address_option"
                    value="{{ auth()->check() && optional(auth()->user()->defaultAddress)->id ? 'existing' : 'new' }}">

                <input type="hidden" name="address_id" id="address_id"
                    value="{{ auth()->check() ? optional(auth()->user()->defaultAddress)->id : '' }}">

                <div class="row">
                    <div class="col-lg-8 col-12">
                        @php
                            $addresses = auth()->check() ? auth()->user()->addresses()->latest()->get() : collect();
                            $defaultId = auth()->check() ? optional(auth()->user()->defaultAddress)->id : null;
                        @endphp

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>Alamat Pengiriman</strong>
                                @auth
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnManageAddress">Kelola
                                        Alamat</button>
                                @endauth
                            </div>
                            <div class="card-body">
                                @if (auth()->check() && $addresses->count())
                                    @foreach ($addresses as $addr)
                                        <label class="d-flex"
                                            style="gap:.5rem;align-items:flex-start;margin-bottom:.25rem;">
                                            <input type="radio" name="address_option" id="addr-{{ $addr->id }}"
                                                value="existing" data-id="{{ $addr->id }}"
                                                data-province-id="{{ $addr->province_id }}"
                                                data-province-name="{{ $addr->province_name }}"
                                                data-city-id="{{ $addr->city_id }}" data-city-name="{{ $addr->city_name }}"
                                                data-district-id="{{ $addr->district_id }}"
                                                data-district-name="{{ $addr->district_name }}"
                                                data-post-code="{{ $addr->post_code }}"
                                                data-address1="{{ e($addr->address1) }}"
                                                {{ ($defaultId == $addr->id && !old('address_option')) || (old('address_option') === 'existing' && old('address_id') == $addr->id) ? 'checked' : '' }}>
                                            <span>
                                                <b>{{ $addr->label ?: 'Tanpa Label' }}</b> — {{ $addr->first_name }}
                                                {{ $addr->last_name }}, {{ $addr->phone }}<br>
                                                {{ $addr->address1 }}, {{ $addr->sub_district_name }},
                                                {{ $addr->district_name }},
                                                {{ $addr->city_name }},
                                                {{ $addr->province_name }},
                                                {{ $addr->post_code }}.
                                                @if ($addr->is_default)
                                                    <span class="badge bg-success text-white p-1">Default</span>
                                                @endif
                                            </span>
                                        </label>
                                        @if ($defaultId == $addr->id)
                                            <input type="hidden" name="address_id" value="{{ $addr->id }}">
                                        @endif
                                        <div style="border-bottom:1px solid #eee;margin:.25rem 0 .5rem 1.6rem;"></div>
                                    @endforeach
                                @else
                                    <div class="alert alert-sticky alert-info mb-0">Belum ada alamat. Klik <b>Kelola
                                            Alamat</b> untuk
                                        menambahkan.</div>
                                @endif
                            </div>
                        </div>

                        <div class="checkout-form">
                            <p>Silakan isi data ini untuk konfirmasi & nota. (Alamat lengkap dikelola via tombol di atas)
                            </p>
                            {{-- <div class="row">
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>First Name<span>*</span></label>
                                        <input type="text" name="first_name" required
                                            value="{{ old('first_name', auth()->user()->first_name ?? '') }}">
                                        @error('first_name')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Last Name<span>*</span></label>
                                        <input type="text" name="last_name" required
                                            value="{{ old('last_name', auth()->user()->last_name ?? '') }}">
                                        @error('last_name')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Email Address<span>*</span></label>
                                        <input type="email" name="email" required
                                            value="{{ old('email', auth()->user()->email ?? '') }}">
                                        @error('email')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-12">
                                    <div class="form-group">
                                        <label>Phone Number <span>*</span></label>
                                        <input type="text" name="phone" required
                                            value="{{ old('phone', auth()->user()->phone ?? '') }}">
                                        @error('phone')
                                            <span class='text-danger'>{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <input type="hidden" name="province_id" id="province_id_fallback">
                                <input type="hidden" name="province_name" id="province_name_input">
                                <input type="hidden" name="city_id" id="city_id_fallback">
                                <input type="hidden" name="city_name" id="city_name_input">
                                <input type="hidden" name="post_code" id="post_code_fallback">
                                <input type="hidden" name="address1" id="address1_fallback">
                            </div> --}}
                        </div>
                    </div>

                    <div class="col-lg-4 col-12">
                        <div class="order-details">

                            <!-- Order Widget -->
                            <div class="single-widget">
                                <h2>TOTAL KERANJANG</h2>
                                <div class="content">
                                    @php
                                        $cartItems = \App\Models\Cart::with(['product', 'variant'])
                                            ->where('user_id', auth()->id())
                                            ->whereNull('order_id')
                                            ->where('is_checked', true)
                                            ->get();
                                        $subTotal = (string) $cartItems->sum('amount');
                                        $totalWeightGrams = (int) $cartItems->sum('line_weight_gram');

                                        $couponVal = '0.00';
                                        if (session()->has('coupon')) {
                                            $c = \App\Models\Coupon::find(session('coupon.id'));
                                            if ($c) {
                                                $eligibleSubtotal = (string) $c->eligibleSubtotal($cartItems);
                                                $couponVal = $c->calculateDiscount($eligibleSubtotal);
                                            }
                                        }
                                        $total_amount = bcsub($subTotal, $couponVal, 2);
                                    @endphp
                                    <ul>
                                        <li class="order_subtotal" data-price="{{ $subTotal }}">
                                            Subtotal Keranjang
                                            <span>{{ $formatRp($subTotal) }}</span>
                                        </li>
                                        <li id="total-weight" data-weight-grams="{{ $totalWeightGrams }}">
                                            Total Berat
                                            @if ($totalWeightGrams < 1000)
                                                <span>{{ $totalWeightGrams }} gr</span>
                                            @else
                                                <span>{{ number_format($totalWeightGrams / 1000, 2, ',', '.') }} kg</span>
                                            @endif
                                        </li>
                                        <li class="shipping">
                                            Pilih Kurir
                                            <select name="courier" id="courier" class="nice-select" disabled>
                                                <option value="">Pilih Kurir</option>
                                                <option value="jne">JNE</option>
                                                <option value="jnt">J&T</option>
                                            </select>

                                            <div id="shipping-options" class="mt-3 p-3"></div>
                                            <input type="hidden" name="shipping_service" id="shipping_service_input">
                                            <input type="hidden" name="shipping_cost" id="shipping_cost_input">
                                            <input type="hidden" name="shipping_courier" id="shipping_courier_input">
                                        </li>
                                        {{-- <div id="shipping-options" class="mt-3 p-3"></div>
                                        <input type="hidden" name="shipping_service" id="shipping_service_input">
                                        <input type="hidden" name="shipping_cost" id="shipping_cost_input">
                                        <input type="hidden" name="shipping_courier" id="shipping_courier_input"> --}}
                                        @php $__coupon = session('coupon'); @endphp
                                        <li class="coupon-row">
                                            <span class="coupon-label">Coupon</span>
                                            @if (empty($__coupon))
                                                <div class="coupon-inline">
                                                    <input type="text" name="code" class="form-control coupon-input"
                                                        placeholder="Masukkan kode kupon">
                                                    <button type="submit" class="btn btn-dark coupon-btn"
                                                        formaction="{{ route('coupon.apply') }}" formmethod="POST"
                                                        formnovalidate>GUNAKAN</button>
                                                </div>
                                            @else
                                                <div class="coupon-inline">
                                                    <input type="text" class="form-control coupon-input"
                                                        value="{{ $__coupon['code'] ?? '' }}" readonly>
                                                    <button type="submit" class="btn btn-dark coupon-btn"
                                                        formaction="{{ route('coupon.remove') }}" formmethod="POST"
                                                        formnovalidate>HAPUS</button>
                                                </div>
                                            @endif
                                        </li>
                                        @if (session()->has('coupon'))
                                            <li class="coupon_price" data-price="{{ $couponVal }}">
                                                You Save
                                                <span>{{ $formatRp($couponVal) }}</span>
                                            </li>
                                        @endif
                                        <li class="last" id="order_total_price">
                                            Total
                                            <span>{{ $formatRp($total_amount) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!--/ End Order Widget -->

                            <!--/ End Order Widget -->
                            <div class="single-widget">
                                <h2>Payments</h2>
                                <div class="content">
                                    <div class="checkbox">
                                        <input name="payment_method" type="radio" value="cod" id="payment-cod"
                                            required>
                                        <label for="payment-cod"> Ambil di Tempat</label><br>
                                        <input name="payment_method" type="radio" value="midtrans"
                                            id="payment-midtrans" required>
                                        <label for="payment-midtrans"> Online Payment</label><br>
                                        <input name="payment_method" type="radio" value="manual_transfer"
                                            id="payment-manual" required>
                                        <label for="payment-manual">Via bank manual </label><br>

                                    </div>
                                </div>
                            </div>
                            <!--/ End Order Widget -->

                            <!-- Payment Method Widget -->
                            <div class="single-widget payement">
                                <div class="content">
                                    <img src="{{ asset('backend/img/payment-method.png') }}" alt="#">
                                </div>
                            </div>
                            <!--/ End Payment Method Widget -->

                            <!-- Button Widget -->
                            <div class="single-widget get-button">
                                <div class="content">
                                    <div class="button">
                                        <button type="button" class="btn" id="place-order-button">Pesan
                                            sekarang</button>
                                    </div>
                                </div>
                            </div>
                            <!--/ End Button Widget -->
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    @include('frontend.layouts.bank_transfer_modal_checkout')
    @include('frontend.layouts.cod_modal_checkout')
    <!--/ End Checkout -->

    <div id="addrModalOverlay">
        <div class="addr-modal-panel">
            <div class="addr-modal-header">
                Kelola Alamat
                <button type="button" class="close-btn" onclick="hideAddrModal()">×</button>
            </div>
            <div class="addr-modal-body">
                {{-- LIST --}}
                <div id="addrListView">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Alamat Tersimpan</h6>
                        <button class="btn btn-sm btn-primary" id="btnAddAddress">+ Tambah Alamat</button>
                    </div>

                    @php $addresses = auth()->check() ? auth()->user()->addresses()->latest()->get() : collect(); @endphp
                    @if ($addresses->isEmpty())
                        <div class="alert alert-info mb-0">Belum ada alamat. Klik <b>Tambah Alamat</b> untuk menambahkan.
                        </div>
                    @else
                        <div class="list-group">
                            @foreach ($addresses as $a)
                                <div class="list-group-item"
                                    style="border:1px solid #eee;border-radius:6px;margin-bottom:10px;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="mb-1">
                                                <strong>{{ $a->label ?: 'Tanpa Label' }}</strong>
                                                @if ($a->is_default)
                                                    <span class="badge bg-success text-light p-1 ms-1">Default</span>
                                                @endif
                                            </div>
                                            <div style="line-height:1.5;">
                                                {{ $a->first_name }} {{ $a->last_name }} — {{ $a->phone }}<br>
                                                {{ $a->address1 }}<br> {{ $a->sub_district_name }},
                                                {{ $a->district_name }}, {{ $a->city_name }}, {{ $a->province_name }}
                                                {{ $a->post_code }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap" style="gap:.5rem;">
                                            <a href="#" class="btn btn-sm btn-outline-secondary addr-edit"
                                                data-id="{{ Crypt::encryptString($a->id) }}"
                                                data-label="{{ $a->label }}" data-first-name="{{ $a->first_name }}"
                                                data-last-name="{{ $a->last_name }}" data-email="{{ $a->email }}"
                                                data-phone="{{ $a->phone }}"
                                                data-province-id="{{ $a->province_id }}"
                                                data-province-name="{{ $a->province_name }}"
                                                data-city-id="{{ $a->city_id }}" data-city-name="{{ $a->city_name }}"
                                                data-district-id="{{ $a->district_id }}"
                                                data-district-name="{{ $a->district_name }}"
                                                data-sub-district-id="{{ $a->sub_district_id }}"
                                                data-sub-district-name="{{ $a->sub_district_name }}"
                                                data-post-code="{{ $a->post_code }}"
                                                data-address1="{{ e($a->address1) }}">Ubah</a>

                                            {{-- @unless ($a->is_default)
                                                <a href="#" class="btn btn-sm btn-outline-success addr-default"
                                                    data-id="{{Crypt::encryptString($a->id)}}">Jadikan Default</a>
                                            @endunless --}}

                                            <a href="#" class="btn btn-sm btn-outline-danger addr-delete"
                                                data-id="{{ Crypt::encryptString($a->id) }}">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- FORM --}}
                <div id="addrFormView" style="display:none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 id="addrFormTitle" class="mb-0">Tambah Alamat</h6>
                        <button class="btn btn-sm btn-dark" id="btnBackList">&larr; KEMBALI</button>
                    </div>

                    <form id="addressForm">@csrf
                        <input type="hidden" name="_method" id="addr_method" value="POST">
                        <input type="hidden" name="address_id" id="addr_id">

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Label</label>
                                <input type="text" class="form-control" name="label" id="addr_label"
                                    placeholder="Rumah / Kantor">
                            </div>

                            {{-- Kolom Checkbox yang sudah diperbaiki --}}
                            <div class="col-sm-6 checkbox-col">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" name="set_default"
                                        id="addr_set_default" value="1">
                                    <span>Jadikan Default</span>
                                </label>
                            </div>

                            {{-- <div class="col-sm-6">
                                <label class="form-label">First Name*</label>
                                <input type="text" class="form-control" name="first_name" id="addr_first_name"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Last Name*</label>
                                <input type="text" class="form-control" name="last_name" id="addr_last_name"
                                    required>
                            </div> --}}

                            <div class="col-sm-12">
                                <label class="form-label">Nama Penerima</label>
                                <input type="text" class="form-control" name="name" id="addr_name"
                                    value="{{ auth()->user()->name }}" readonly>
                                <small class="text-muted">Nama penerima mengikuti nama pada akun Anda.</small>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="addr_email"
                                    value="{{ auth()->user()->email }}" readonly>
                                <small class="text-muted">Email mengikuti data akun Anda.</small>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Phone*</label>
                                <input type="text" class="form-control" name="phone" id="addr_phone" required>
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label">Provinsi*</label>
                                {{-- Menghapus .form-control dari select --}}
                                <select class="nice-select" name="province_id" id="addr_province_id" required>
                                    <option value="">Pilih Provinsi</option>
                                </select>
                                <input type="hidden" name="province_name" id="addr_province_name">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Kota/Kabupaten*</label>
                                {{-- Menghapus .form-control dari select --}}
                                <select class="nice-select" name="city_id" id="addr_city_id" required disabled>
                                    <option value="">Pilih Kota/Kabupaten</option>
                                </select>
                                <input type="hidden" name="city_name" id="addr_city_name">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label">Kecamatan*</label>
                                <select class="nice-select" name="district_id" id="addr_district_id" required disabled>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                                <input type="hidden" name="district_name" id="addr_district_name">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label">Kelurahan / Desa*</label>
                                <select class="nice-select" name="sub_district_id" id="addr_sub_district_id" required
                                    disabled>
                                    <option value="">Pilih Kelurahan/Desa</option>
                                </select>
                                <input type="hidden" name="sub_district_name" id="addr_sub_district_name">
                            </div>

                            <div class="col-sm-6">
                                <label class="form-label">Kode Pos*</label>
                                <input type="text" class="form-control" name="post_code" id="addr_post_code" readonly
                                    required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Lengkap*</label>
                                <textarea class="form-control" name="address1" id="addr_address1" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="submit" class="btn btn-primary" id="addrSubmit" disabled>Lengkapi
                                Data</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="addr-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hideAddrModal()">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .swal2-container {
            z-index: 1060 !important;
        }

        li.shipping {
            display: inline-flex;
            width: 100%;
            font-size: 14px;
        }

        .single-widget .content ul li.coupon-row {
            display: block;
            width: 100%;
            padding: 8px 0;
            border-top: 1px solid #f1f1f1;
        }

        .coupon-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            text-align: center;
        }

        .coupon-inline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            width: 100%;
        }

        .coupon-input {
            max-width: 260px;
            width: 100%;
            height: 36px;
        }

        .coupon-btn {
            height: 36px;
            line-height: 1;
            padding: 0 14px;
            white-space: nowrap;
        }

        .coupon-remove {
            height: 36px;
            line-height: 36px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.show {
            display: flex;
            /* Tampilkan saat class 'show' ditambahkan */
            opacity: 1;
            /* Munculkan (fade in) */
        }

        .modal-box {
            background-color: #fff;
            border-radius: 8px;
            /* Sudut sedikit melengkung */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            /* Lebar maksimum */
            width: 90%;
            /* Lebar responsif */
            max-height: 90vh;
            /* Tinggi maksimum */
            overflow-y: auto;
            /* Scroll jika konten panjang */
            display: flex;
            /* Gunakan flexbox untuk layout internal */
            flex-direction: column;
            /* Susun header, body, footer secara vertikal */
            transform: scale(0.95);
            /* Mulai sedikit kecil untuk animasi zoom */
            transition: transform 0.3s ease;
            /* Animasi zoom */
            transform-origin: center center;
        }

        .modal-overlay.show .modal-box {
            transform: scale(1);
            /* Kembali ke ukuran normal saat muncul */
        }

        .modal-header-custom {
            display: flex;
            justify-content: space-between;
            /* Judul kiri, tombol close kanan */
            align-items: center;
            padding: 1rem 1.5rem;
            /* Padding header */
            border-bottom: 1px solid #dee2e6;
            /* Garis pemisah */
        }

        .modal-title-custom {
            margin-bottom: 0;
            font-size: 1.25rem;
            /* Ukuran judul */
            font-weight: 500;
            line-height: 1.5;
        }

        .modal-close-btn {
            /* Style untuk tombol 'x' dan 'Tutup' */
            background: transparent;
            border: none;
            font-size: 1.8rem;
            /* Ukuran icon 'x' */
            font-weight: 700;
            line-height: 1;
            color: #000;
            opacity: 0.5;
            cursor: pointer;
            padding: 0.5rem;
            /* Area klik lebih besar */
            margin: -0.5rem -0.5rem -0.5rem auto;
            /* Posisi 'x' di kanan atas */
        }

        .modal-close-btn:hover {
            opacity: 0.8;
        }

        .modal-footer-custom .modal-close-btn {
            /* Style khusus tombol 'Tutup' di footer */
            font-size: 1rem;
            /* Ukuran teks normal */
            font-weight: 400;
            opacity: 1;
            margin: 0;
            /* Reset margin */
            color: #fff;
            background-color: #6c757d;
            /* Warna abu Bootstrap */
            border-color: #6c757d;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }

        .modal-footer-custom .modal-close-btn:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .modal-body-custom {
            padding: 1.5rem;
            /* Padding konten */
            line-height: 1.6;
            /* Spasi antar baris */
            flex-grow: 1;
            /* Body mengisi ruang sisa jika konten pendek */
        }

        .modal-body-custom .bank-account {
            background-color: #f8f9fa;
            /* Background abu muda */
            border-radius: 5px;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #eee;
            /* Tambah border tipis */
        }

        .modal-body-custom .bank-account strong {
            display: inline-block;
            margin-bottom: 0.25rem;
        }

        .modal-body-custom hr {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .modal-body-custom .wa-confirm-button {
            /* Tombol WhatsApp */
            background-color: #25D366 !important;
            border-color: #25D366 !important;
            color: white !important;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            border-radius: 0.3rem;
            text-align: center;
            display: block;
            text-decoration: none;
            margin-top: 1rem;
            /* Jarak dari teks PENTING */
        }

        .modal-body-custom .wa-confirm-button i {
            margin-right: 0.5rem;
        }

        .modal-footer-custom {
            display: flex;
            justify-content: flex-end;
            /* Tombol ke kanan */
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
            /* Garis pemisah */
            background-color: #f8f9fa;
            /* Background footer abu muda */
            border-radius: 0 0 8px 8px;
            /* Sudut bawah melengkung */
        }


        @media (max-width:576px) {
            .coupon-inline {
                flex-direction: column;
                align-items: stretch;
            }

            .coupon-btn {
                width: 100%;
            }
        }


        /* Untuk pop up si alamat */
        /* Overlay full-screen */
        #addrModalOverlay {
            position: fixed;
            inset: 0;
            /* top:0; right:0; bottom:0; left:0 */
            background: rgba(0, 0, 0, .55);
            display: none;
            /* default: sembunyikan */
            align-items: center;
            /* center vertikal */
            justify-content: center;
            /* center horizontal */
            z-index: 1050;
            padding: 20px;
            /* beri ruang di sekeliling pada layar kecil */
            height: 100vh;
            /* pastikan overlay menutupi penuh viewport */
        }

        /* Saat ditampilkan */
        #addrModalOverlay.show {
            display: flex;
        }

        #addrModalOverlay .addr-modal-header .close-btn {
            position: absolute;
            top: 14px;
            right: 18px;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 2rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            color: #000 !important;
            text-shadow: 0 1px 0 #fff;
            opacity: 0.5;
            text-transform: none !important;
            width: auto;
        }

        #addrModalOverlay .addr-modal-header .close-btn:hover {
            opacity: 0.8;
            color: #000 !important;
            background: transparent !important;
        }

        #addrModalOverlay #btnAddAddress {
            padding: 0.25rem 0.65rem !important;
            /* Ukuran .btn-sm */
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            text-transform: none !important;
            color: #fff !important;
            background-color: #007bff !important;
            /* btn-primary */
            border-color: #007bff !important;
        }

        #addrModalOverlay #btnAddAddress:hover {
            background-color: #0069d9 !important;
            border-color: #0062cc !important;
        }

        #addrModalOverlay .list-group-item .btn {
            padding: 0.25rem 0.65rem !important;
            /* Ukuran .btn-sm */
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            background-color: transparent !important;
            text-transform: none !important;
            width: auto;
            margin-bottom: 0;
        }

        #addrModalOverlay .list-group-item .btn.btn-outline-secondary {
            color: #6c757d !important;
            border-color: #6c757d !important;
        }

        #addrModalOverlay .list-group-item .btn.btn-outline-secondary:hover {
            color: #fff !important;
            background-color: #6c757d !important;
        }

        /* Warna btn-outline-success */
        #addrModalOverlay .list-group-item .btn.btn-outline-success {
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        #addrModalOverlay .list-group-item .btn.btn-outline-success:hover {
            color: #fff !important;
            background-color: #28a745 !important;
        }

        /* Warna btn-outline-danger */
        #addrModalOverlay .list-group-item .btn.btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        #addrModalOverlay .list-group-item .btn.btn-outline-danger:hover {
            color: #fff !important;
            background-color: #dc3545 !important;
        }

        #addrModalOverlay #addrSubmit {
            padding: 0.375rem 0.75rem !important;
            /* Ukuran .btn */
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            text-transform: none !important;
            color: #fff !important;
            background-color: #007bff !important;
            /* btn-primary */
            border-color: #007bff !important;
        }

        #addrModalOverlay #addrSubmit:hover {
            background-color: #0069d9 !important;
            border-color: #0062cc !important;
        }

        #addrModalOverlay #addrCancel {
            padding: 0.375rem 0.75rem !important;
            /* Ukuran .btn */
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            text-transform: none !important;
            color: #212529 !important;
            background-color: #f8f9fa !important;
            /* btn-light */
            border-color: #f8f9fa !important;
        }

        #addrModalOverlay .addr-modal-footer .btn {
            padding: 0.375rem 0.75rem !important;
            /* Ukuran .btn */
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
            text-transform: none !important;
            color: #fff !important;
            background-color: #6c757d !important;
            /* btn-secondary */
            border-color: #6c757d !important;
        }

        #addrModalOverlay .addr-modal-footer .btn:hover {
            color: #fff !important;
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
        }

        #addrModalOverlay #addrCancel:hover {
            color: #212529 !important;
            background-color: #e2e6ea !important;
            border-color: #dae0e5 !important;
        }

        /* Panel / kotak modal */
        .addr-modal-panel {
            width: min(720px, 92vw);
            max-height: 90vh;
            overflow: auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
            transform: scale(.98);
            animation: addr-pop .18s ease forwards;
            /* pastikan panel terpusat dalam overlay */
            margin: auto;
            transform-origin: center center;
        }

        .addr-modal-body .form-control {
            height: 45px;
        }

        /* 2. Styling untuk .nice-select di dalam modal */
        .addr-modal-body .nice-select {
            width: 100%;
            /* Lebar penuh di dalam kolom */
            height: 45px;
            /* Samakan tinggi dengan .form-control */
            line-height: 43px;
            /* Rata tengah vertikal teks */
            border: 1px solid #ced4da;
            /* Border ala Bootstrap */
            border-radius: 0.25rem;
            /* Sudut ala Bootstrap */
            background-color: #fff;
            float: none;
            /* Hapus float default dari nice-select */
            font-size: 14px;
            /* Samakan font-size */
        }

        /* 3. Teks yang terpilih di nice-select */
        .addr-modal-body .nice-select .current {
            color: #212529;
            /* Warna teks input Bootstrap */
            font-size: 14px;
        }

        /* 4. Panah dropdown */
        .addr-modal-body .nice-select::after {
            border-bottom: 2px solid #555;
            border-right: 2px solid #555;
            height: 8px;
            width: 8px;
            right: 15px;
            margin-top: -5px;
        }

        /* 5. Daftar dropdown */
        .addr-modal-body .nice-select .list {
            width: 100%;
            border-radius: 0.25rem;

            /* ▼▼▼ TAMBAHKAN DUA BARIS INI ▼▼▼ */
            max-height: 250px;
            /* Batasi tinggi list-nya, misal 250px */
            overflow-y: auto;
            /* Beri scrollbar HANYA PADA LIST ini */
            /* ▲▲▲ BATAS AKHIR KODE TAMBAHAN ▲▲▲ */
        }

        /* 6. Perataan kolom checkbox "Jadikan Default" */
        .addr-modal-body .checkbox-col {
            display: flex;
            align-items: flex-end;
            /* Dorong item ke bawah */
            padding-bottom: 0.4rem;
            /* Sesuaikan agar rata dengan bawah input */
        }

        .addr-modal-body .checkbox-col .form-check {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding-left: 0;
            /* Hapus padding default .form-check */
        }

        .addr-modal-body .checkbox-col .form-check-input {
            margin-top: 0;
            /* Reset margin */
        }

        /* 7. Tombol "Kembali" agar lebih rapi */
        #btnBackList {
            font-weight: 500;
            font-size: 13px;
            padding: 0.5rem 0.8rem;
        }

        @keyframes addr-pop {
            to {
                transform: scale(1);
            }
        }

        /* Header / body / footer opsional */
        .addr-modal-header {
            padding: 14px 18px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .addr-modal-body {
            padding: 16px 18px;
        }

        .addr-modal-footer {
            padding: 12px 18px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: .5rem;
        }

        .addr-modal-body .list-group-item .btn {
            padding: 0.25rem 0.65rem !important;
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            background-color: transparent !important;
            text-transform: none !important;
            width: auto;
            margin-bottom: 0;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-secondary {
            color: #6c757d !important;
            border-color: #6c757d !important;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-secondary:hover {
            color: #fff !important;
            background-color: #6c757d !important;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-success {
            color: #28a745 !important;
            border-color: #28a745 !important;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-success:hover {
            color: #fff !important;
            background-color: #28a745 !important;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-danger {
            color: #dc3545 !important;
            border-color: #dc3545 !important;
        }

        .addr-modal-body .list-group-item .btn.btn-outline-danger:hover {
            color: #fff !important;
            background-color: #dc3545 !important;
        }



        .addr-modal-body #btnAddAddress {
            padding: 0.25rem 0.65rem !important;
            /* Ukuran .btn-sm Bootstrap 4 */
            font-size: 0.875rem !important;
            line-height: 1.5 !important;
            text-transform: none !important;

            /* Memastikan style btn-primary (solid) tetap jalan */
            color: #fff !important;
            background-color: #007bff !important;
            /* Warna primary Bootstrap 4 */
            border-color: #007bff !important;
        }

        .addr-modal-body #btnAddAddress:hover {
            background-color: #0069d9 !important;
            /* Warna hover primary */
            border-color: #0062cc !important;
        }

        /* (Opsional) Rapikan pembungkus tombolnya */
        .addr-modal-body .list-group-item .d-flex[style*="gap"] {
            flex-shrink: 0;
            margin-left: 1rem;
        }

        .addr-modal-header .close-btn {
            /* 1. Reset tampilan button & hapus padding global */
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;

            /* 2. Posisikan di kanan atas header */
            position: absolute;
            top: 14px;
            /* Sesuaikan jarak dari atas */
            right: 18px;
            /* Sesuaikan jarak dari kanan */

            /* 3. Styling teks 'x' agar terlihat seperti tombol close */
            font-size: 2rem !important;
            /* Ukuran 'x' */
            font-weight: 700 !important;
            line-height: 1 !important;
            color: #000 !important;
            text-shadow: 0 1px 0 #fff;
            /* Shadow ala Bootstrap 4 */
            opacity: 0.5;

            /* 4. Hapus sisa-sisa style template */
            text-transform: none !important;
            width: auto;
        }

        /* 5. Efek hover */
        .addr-modal-header .close-btn:hover {
            opacity: 0.8;
            color: #000 !important;
            background-color: transparent !important;
        }

        .addr-modal-footer .btn {
            /* 1. Atur ukuran jadi normal/kecil */
            padding: 0.375rem 0.75rem !important;
            /* Ukuran .btn default Bootstrap 4 */
            font-size: 0.9rem !important;
            line-height: 1.5 !important;

            /* 2. Hapus UPPERCASE */
            text-transform: none !important;

            /* 3. Kembalikan style .btn-secondary (Abu-abu) */
            color: #fff !important;
            background-color: #6c757d !important;
            /* Warna secondary Bootstrap 4 */
            border-color: #6c757d !important;

            /* 4. Hapus sisa-sisa style template */
            width: auto;
            margin-bottom: 0;
        }

        #addrModalOverlay .list-group-item .d-flex[style*="gap"] {
            flex-shrink: 0;
            margin-left: 1rem;
        }

        /* 5. Efek hover */
        .addr-modal-footer .btn:hover {
            color: #fff !important;
            background-color: #5a6268 !important;
            /* Warna hover secondary */
            border-color: #545b62 !important;
        }
    </style>
@endpush

@push('scripts')
    {{-- Load Library Eksternal --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script>
    <script type="text/javascript"
        src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    {{-- SCRIPT UTAMA CHECKOUT & ORDER --}}
    <script>
        $(document).ready(function() {
            // 1. Inisialisasi Plugin
            $("select.select2").select2();
            $('select.nice-select').niceSelect();

            // 2. Helper Format Rupiah
            const formatRp = (n) => 'Rp' + new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(n);

            // 3. Fungsi Update Total Harga Tampilan
            function updateTotal(shippingCost) {
                let subtotal = parseFloat($('.order_subtotal').data('price')) || 0;
                let coupon = parseFloat($('.coupon_price').data('price')) || 0;
                let total = Math.max(0, subtotal + (shippingCost || 0) - coupon);
                $('#order_total_price span').text(formatRp(total));
            }

            // 4. Fungsi Reset Ongkir
            window.resetShipping = function() {
                $('#shipping-options').empty();
                ['shipping_cost_input', 'shipping_service_input', 'shipping_courier_input'].forEach(id => $(
                    `#${id}`).val(''));
                updateTotal(0);
            };

            // 5. Event: Pilih Kurir -> Cek Ongkir
            $('select[name="courier"]').on('change', function() {
                let courier = $(this).val();
                const r = $('input[name="address_option"]:checked');
                let districtId = r.data('district-id') || null;
                let cityName = r.data('city-name') || '';
                let districtName = r.data('district-name') || '';
                let totalWeightGrams = parseFloat($('#total-weight').data('weight-grams')) || 1;
                if (totalWeightGrams <= 0) totalWeightGrams = 1;

                if (districtId && courier) {
                    $.ajax({
                        url: '{{ route('rajaongkir.checkOngkir') }}',
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            destination_district_id: districtId,
                            destination_city_name: cityName,
                            destination_district_name: districtName,
                            courier: courier,
                            weight: totalWeightGrams
                        },
                        dataType: 'json',
                        beforeSend: () => $('#shipping-options').html(
                            '<div class="p-2">Mencari layanan...</div>'),
                        success: function(response) {
                            $('#shipping-options').empty();
                            if (response[0] && response[0].costs.length > 0) {
                                $.each(response[0].costs, function(key, cost) {
                                    $('#shipping-options').append(
                                        `<div class="single-shipping" style="border-bottom: 1px solid #eee; padding: 10px 0;">
                                            <input type="radio" name="shipping_option" id="service-${key}" value="${cost.cost}" data-service="${cost.service}" data-courier="${courier.toUpperCase()}">
                                            <label for="service-${key}" style="margin-left: 10px; cursor: pointer; display: block;"><b>${courier.toUpperCase()} ${cost.service}</b> (${formatRp(cost.cost)}) <br><small>Estimasi ${cost.etd}</small></label>
                                        </div>`
                                    );
                                });
                            } else {
                                $('#shipping-options').html(
                                    '<div class="alert alert-danger p-2">Layanan pengiriman tidak tersedia.</div>'
                                );
                            }
                        },
                        error: () => $('#shipping-options').html(
                            '<div class="alert alert-danger p-2">Gagal mengambil data ongkir.</div>'
                        )
                    });
                }
                resetShipping();
            });

            // 6. Event: Pilih Layanan Ongkir (JNE REG, dll)
            $(document.body).on('change', 'input[name="shipping_option"]', function() {
                let shippingCost = parseFloat($(this).val());
                $('#shipping_cost_input').val(shippingCost);
                $('#shipping_service_input').val(`${$(this).data('courier')} - ${$(this).data('service')}`);
                $('#shipping_courier_input').val($(this).data('courier'));
                updateTotal(shippingCost);
            });

            // 7. Event: Ganti Metode Pembayaran
            function handlePaymentMethodChange() {
                const selectedMethod = $('input[name="payment_method"]:checked').val();
                const shippingSection = $('.shipping, #shipping-options, select[name="courier"]').closest('li');

                if (selectedMethod === 'cod') {
                    shippingSection.hide();
                    resetShipping();
                    // if (!$('#cod-info').length) {
                    //     $('#order_total_price').closest('.content').prepend(
                    //         '<small id="cod-info" class="text-info d-block mb-2">Alamat tidak diperlukan untuk pengambilan di tempat.</small>'
                    //     );
                    // }
                } else {
                    shippingSection.show();
                    $('#cod-info').remove();
                }
            }
            $('input[name="payment_method"]').on('change', handlePaymentMethodChange);
            handlePaymentMethodChange();

            // 8. LOGIKA TOMBOL "PESAN SEKARANG" (PLACE ORDER)
            $('#place-order-button').on('click', function(e) {
                e.preventDefault();
                let btn = $(this); // Simpan referensi tombol
                let paymentMethod = $('input[name="payment_method"]:checked').val();

                // Validasi Metode Pembayaran
                if (!paymentMethod) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Metode Pembayaran Belum Dipilih',
                        text: 'Silakan pilih metode pembayaran terlebih dahulu.',
                    });
                    return;
                }

                // Konfirmasi SweetAlert
                Swal.fire({
                    title: 'Sudah siap check-out?',
                    text: "Pastikan pesanan Anda sudah sesuai sebelum melanjutkan.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Proses Pesanan!',
                    cancelButtonText: 'Cek Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Matikan tombol agar tidak double click
                        btn.text('Processing...').prop('disabled', true);

                        // Persiapkan data alamat fallback
                        const r = $('input[name="address_option"]:checked');
                        if (r.length) {
                            $('#province_id_fallback').val(r.data('province-id') || '');
                            $('#province_name_input').val(r.data('province-name') || '');
                            $('#city_id_fallback').val(r.data('city-id') || '');
                            $('#city_name_input').val(r.data('city-name') || '');
                            $('#post_code_fallback').val(r.data('post-code') || '');
                            $('#address1_fallback').val(r.data('address1') || '');
                            $('#district_id_fallback').val(r.data('district-id') || '');
                            $('#district_name_fallback').val(r.data('district-name') || '');
                        }

                        // AJAX Submit Order
                        $.ajax({
                            url: "{{ route('cart.order') }}",
                            type: 'POST',
                            data: $('#checkout-form').serialize(),
                            success: function(response) {
                                if (response.status === 'success') {
                                    // A. MIDTRANS
                                    if (paymentMethod === 'midtrans') {
                                        if (response.snap_token) {
                                            const handleSnapCancelOrPending = () => {
                                                $.ajax({
                                                    url: "{{ route('order.cancel.unpaid') }}",
                                                    type: 'POST',
                                                    data: {
                                                        _token: "{{ csrf_token() }}",
                                                        order_number: response
                                                            .order_number
                                                    },
                                                    success: function() {
                                                        Swal.fire({
                                                            icon: 'info',
                                                            title: 'Dibatalkan',
                                                            text: 'Pembayaran dibatalkan, item kembali ke keranjang.'
                                                        }).then(
                                                            () =>
                                                            window
                                                            .location
                                                            .replace(
                                                                "{{ route('cart') }}"
                                                            ));
                                                    },
                                                    error: function() {
                                                        window.location
                                                            .replace(
                                                                "{{ route('cart') }}"
                                                            );
                                                    }
                                                });
                                                btn.text('Place Order').prop(
                                                    'disabled', false);
                                            };

                                            snap.pay(response.snap_token, {
                                                onSuccess: (result) => {
                                                    window.location.replace(
                                                        response
                                                        .redirect_url +
                                                        '?status=success&order=' +
                                                        response
                                                        .order_number);
                                                },
                                                onPending: (result) => {
                                                    // Jika pending, kita anggap user belum selesai, kembalikan ke cart
                                                    handleSnapCancelOrPending
                                                        ();
                                                },
                                                onError: (result) => {
                                                    // --- PERBAIKAN DISINI ---
                                                    // Jangan reload, tapi panggil fungsi 'kembalikan ke cart'
                                                    console.log(
                                                        'Midtrans Error:',
                                                        result);
                                                    handleSnapCancelOrPending
                                                        ();
                                                },
                                                onClose: () => {
                                                    // Jika ditutup tanpa bayar
                                                    handleSnapCancelOrPending
                                                        ();
                                                }
                                            });
                                        } else {
                                            Swal.fire('Error',
                                                'Token pembayaran tidak ditemukan.',
                                                'error');
                                            btn.text('Place Order').prop('disabled',
                                                false);
                                        }
                                    }
                                    // B. MANUAL TRANSFER
                                    else if (paymentMethod === 'manual_transfer') {
                                        const overlay = $('#bankTransferOverlay');
                                        $('#modal-total-amount').text(formatRp(response
                                            .total_amount));
                                        const waText =
                                            `Halo, saya ingin konfirmasi pesanan ${response.order_number} total ${formatRp(response.total_amount)}.`;
                                        $('#modal-wa-link').attr('href',
                                            `https://wa.me/628988199366?text=${encodeURIComponent(waText)}`
                                        );

                                        const closeModalAndRedirect = () => {
                                            overlay.removeClass('show');
                                            setTimeout(() => window.location
                                                .replace(response.redirect_url),
                                                300);
                                        };

                                        overlay.addClass('show');
                                        $(document).off('click', '.modal-close-btn').on(
                                            'click', '.modal-close-btn',
                                            closeModalAndRedirect);
                                    }
                                    // C. COD (Custom Modal)
                                    else {
                                        const waText =
                                            `Halo Jirifarm, saya pesan No: ${response.order_number} (COD). Mohon info pengambilan.`;
                                        const waLink =
                                            `https://wa.me/628988199366?text=${encodeURIComponent(waText)}`;
                                        openCustomCodModal(response.order_number,
                                            formatRp(response.total_amount), waLink,
                                            response.redirect_url);
                                    }
                                } else {
                                    Swal.fire('Oops...', response.message ||
                                        'Terjadi kesalahan.', 'error');
                                    btn.text('Place Order').prop('disabled', false);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan validasi.';
                                if (xhr.responseJSON) {
                                    if (xhr.responseJSON.errors) {
                                        errorMessage = Object.values(xhr.responseJSON
                                            .errors)[0][0];
                                    } else if (xhr.responseJSON.message) {
                                        errorMessage = xhr.responseJSON.message;
                                    }
                                }
                                Swal.fire('Gagal', errorMessage, 'error');
                                btn.text('Place Order').prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });
    </script>

    {{-- SCRIPT PENGELOLAAN ALAMAT (MODAL CRUD) --}}
    <script>
        $(function() {
            const overlay = $('#addrModalOverlay');

            // --- FUNGSI MODAL (show/hide) ---
            function openOverlay() {
                overlay.show().addClass('show');
            }

            function closeOverlay() {
                overlay.removeClass('show');
                setTimeout(() => overlay.hide(), 150);
            }

            function showList() {
                $('#addrListView').show();
                $('#addrFormView').hide();
            }

            function showForm() {
                $('#addrListView').hide();
                $('#addrFormView').show();
            }

            $('#btnManageAddress').on('click', function(e) {
                e.preventDefault();
                openOverlay();
                showList();
            });

            $(document).on('click', '#addrModalOverlay .close-btn, #addrModalOverlay .addr-modal-footer .btn',
                function(e) {
                    e.preventDefault();
                    closeOverlay();
                });

            overlay.on('click', function(e) {
                if (e.target === this) closeOverlay();
            });

            // --- FUNGSI RESET FORM ---
            function resetForm() {
                $('#addressForm')[0].reset();
                $('#addr_id').val('');
                $('#addr_method').val('POST');
                $('#addrFormTitle').text('Tambah Alamat');
                $('#addr_province_id').val('').niceSelect('update');
                $('#addr_city_id').empty().append('<option value="">Pilih Kota/Kabupaten</option>').prop('disabled',
                    true).niceSelect('update');
                $('#addr_district_id').empty().append('<option value="">Pilih Kecamatan</option>').prop('disabled',
                    true).niceSelect('update');
                $('#addr_sub_district_id').empty().append('<option value="">Pilih Kelurahan/Desa</option>').prop(
                    'disabled', true).niceSelect('update');
                $('#addr_post_code').val('');
                $('#addr_province_name, #addr_city_name, #addr_district_name').val('');
            }

            // --- FUNGSI LOAD DATA API ---
            function loadProvinces(selectedId) {
                return $.getJSON('{{ route('rajaongkir.provinces') }}', function(data) {
                    const $p = $('#addr_province_id');
                    $p.empty().append('<option value="">Pilih Provinsi</option>');
                    data.forEach(v => $p.append(`<option value="${v.id}">${v.name}</option>`));
                    if (selectedId) $p.val(selectedId);
                    $p.niceSelect('update');
                });
            }

            function loadCities(provinceId, selectedCityId) {
                if (!provinceId) {
                    $('#addr_city_id').prop('disabled', true).niceSelect('update');
                    return $.Deferred().resolve().promise();
                }
                return new Promise((resolve, reject) => {
                    $.getJSON("{{ route('rajaongkir.cities', ['province_id' => ':id']) }}".replace(':id',
                        provinceId), function(data) {
                        const $c = $('#addr_city_id');
                        $c.empty().append('<option value="">Pilih Kota/Kabupaten</option>');
                        data.forEach(v => $c.append(`<option value="${v.id}">${v.name}</option>`));
                        $c.prop('disabled', false).niceSelect('update');
                        if (selectedCityId) {
                            setTimeout(() => {
                                $c.val(selectedCityId).niceSelect('update');
                                resolve();
                            }, 50);
                        } else {
                            resolve();
                        }
                    }).fail(reject);
                });
            }

            function loadDistricts(cityId, selectedDistrictId) {
                if (!cityId) {
                    $('#addr_district_id').prop('disabled', true).niceSelect('update');
                    return $.Deferred().resolve().promise();
                }
                return new Promise((resolve, reject) => {
                    $.getJSON("{{ route('rajaongkir.districts', ['city_id' => ':id']) }}".replace(':id',
                        cityId), function(data) {
                        const $d = $('#addr_district_id');
                        $d.empty().append('<option value="">Pilih Kecamatan</option>');
                        data.forEach(v => $d.append(`<option value="${v.id}">${v.name}</option>`));
                        $d.prop('disabled', false).niceSelect('update');
                        if (selectedDistrictId) {
                            setTimeout(() => {
                                $d.val(selectedDistrictId).niceSelect('update');
                                resolve();
                            }, 50);
                        } else {
                            resolve();
                        }
                    }).fail(reject);
                });
            }

            function loadSubDistricts(districtId, selectedSubDistrictId) {
                if (!districtId) {
                    $('#addr_sub_district_id').prop('disabled', true).niceSelect('update');
                    return $.Deferred().resolve().promise();
                }
                return new Promise((resolve, reject) => {
                    $.getJSON("{{ route('rajaongkir.subdistricts', ['district_id' => ':id']) }}".replace(
                        ':id', districtId), function(data) {
                        const $s = $('#addr_sub_district_id');
                        $s.empty().append('<option value="">Pilih Kelurahan/Desa</option>');
                        data.forEach(v => $s.append(
                            `<option value="${v.id}" data-zip="${v.zip_code}">${v.name}</option>`
                        ));
                        $s.prop('disabled', false).niceSelect('update');
                        if (selectedSubDistrictId) {
                            setTimeout(() => {
                                $s.val(selectedSubDistrictId).niceSelect('update');
                                resolve();
                            }, 50);
                        } else {
                            resolve();
                        }
                    }).fail(reject);
                });
            }

            function checkAddressFormValidity() {
                const req = ['#addr_province_id', '#addr_city_id', '#addr_district_id', '#addr_sub_district_id',
                    '#addr_phone', '#addr_address1'
                ];
                const isComplete = req.every(sel => $(sel).val());
                $('#addrSubmit').prop('disabled', !isComplete).text(isComplete ? 'Simpan' : 'Lengkapi Data');
            }

            // --- EVENT HANDLER DROPDOWN ---
            $('#addr_province_id').on('change', async function() {
                $('#addr_province_name').val($('#addr_province_id option:selected').text());
                $('#addr_city_id').val('').prop('disabled', true).niceSelect('update');
                $('#addr_district_id').val('').prop('disabled', true).niceSelect('update');
                $('#addr_sub_district_id').val('').prop('disabled', true).niceSelect('update');
                if ($(this).val()) await loadCities($(this).val());
            });

            $('#addr_city_id').on('change', async function() {
                $('#addr_city_name').val($('#addr_city_id option:selected').text());
                $('#addr_district_id').val('').prop('disabled', true).niceSelect('update');
                $('#addr_sub_district_id').val('').prop('disabled', true).niceSelect('update');
                if ($(this).val()) await loadDistricts($(this).val());
            });

            $('#addr_district_id').on('change', async function() {
                $('#addr_district_name').val($('#addr_district_id option:selected').text());
                $('#addr_sub_district_id').val('').prop('disabled', true).niceSelect('update');
                if ($(this).val()) await loadSubDistricts($(this).val());
            });

            $('#addr_sub_district_id').on('change', function() {
                const opt = $(this).find('option:selected');
                $('#addr_post_code').val(opt.data('zip') || '');
                $('#addr_sub_district_name').val(opt.text());
                checkAddressFormValidity();
            });

            $('#addressForm').on('change keyup', 'input, select, textarea', checkAddressFormValidity);

            // --- CRUD ACTIONS ---
            $('#btnAddAddress').on('click', async function() {
                resetForm();
                await loadProvinces();
                showForm();
                checkAddressFormValidity();
            });

            $('#btnBackList, #addrCancel').on('click', () => showList());

            $(document).on('click', '.addr-edit', async function(e) {
                e.preventDefault();
                const d = this.dataset;
                resetForm();
                $('#addr_id').val(d.id);
                $('#addr_method').val('PUT');
                $('#addrFormTitle').text('Ubah Alamat');
                $('#addr_label').val(d.label);
                // $('#addr_first_name').val(d.firstName);
                // $('#addr_last_name').val(d.lastName);
                // $('#addr_email').val(d.email);
                $('#addr_phone').val(d.phone);
                $('#addr_post_code').val(d.postCode);
                $('#addr_address1').val(d.address1);

                await loadProvinces(d.provinceId);
                $('#addr_province_name').val(d.provinceName);
                await loadCities(d.provinceId, d.cityId);
                $('#addr_city_name').val(d.cityName);
                await loadDistricts(d.cityId, d.districtId);
                $('#addr_district_name').val(d.districtName);
                await loadSubDistricts(d.districtId, d.subDistrictId);
                $('#addr_sub_district_name').val(d.subDistrictName);
                showForm();
                checkAddressFormValidity();
            });

            // Set Default
            $(document).on('click', '.addr-default', function(e) {
                e.preventDefault();

                let encId = $(this).data('id'); // encryptedId

                $.ajax({
                    url: "/addresses/" + encId + "/set-default",
                    method: "PATCH",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: (res) => Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload()),

                    error: (xhr) => Swal.fire(
                        "Gagal",
                        xhr.responseJSON?.message || "Gagal menjadikan default.",
                        "error"
                    )
                });
            });

            // Delete Address
            $(document).on('click', '.addr-delete', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus alamat?',
                    text: "Tindakan ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('addresses.destroy', ':id') }}".replace(':id',
                                id),
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: "{{ csrf_token() }}"
                            },
                            success: (res) => Swal.fire('Terhapus!',
                                'Alamat berhasil dihapus.', 'success').then(() =>
                                location.reload()),
                            error: (xhr) => Swal.fire('Gagal', xhr.responseJSON?.message ||
                                'Gagal menghapus.', 'error')
                        });
                    }
                });
            });

            // Submit Form
            $('#addressForm').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#addrSubmit');
                const id = $('#addr_id').val();
                const url = id ? "{{ route('addresses.update', ':id') }}".replace(':id', id) :
                    "{{ route('addresses.store') }}";

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).text('Menyimpan...'),
                    success: (res) => Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => location.reload()),
                    error: (xhr) => {
                        let msg = 'Gagal menyimpan alamat.';
                        if (xhr.responseJSON?.errors) msg = Object.values(xhr.responseJSON
                            .errors)[0][0];
                        else if (xhr.responseJSON?.message) msg = xhr.responseJSON.message;
                        Swal.fire('Gagal Menyimpan', msg, 'error');
                        $btn.prop('disabled', false).text('Simpan');
                    }
                });
            });

            // Pilih Alamat (Radio Button)
            $(document).on('change', 'input[name="address_option"]', function() {
                if (this.value !== 'existing') return;
                const districtId = $(this).data('district-id');

                let hid = $('input[name="address_id"]');
                if (!hid.length) hid = $('<input type="hidden" name="address_id">').appendTo(
                    '#checkout-form');
                hid.val($(this).data('id'));

                $('#courier').prop('disabled', !districtId).niceSelect('update');
                if (typeof resetShipping === 'function') resetShipping();
            });

            // Init saat load halaman
            const initR = $('input[name="address_option"]:checked');
            if (initR.length) {
                $('#courier').prop('disabled', !initR.data('district-id')).niceSelect('update');
            }
        });
    </script>
@endpush
