<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .wrapper {
            max-width: 100%;
        }

        .clearfix:after {
            content: "";
            display: block;
            clear: both;
        }

        .header {
            margin-bottom: 20px;
        }

        .left {
            float: left;
            width: 50%;
        }

        .right {
            float: right;
            width: 50%;
            text-align: right;
        }

        h2,
        h3,
        h4 {
            margin: 0 0 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }

        thead th {
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #777;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        {{-- Header --}}
        <div class="header clearfix">
            <div class="left">
                <h2>INVOICE</h2>
                <div>No: <strong>{{ $order->order_number }}</strong></div>
                <div>Tanggal: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</div>
            </div>
            <div class="right">
                <h3><img src="backend/img/logojirifarm.png" alt="logo" style="max-height: 50px;"></h3>
                <div class="muted">{{ config('app.url') }}</div>
            </div>
        </div>

        {{-- Address --}}
        <div class="clearfix" style="margin-bottom:16px;">
            <div class="left">
                <h4>Kepada</h4>
                <div>{{ $order->first_name }} {{ $order->last_name }}</div>
                <div>{{ $order->address1 }}</div>
                <div>{{ $order->city_name }}, {{ $order->province_name }} {{ $order->post_code }}</div>
                <div>{{ $order->email }} {{ $order->phone ? ' | ' . $order->phone : '' }}</div>
            </div>
            <div class="right">
                <h4>Pengiriman</h4>
                @if (strtolower($order->payment_method) == 'cod')
                    <div><strong>Metode:</strong> Ambil di Tempat (COD)</div>
                    <div><strong>Layanan:</strong> Bayar di Tempat</div>
                @else
                    <div>Kurir: {{ strtoupper($order->shipping_courier ?? '-') }}</div>
                    <div>Layanan: {{ $order->shipping_service ?? '-' }}</div>
                @endif
            </div>
        </div>

        {{-- Items --}}
        <table>
            <thead>
                <tr>
                    <th style="width:160px;">Produk</th>
                    <th style="width:160px;">Varian</th>
                    <th class="text-center" style="width:50px;">Qty</th>
                    <th class="text-right" style="width:70px;">Harga</th>
                    <th class="text-right" style="width:70px;">Total</th>
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
                        <td style="white-space: pre-wrap;">{{ $cart->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary --}}
        @php
            $subtotal = (float) ($order->sub_total ?? 0);
            $ongkir = (float) ($order->delivery_charge ?? 0); // <= tidak pakai $order->shipping->price
            $diskon = (float) ($order->coupon ?? 0);
            $total = (float) ($order->total_amount ?? $subtotal + $ongkir - $diskon);
        @endphp

        <table style="margin-top:10px;">
            <tr>
                <td style="border:none; width:60%"></td>
                <th class="text-right" style="width:20%;">Subtotal :</th>
                <td class="text-right" style="width:20%;">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border:none;"></td>
                <th class="text-right">Ongkir :</th>
                <td class="text-right">Rp{{ number_format($ongkir, 0, ',', '.') }}</td>
            </tr>
            @if ($diskon > 0)
                <tr>
                    <td style="border:none;"></td>
                    <th class="text-right">Diskon :</th>
                    <td class="text-right">- Rp{{ number_format($diskon, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr>
                <td style="border:none;"></td>
                <th class="text-right">Total :</th>
                <td class="text-right"><strong>Rp{{ number_format($total, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <p class="muted" style="margin-top:16px;">Terima kasih telah berbelanja.</p>
    </div>
</body>

</html>
