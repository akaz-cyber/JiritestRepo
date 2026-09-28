@extends('backend.layouts.master')

@section('title', 'Order Detail')

@section('main-content')
    <div class="card">
        <h5 class="card-header">Order Edit</h5>
        <div class="card-body">
            @if (!$isStockSufficient)
                <div class="alert alert-danger alert-sticky">
                    <h5 class="alert-heading">⚠️ Stok Tidak Cukup!</h5>
                    <p>Stok untuk item berikut tidak mencukupi untuk memenuhi pesanan ini:</p>
                    <ul>
                        @foreach ($outOfStockItems as $itemMessage)
                            <li><strong>{{ $itemMessage }}</strong></li>
                        @endforeach
                    </ul>
                    <hr>
                    <p class="mb-0">Anda tidak dapat memproses atau mengirim pesanan ini. Harap batalkan pesanan.</p>
                </div>
            @endif
            <form action="{{ route('order.update', $order->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="status">Status :</label>
                    <select name="status" id="" class="form-control">
                        <option value="new" {{ $order->status != 'new' || !$isStockSufficient ? 'disabled' : '' }}
                            {{ $order->status == 'new' ? 'selected' : '' }}>New</option>

                        <option value="process"
                            {{ $order->status == 'delivered' || $order->status == 'cancel' || !$isStockSufficient ? 'disabled' : '' }}
                            {{ $order->status == 'process' ? 'selected' : '' }}>Process</option>

                        <option value="delivered" {{ $order->status == 'cancel' || !$isStockSufficient ? 'disabled' : '' }}
                            {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>

                        <option value="cancel" {{ $order->status == 'delivered' ? 'disabled' : '' }}
                            {{ $order->status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="payment_status">Payment Status :</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_status" id="payment_status_paid"
                            value="paid" {{ $order->payment_status == 'paid' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_status_paid">
                            Paid
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_status" id="payment_status_unpaid"
                            value="unpaid" {{ $order->payment_status == 'unpaid' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_status_unpaid">
                            Unpaid
                        </label>
                    </div>
                </div>

                @if (strtolower($order->payment_method) != 'cod')
                    <div class="form-group"
                        style="background: #f8f9fa; padding: 15px; border-left: 4px solid #4e73df; border-radius: 4px;">
                        <label for="resi_number" class="font-weight-bold">
                            Nomor Resi Pengiriman
                            <span class="badge badge-primary">{{ strtoupper($order->shipping_courier ?? '-') }} -
                                {{ $order->shipping_service ?? '' }}</span> :
                        </label>
                        <input type="text" name="resi_number" id="resi_number" class="form-control mb-2"
                            placeholder="Masukkan Nomor Resi (Contoh: JP1234567890 / 084460000988926)"
                            value="{{ old('resi_number', $order->resi_number) }}">

                        {{-- KHUSUS JNE: TAMBAHKAN INPUT UNTUK VERIFIKASI NOMOR HP --}}
                        @if (strtolower($order->shipping_courier) == 'jne')
                            <label for="jne_phone_verify" class="font-weight-bold mt-2 text-danger">
                                <i class="fas fa-exclamation-circle"></i> 5 Digit Terakhir No. HP Penerima (Syarat Lacak
                                JNE) :
                            </label>
                            <input type="text" name="jne_phone_verify" id="jne_phone_verify" class="form-control"
                                maxlength="5" placeholder="Contoh: 12345"
                                value="{{ old('jne_phone_verify', $order->jne_phone_verify) }}">
                            <small class="form-text text-muted">
                                *JNE mewajibkan 5 digit terakhir nomor telepon penerima/pengirim untuk membuka detail
                                pelacakan resi.
                            </small>
                        @else
                            <small class="form-text text-muted">
                                *Isi nomor resi ini setelah paket diserahkan ke kurir agar pembeli bisa melacak pesanan.
                            </small>
                        @endif
                    </div>
                @else
                    {{-- Jika COD tidak perlu input resi --}}
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Pesanan ini menggunakan metode <strong>Ambil di Tempat
                            (COD)</strong> sehingga tidak memerlukan Nomor Resi Ekspedisi.
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .order-info,
        .shipping-info {
            background: #ECECEC;
            padding: 20px;
        }

        .order-info h4,
        .shipping-info h4 {
            text-decoration: underline;
        }
    </style>
@endpush
