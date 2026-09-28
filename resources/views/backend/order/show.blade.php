@extends('backend.layouts.master')

@section('main-content')
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Detail Pesanan: {{ $order->order_number }}</h6>
            {{-- <a href="{{ route('order.pdf', $order->id) }}" class="btn btn-sm btn-primary shadow-sm float-right"
                target="_blank">
                <i class="fas fa-download fa-sm text-white-50"></i> Cetak Invoice (PDF)
            </a> --}}
        </div>
        <div class="card-body">

            {{-- ====== ORDER & CUSTOMER INFO (tidak banyak diubah) ====== --}}
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">INFORMASI PESANAN</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width:180px;">No Order</th>
                            <td>: {{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: <span
                                    class="badge badge-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : 'warning') }}">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Metdoe Pembayaran</th>
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
                            <th>Waktu Pemesanan</th>
                            <td>: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
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
                                    <span class="badge badge-info">Bayar di Tempat</span>
                                @else
                                    {{ $order->shipping_courier ?? '-' }}
                                    {{ $order->shipping_service ? '(' . $order->shipping_service . ')' : '' }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ====== ITEMS (tabel baru tapi look & feel bootstrap standar) ====== --}}
            <h5 class="mb-3 mt-4">ITEMS</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Produk</th>
                            <th style="width:180px;">Varian</th>
                            <th class="text-center" style="width:90px;">Qty</th>
                            <th class="text-right" style="width:150px;">Harga</th>
                            <th class="text-right" style="width:160px;">Total</th>
                            <th>Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->cart_info as $cart)
                            @php
                                $produk = $cart->product->title ?? '-';
                                $varian = $cart->variant->variant_name ?? '-';
                                $unit = (float) ($cart->price ?? 0);
                                $qty = (int) ($cart->quantity ?? 0);
                                $line = (float) ($cart->computed_amount ?? $unit * $qty);
                            @endphp
                            <tr>
                                <td>{{ $produk }}</td>
                                <td>{{ $varian }}</td>
                                <td class="text-center">{{ $qty }}</td>
                                <td class="text-right">Rp{{ number_format($unit, 0, ',', '.') }}</td>
                                <td class="text-right">Rp{{ number_format($line, 0, ',', '.') }}</td>
                                <td style="max-width:360px; white-space: pre-wrap;">{{ $cart->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ====== RINGKASAN (pakai delivery_charge, bukan shipping->price) ====== --}}
            @php
                $subtotal = (float) ($order->sub_total ?? 0);
                $ongkir = (float) ($order->delivery_charge ?? 0);
                $diskon = (float) ($order->coupon ?? 0);
                $total = (float) ($order->total_amount ?? $subtotal + $ongkir - $diskon);
                $qtyAll = (int) ($order->cart_info->sum('quantity') ?? 0);
            @endphp

            <div class="row mt-3">
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th style="width:180px;">Total Barang</th>
                            <td>: {{ $qtyAll }}</td>
                        </tr>
                        {{-- Ganti jadi total berat next --}}
                        @php
                            $totalWeight = 0;
                            foreach ($order->cart_info as $cart) {
                                $totalWeight += ($cart->variant->weight ?? 0) * $cart->quantity;
                            }
                        @endphp

                        <tr>
                            <th>Total Berat</th>
                            <td>: {{ $totalWeight }} gram</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm">
                        <tr>
                            <th class="text-right" style="width:60%;">Subtotal :</th>
                            <td class="text-right">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th class="text-right">Ongkir :</th>
                            <td class="text-right">Rp{{ number_format($ongkir, 0, ',', '.') }}</td>
                        </tr>
                        @if ($diskon > 0)
                            <tr>
                                <th class="text-right">Diskon :</th>
                                <td class="text-right">- Rp{{ number_format($diskon, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th class="text-right">Total :</th>
                            <td class="text-right font-weight-bold">Rp{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- ====== ACTION BUTTONS (biarkan sesuai kebiasaan kamu) ====== --}}
            <div class="mt-3">
                <a href="{{ route('order.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
                <a href="{{ route('order.pdf', $order->id) }}" class="btn btn-primary btn-sm" target="_blank">Cetak Invoice
                    (PDF)</a>
                {{-- tombol lain seperti ubah status / hapus bisa tetap seperti semula --}}
            </div>

        </div>
    </div>
@endsection
