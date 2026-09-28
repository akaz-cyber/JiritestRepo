@extends('frontend.layouts.master')
@section('title', 'Cart Page')

@section('main-content')
    @once
        @include('frontend.layouts.wa_fb')
    @endonce
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="bread-inner">
                        <ul class="bread-list">
                            <li><a href="{{ route('home') }}">Beranda<i class="ti-arrow-right"></i></a></li>
                            <li class="active"><a href="">Keranjang</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $formatRp = fn($n) => 'Rp' . number_format($n, 0, ',', '.');
    @endphp

    <div class="shopping-cart section">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div id="cart-ajax-alert-container"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <form id="cart-form" action="{{ route('cart.update') }}" method="POST">
                        @csrf
                        <div class="alert alert-sticky alert-info" role="alert"
                            style="font-size: 14px; border-radius: 5px; background-color: #e6f7ff; border-color: #b3e7ff; color: #0056b3;">
                            <i class="ti-info-alt" style="margin-right: 5px;"></i>
                            <strong>Perhatian:</strong> Pastikan produk yang ingin Anda beli telah tercentang,
                        </div>
                        <div class="table-responsive-wrapper">
                            <table class="table shopping-summery">
                                <thead>
                                    <tr class="main-hading">
                                        <th class="text-center" style="width: 50px;">
                                            <input type="checkbox" id="select-all-checkbox" title="Pilih Semua">
                                        </th>
                                        <th>PRODUK</th>
                                        <th>NAMA</th>
                                        <th class="text-center">HARGA PERUNIT</th>
                                        <th class="text-center">QUANTITY</th>
                                        <th class="text-center">TOTAL</th>
                                        <th class="text-center">
                                            <a href="javascript:void(0);" id="delete-selected-btn"
                                                title="Hapus item terpilih" style="color: #fffdfd; cursor: pointer;">
                                                <i class="ti-trash remove-icon"></i>
                                            </a>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="cart_item_list">
                                    @forelse (Helper::getAllProductFromCart() as $key => $cart)
                                        @php
                                            // ambil foto varian dulu kalau ada
                                            if (!empty($cart->variant?->photo)) {
                                                $thumbPath = $cart->variant->photo;
                                            } else {
                                                $paths = array_values(
                                                    array_filter(
                                                        array_map('trim', explode('|', $cart->product['photo'] ?? '')),
                                                    ),
                                                );
                                                $thumbPath = $paths[0] ?? null;
                                            }

                                            $thumb = $thumbPath ? asset('storage/' . $thumbPath) : null;
                                            $unit = (int) ($cart['price'] ?? 0);
                                            $qty = (int) ($cart->quantity ?? 0);
                                            $line = $cart->computed_amount;
                                            $stock = $cart->variant
                                                ? (int) $cart->variant->stock
                                                : (int) ($cart->product->stock ?? 0);
                                        @endphp

                                        {{-- 2. TAMBAH DATA-PRICE PADA TR UNTUK PERHITUNGAN JS --}}
                                        <tr data-price="{{ $line }}" data-unit-price="{{ $unit }}">
                                            {{-- 3. TAMBAH KOLOM CHECKBOX PER ITEM --}}
                                            <td class="text-center align-middle">
                                                <input type="checkbox" class="cart-item-checkbox"
                                                    data-id="{{ $cart->id }}" name="selected_carts[]"
                                                    value="{{ $cart->id }}" {{ $cart->is_checked ? 'checked' : '' }}>
                                            </td>

                                            <td class="image" data-title="No">
                                                @if ($thumb)
                                                    <img src="{{ $thumb }}" alt="thumb">
                                                @endif
                                            </td>

                                            <td class="product-des" data-title="Description">
                                                <p class="product-name mb-1">
                                                    <a href="{{ route('product-detail', $cart->product['slug']) }}"
                                                        target="_blank">
                                                        {{ $cart->product['title'] }}
                                                    </a>
                                                </p>
                                                @if ($cart->variant)
                                                    <div class="text-muted small">Varian:
                                                        {{ $cart->variant->variant_name }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="price text-center" data-title="Price">
                                                <span>{{ $formatRp($unit) }}</span>
                                            </td>

                                            <td class="qty" data-title="Qty">
                                                <div class="input-group">
                                                    <div class="button minus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                            {{ $qty <= 1 ? 'disabled="disabled"' : '' }} data-type="minus"
                                                            data-field="quant[{{ $cart->id }}]">
                                                            <i class="ti-minus"></i>
                                                        </button>
                                                    </div>
                                                    <input type="text" name="quant[{{ $cart->id }}]"
                                                        class="input-number" data-min="1" data-max="{{ $stock }}"
                                                        value="{{ $qty }}">
                                                    <div class="button plus">
                                                        <button type="button" class="btn btn-primary btn-number"
                                                            data-type="plus" data-field="quant[{{ $cart->id }}]">
                                                            <i class="ti-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="total-amount cart_single_price text-center" data-title="Total">
                                                <span class="money">{{ $formatRp($line) }}</span>
                                            </td>

                                            <td class="action text-center" data-title="Remove">
                                                <a href="{{ route('cart-delete', $cart->id) }}"><i
                                                        class="ti-trash remove-icon"></i></a>
                                            </td>
                                        </tr>
                                        <tr class="cart-notes-row">
                                            <td></td> {{-- Kolom kosong untuk meluruskan dengan checkbox --}}
                                            <td data-title="Catatan" colspan="6" class="py-0 px-2">
                                                <div class="form-group mb-2 w-75 w-md-50">
                                                    <input type="text" name="notes[{{ $cart->id }}]"
                                                        class="form-control form-control-sm notes-input"
                                                        placeholder="Tulis catatan/instruksi pesananmu"
                                                        value="{{ $cart->notes }}" maxlength="150"
                                                        data-id="{{ $cart->id }}" />
                                                    <small class="char-counter text-muted float-right"
                                                        data-counter-id="{{ $cart->id }}">
                                                        {{ strlen($cart->notes) }}/150
                                                    </small>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            {{-- Ubah colspan menjadi 7 --}}
                                            <td class="text-center" colspan="7">
                                                Tidak ada item di keranjang.
                                                <a href="{{ route('product-grids') }}" style="color:blue;">Lanjut
                                                    berbelanja</a>
                                            </td>
                                        </tr>
                                    @endforelse

                                    @if (Helper::getAllProductFromCart() && count(Helper::getAllProductFromCart()) > 0)
                                        <tr>
                                            <td colspan="5"></td>
                                            <td colspan="2" class="text-right">
                                                <button class="btn" type="submit">Perbarui</button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="total-amount">
                        <div class="row">
                            <div class="col-lg-8 col-md-5 col-12">
                                <div class="left">
                                    {{-- Bagian Kupon bisa ditambahkan di sini jika perlu --}}
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-7 col-12">
                                <div class="right">
                                    {{-- @php
                                        // Cart page: jangan ambil nilai diskon dari session (tidak disimpan).
                                        // Diskon final dihitung ulang di Checkout (POST).
                                        $subTotal = (float) Helper::totalCartPrice();
                                        $coupon   = 0.0; // biarkan 0 di halaman cart
                                        $toPay    = max(0, $subTotal - $coupon);
                                    @endphp --}}

                                    @php
                                        // Helper akan otomatis menghitung item yang tercentang saja saat halaman dimuat
                                        $subTotal = (int) Helper::totalCartPrice();
                                    @endphp

                                    <ul>
                                        {{-- 4. TAMBAH ID PADA SPAN UNTUK TARGET JS --}}
                                        <li class="order_subtotal">
                                            Subtotal keranjang
                                            <span id="subtotal-display">{{ $formatRp($subTotal) }}</span>
                                        </li>
                                        <li class="last">
                                            Total Bayar
                                            <span id="total-pay-display">{{ $formatRp($subTotal) }}</span>
                                        </li>
                                    </ul>
                                    <div class="button5">
                                        <a href="{{ route('checkout') }}" id="checkout-btn" class="btn">Checkout</a>
                                        <a href="{{ route('product-grids') }}" class="btn">Lanjutkan berbelanja</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        /* 1. PERBAIKI PADDING BREADCRUMB DI MOBILE */
        @media (max-width: 991px) {
            .breadcrumbs {
                /* Mengubah dari 90px (responsive.css) menjadi 20px */
                padding: 20px 0 !important;
            }
        }

        /* 2. PERBAIKI PADDING ATAS AREA KERANJANG (SEMUA UKURAN) */
        .shopping-cart.section {
            /* Mengubah dari 50px (style.css) menjadi 20px */
            padding-top: 20px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('frontend/js/nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('frontend/js/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("select.select2").select2();
            $('select.nice-select').niceSelect();

            const formatRp = (n) => {
                return 'Rp' + Number(n || 0).toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });
            };
            $('#checkout-btn').on('click', function(e) {
                // 1. Mencegah link pindah halaman secara langsung
                e.preventDefault();

                let form = $('#cart-form');
                let checkoutUrl = $(this).attr('href');
                let checkoutButton = $(this);

                // Kirim semua data form (kuantitas & catatan) menggunakan AJAX
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    beforeSend: function() {
                        $('#cart-ajax-alert-container').html('');
                        checkoutButton.text('Menyimpan...');
                        checkoutButton.addClass('disabled');
                    },
                    success: function(response) {
                        // 2. Jika server berhasil menyimpan (sukses), lanjutkan ke halaman checkout
                        console.log('Keranjang berhasil diupdate, melanjutkan ke checkout...');
                        window.location.href = checkoutUrl;
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else {
                            console.error('Gagal mengupdate keranjang:', xhr.responseText);
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Stok Tidak Cukup',
                            html: errorMessage,
                            confirmButtonText: 'OK'
                        });

                        checkoutButton.text('Checkout');
                        checkoutButton.removeClass('disabled');
                    }
                });
            });

            function updateCharCounter(inputElement) {
                let currentLength = $(inputElement).val().length;
                let maxLength = $(inputElement).attr('maxlength');
                let cartId = $(inputElement).data('id');
                let counterElement = $('.char-counter[data-counter-id="' + cartId + '"]');
                counterElement.text(currentLength + '/' + maxLength);
            }

            $('.notes-input').each(function() {
                updateCharCounter(this);
            });

            $('.notes-input').on('keyup input', function() {
                updateCharCounter(this);
            });

            function updateCheckoutButton() {
                const checkoutButton = $('#checkout-btn');
                const checkedItems = $('.cart-item-checkbox:checked');
                let checkoutUrl = "{{ route('checkout') }}";

                if (checkedItems.length > 0) {
                    const selectedIds = checkedItems.map(function() {
                        return $(this).data('id');
                    }).get();

                    checkoutUrl += "?items=" + selectedIds.join(',');
                }

                checkoutButton.attr('href', checkoutUrl);
            }

            function initializeQuantityButtons() {
                $('.input-number').each(function() {
                    let $input = $(this);
                    let currentQty = parseInt($input.val()) || 0;
                    let min = parseInt($input.data('min')) || 1;
                    let max = parseInt($input.data('max'));

                    let $plusBtn = $input.siblings('.button.plus').find('.btn-number');
                    let $minusBtn = $input.siblings('.button.minus').find('.btn-number');

                    // Nonaktifkan PLUS jika qty >= stok
                    if (!isNaN(max) && currentQty >= max) {
                        $plusBtn.prop('disabled', true);
                    } else {
                        $plusBtn.prop('disabled', false);
                    }

                    // Nonaktifkan MINUS jika qty <= min
                    if (currentQty <= min) {
                        $minusBtn.prop('disabled', true);
                    } else {
                        $minusBtn.prop('disabled', false);
                    }
                });
            }

            function updateLineItem($input, event) {
                let $row = $input.closest('tr');
                if (!$row.length) return;

                let unitPrice = parseInt($row.data('unit-price')) || 0;
                let newQty = parseInt($input.val()) || 0;

                // Ambil batas min dan max (stok) dari atribut data-
                let min = parseInt($input.data('min')) || 1;
                let max = parseInt($input.data('max')); // Ini adalah stok Anda

                // Dapatkan tombol plus dan minus
                let $plusBtn = $input.siblings('.button.plus').find('.btn-number');
                let $minusBtn = $input.siblings('.button.minus').find('.btn-number');

                // --- VALIDASI STOK (MAX) ---
                // Cek apakah 'max' adalah angka yang valid (stok bisa jadi 0)
                if (!isNaN(max) && newQty > max) {
                    newQty = max; // Paksa nilainya kembali ke max
                    $input.val(newQty); // Update tampilan input
                }

                // --- VALIDASI MIN (saat 'change' / blur) ---
                // Kita lakukan ini di 'change' agar user bisa hapus "1" untuk mengetik "10"
                if (event.type === 'change') {
                    if (isNaN(newQty) || newQty < min) {
                        newQty = min;
                        $input.val(newQty);
                    }
                }

                // --- Atur Status Tombol (Disabled/Enabled) ---
                // Jika kuantitas >= stok (max), nonaktifkan tombol PLUS
                if (!isNaN(max) && newQty >= max) {
                    $plusBtn.prop('disabled', true);
                } else {
                    $plusBtn.prop('disabled', false);
                }

                // Jika kuantitas <= min, nonaktifkan tombol MINUS
                if (newQty <= min) {
                    $minusBtn.prop('disabled', true);
                } else {
                    $minusBtn.prop('disabled', false);
                }

                // --- Kalkulasi Ulang Harga ---
                let newLineTotal = unitPrice * newQty;

                // Update tampilan total baris
                $row.find('.total-amount .money').text(formatRp(newLineTotal));

                // Update cache data jQuery untuk subtotal
                $row.data('price', newLineTotal);

                // Panggil fungsi update subtotal
                if (typeof updateTotals === 'function') {
                    updateTotals();
                }
            }
            $('#cart_item_list').on('input', '.input-number', function(event) {
                updateLineItem($(this), event);
            });

            $('#cart_item_list').on('change', '.input-number', function(event) {
                updateLineItem($(this), event);
            });

            function updateTotals() {
                let subTotal = 0;
                const checkedItems = $('.cart-item-checkbox:checked');

                checkedItems.each(function() {
                    let price = parseInt($(this).closest('tr').data('price')) || 0;
                    subTotal += price;
                    // if (!isNaN(price)) {
                    //     subTotal += price;
                    // }
                });
                $('#subtotal-display').text(formatRp(subTotal));
                $('#total-pay-display').text(formatRp(subTotal));

                const checkoutButton = $('#checkout-btn');
                if (checkedItems.length > 0) {
                    checkoutButton.removeClass('disabled');
                    checkoutButton.css('pointer-events', 'auto');
                    checkoutButton.prop('aria-disabled', 'false');
                } else {
                    checkoutButton.addClass('disabled');
                    checkoutButton.css('pointer-events', 'none');
                    checkoutButton.prop('aria-disabled', 'true');
                }

                updateCheckoutButton();
            }

            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.cart-item-checkbox').length;
                const checkedCheckboxes = $('.cart-item-checkbox:checked').length;
                $('#select-all-checkbox').prop('checked', totalCheckboxes > 0 && totalCheckboxes ===
                    checkedCheckboxes);

            }

            function saveSelection(cartId, isChecked) {
                $.ajax({
                    url: "{{ route('cart.update-selection') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        cart_id: cartId,
                        is_checked: isChecked,
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#subtotal-display').text(formatRp(response.new_sub_total));
                            $('#total-pay-display').text(formatRp(response.new_sub_total));
                            console.log('Selection saved for cart ID: ' + cartId);
                        }
                    },
                    error: function() {
                        console.error('Failed to save selection for cart ID: ' + cartId);
                    }
                });
            }



            $('.cart-item-checkbox').on('change', function() {
                const cartId = $(this).data('id');
                const isChecked = $(this).is(':checked');
                updateTotals();
                updateSelectAllCheckbox();
                saveSelection(cartId, isChecked);
            });

            $('#select-all-checkbox').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.cart-item-checkbox').each(function() {
                    if ($(this).is(':checked') !== isChecked) {
                        $(this).prop('checked', isChecked).trigger('change');
                    }
                });
            });

            $('#delete-selected-btn').on('click', function(e) {
                e.preventDefault();
                const checkedItems = $('.cart-item-checkbox:checked');
                const ids = checkedItems.map(function() {
                    return $(this).data('id');
                }).get();

                if (ids.length === 0) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'Silakan pilih produk yang ingin dihapus.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                const count = ids.length;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: 'Anda akan menghapus ' + count + ' item dari keranjang.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6', // Warna tombol konfirmasi
                    cancelButtonColor: '#d33', // Warna tombol batal
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('cart.delete-selected') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: ids
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    text: 'Harap tunggu.',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Berhasil!',
                                        response.message || 'Item telah dihapus.',
                                        'success'
                                    ).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        response.message || 'Gagal menghapus item.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                // Tampilkan pesan error teknis
                                console.error('AJAX Error:', xhr.responseText);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan. Silakan coba lagi.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            initializeQuantityButtons();
            // Inisialisasi saat halaman dimuat
            updateTotals();
            updateSelectAllCheckbox();
        });
    </script>
@endpush
