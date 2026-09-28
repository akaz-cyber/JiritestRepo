@extends('user.layouts.master')

@section('title', 'Order Detail')

@section('main-content')

    {{-- Helper Format Rupiah --}}
    @php
        $formatRp = fn($n) => 'Rp' . number_format($n, 0, ',', '.');
    @endphp

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-column flex-md-row align-items-start align-items-md-center">

            <h6 class="m-0 font-weight-bold text-primary mb-3 mb-md-0 mr-md-auto">
                Detail Pesanan: {{ $order->order_number }}
            </h6>

            <div class="d-flex flex-column flex-sm-row w-100 w-md-auto justify-content-end">
                @if ($order->payment_method == 'midtrans' && $order->payment_status == 'paid' && $order->status == 'new')
                    @php
                        $waConfirmNumber = '628988199366';
                        $waConfirmText =
                            'Halo admin Jirifarm, saya sudah berhasil melakukan pembayaran online untuk pesanan dengan Nomor Order: *' .
                            $order->order_number .
                            '*. Mohon untuk dicek dan segera diproses ya. Terima kasih!';
                        $waConfirmLink = 'https://wa.me/' . $waConfirmNumber . '?text=' . rawurlencode($waConfirmText);
                    @endphp
                    <a href="{{ $waConfirmLink }}" class="btn btn-sm btn-success shadow-sm mb-2 mb-sm-0 mr-sm-2 text-center"
                        target="_blank">
                        <i class="fab fa-whatsapp fa-sm text-white-50"></i> Konfirmasi Pembayaran
                    </a>
                @endif
                <a href="{{ route('order.pdf', $order->id) }}"
                    class="btn btn-sm btn-primary shadow-sm mb-2 mb-sm-0 mr-sm-2 text-center" target="_blank">
                    <i class="fas fa-download fa-sm text-white-50"></i> Cetak Invoice (PDF)
                </a>

                @php
                    $waNumber = '628988199366';
                    $waMessage =
                        'Halo Kak,, saya mengalami masalah pada pesanan saya dengan nomor: ' . $order->order_number;
                    $waLink = 'https://wa.me/' . $waNumber . '?text=' . urlencode($waMessage);
                @endphp

                <a href="{{ $waLink }}" class="btn btn-sm btn-danger shadow-sm text-center" target="_blank">
                    <i class="fas fa-exclamation-triangle fa-sm text-white-50"></i> Ada masalah pada pembelian Anda?
                </a>
            </div>

        </div>
        <div class="card-body">
            @if ($order)
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3 text-uppercase">Informasi Pesanan</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:150px;">Nomor Pesanan</th>
                                <td>: {{ $order->order_number }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Pesanan</th>
                                <td>: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status Pesanan</th>
                                <td>:
                                    @if ($order->status == 'new')
                                        <span class="badge badge-info">Baru</span>
                                    @elseif($order->status == 'process')
                                        <span class="badge badge-warning">Diproses</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge badge-success">Terkirim</span>
                                    @elseif($order->status == 'cancel')
                                        <span class="badge badge-danger">Dibatalkan</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Metode Pembayaran</th>
                                <td>:
                                    @if ($order->payment_method == 'cod')
                                        Ambil di Tempat
                                    @else
                                        {{-- Sesuaikan jika ada metode lain --}}
                                        Online Payment ({{ strtoupper($order->payment_method) }})
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status Pembayaran</th>
                                <td>:
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-success">Lunas</span>
                                    @elseif($order->payment_status == 'unpaid')
                                        <span class="badge badge-danger">Belum Lunas</span>
                                    @else
                                        <span class="badge badge-secondary">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Total berat</th>
                                <td>:
                                    @php $weight = $order->total_weight_gram ?? 0; @endphp
                                    @if ($weight < 1000)
                                        <span style="white-space: nowrap;">{{ $weight }} gr</span>
                                    @else
                                        <span style="white-space: nowrap;">{{ number_format($weight / 1000, 2, ',', '.') }}
                                            kg</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3 text-uppercase">Informasi Penerima</h5>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th style="width:150px;">Nama Lengkap</th>
                                <td>: {{ $order->first_name }} {{ $order->last_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>: {{ $order->email }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>: {{ $order->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td style="white-space: pre-wrap;">: {{ $order->address1 ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kota/Kab.</th>
                                <td>: {{ $order->city_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Provinsi</th>
                                <td>: {{ $order->province_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kode Pos</th>
                                <td>: {{ $order->post_code ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kurir / Layanan</th>
                                <td>:
                                    @if (strtolower($order->payment_method) == 'cod')
                                        <span class="badge badge-primary">Bayar di Tempat</span>
                                    @else
                                        {{ $order->shipping_courier ?? '-' }}
                                        {{ $order->shipping_service ? '(' . $order->shipping_service . ')' : '' }}
                                    @endif
                                </td>
                            </tr>
                            @if ($order->resi_number)
                                <tr>
                                    <th>Nomor Resi</th>
                                    <td>: <span class="font-weight-bold text-primary">{{ $order->resi_number }}</span></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- ====== ITEMS ====== --}}
                <h5 class="mb-3 mt-4 text-uppercase">Detail Barang</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Produk</th>
                                <th style="width:180px;">Varian</th>
                                <th class="text-center" style="width:90px;">Qty</th>
                                <th class="text-right" style="width:150px;">Harga Satuan</th>
                                <th class="text-right" style="width:160px;">Subtotal</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Asumsi relasi ke item cart/order adalah 'cart' atau 'cart_info' --}}
                            {{-- Ganti 'cart_info' jika nama relasinya berbeda --}}
                            @forelse($order->cart as $item)
                                @php
                                    $productName = $item->product->title ?? 'Produk Dihapus';
                                    $variantName = $item->variant->variant_name ?? '-'; // Sesuaikan jika relasi/nama kolom beda
                                    $unitPrice = (float) ($item->price ?? 0);
                                    $quantity = (int) ($item->quantity ?? 0);
                                    $lineTotal = (float) ($item->amount ?? $unitPrice * $quantity);
                                @endphp
                                <tr>
                                    <td>{{ $productName }}</td>
                                    <td>{{ $variantName }}</td>
                                    <td class="text-center">{{ $quantity }}</td>
                                    <td class="text-right">{{ $formatRp($unitPrice) }}</td>
                                    <td class="text-right">{{ $formatRp($lineTotal) }}</td>
                                    <td style="max-width:300px; white-space: pre-wrap;">{{ $item->notes ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada item dalam pesanan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ====== RINGKASAN ====== --}}
                @php
                    $subtotal = (float) ($order->sub_total ?? 0);
                    $shipping = (float) ($order->delivery_charge ?? 0);
                    $discount = (float) ($order->coupon ?? 0);
                    $total = (float) ($order->total_amount ?? $subtotal + $shipping - $discount);
                    // Pastikan $order->cart tersedia dan merupakan collection
                    $totalQty = $order->cart ? $order->cart->sum('quantity') ?? 0 : 0;
                @endphp
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-6">
                        <h5 class="mb-3 text-uppercase">Ringkasan Pembayaran</h5>
                        <table class="table table-sm">
                            <tr>
                                <th class="text-right" style="width:60%;">Subtotal ({{ $totalQty }} item) :</th>
                                <td class="text-right">{{ $formatRp($subtotal) }}</td>
                            </tr>
                            <tr>
                                <th class="text-right">Ongkos Kirim :</th>
                                <td class="text-right">{{ $formatRp($shipping) }}</td>
                            </tr>
                            @if ($discount > 0)
                                <tr>
                                    <th class="text-right">Diskon Kupon :</th>
                                    <td class="text-right text-danger">- {{ $formatRp($discount) }}</td>
                                </tr>
                            @endif
                            <tr style="font-size: 1.1em;">
                                <th class="text-right">Total :</th>
                                <td class="text-right font-weight-bold">{{ $formatRp($total) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- Tombol Aksi Tambahan (jika diperlukan) --}}
                {{-- Contoh: Tombol untuk konfirmasi penerimaan jika status 'delivered' --}}
                {{--
            @if ($order->status == 'delivered')
                <div class="mt-4">
                     <form action="{{ route('user.order.confirm_receipt', $order->id) }}" method="POST">
                         @csrf
                         <button type="submit" class="btn btn-success">Konfirmasi Pesanan Diterima</button>
                     </form>
                </div>
            @endif
            --}}
            @else
                <p class="text-center">Detail pesanan tidak ditemukan.</p>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    {{-- Anda bisa memindahkan style ini ke file CSS terpisah jika mau --}}
    <style>
        .table-borderless th,
        .table-borderless td {
            border: none !important;
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
        }

        .card-body h5 {
            color: #5a5c69;
            /* Sesuaikan dengan warna tema Anda */
        }
    </style>
@endpush
