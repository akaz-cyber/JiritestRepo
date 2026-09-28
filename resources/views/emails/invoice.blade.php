@php
    $formatRp = fn($n) => 'Rp' . number_format($n, 0, ',', '.');

    $subtotal = (float) ($order->sub_total ?? 0);
    $shipping = (float) ($order->delivery_charge ?? 0);
    $discount = (float) ($order->coupon ?? 0);
    $total = (float) ($order->total_amount ?? $subtotal + $shipping - $discount);
    $totalQty = $order->cart ? $order->cart->sum('quantity') ?? 0 : 0;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice Pesanan</title>
</head>

<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0;">
<tr>
<td align="center">

<table width="700" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;padding:30px;">

<tr>
<td>

<!-- HEADER -->
<h2 style="margin:0 0 15px 0;color:#092327;">
Invoice Transaksi #{{ $order->order_number }}
</h2>

<p style="margin:0 0 10px 0;font-size:14px;">
Halo <strong>{{ $order->first_name }} {{ $order->last_name }}</strong>,
</p>

<p style="margin:0 0 10px 0;font-size:14px;">
Terima kasih telah melakukan transaksi di <strong>{{ config('app.name') }}</strong>.
Pesanan Anda dengan nomor transaksi 
<strong>#{{ $order->order_number }}</strong>
telah berhasil diproses dan pembayaran telah kami terima.
</p>

<p style="margin:0 0 20px 0;font-size:14px;">
Berikut kami sertakan detail invoice untuk transaksi Anda:
</p>

<hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

<!-- INFORMASI PESANAN -->
<h3 style="margin-bottom:10px;">Informasi Pesanan</h3>

<table width="100%" cellpadding="5" cellspacing="0" style="font-size:14px;">
<tr>
<td width="40%">Tanggal Pesanan</td>
<td>: {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
</tr>
<tr>
<td>Status Pembayaran</td>
<td>: <strong style="color:green;">LUNAS</strong></td>
</tr>
<tr>
<td>Metode Pembayaran</td>
<td>: {{ strtoupper($order->payment_method) }}</td>
</tr>
<tr>
<td>Kurir / Layanan</td>
<td>:
    @if(strtolower($order->payment_method) == 'cod')
        Bayar di Tempat
    @else
        {{ $order->shipping_courier ?? '-' }}
        {{ $order->shipping_service ? '(' . $order->shipping_service . ')' : '' }}
    @endif
</td>
</tr>
</table>

<br>

<!-- INFORMASI PENERIMA -->
<h3 style="margin-bottom:10px;">Informasi Penerima</h3>

<table width="100%" cellpadding="5" cellspacing="0" style="font-size:14px;">
<tr>
<td width="40%">Nama</td>
<td>: {{ $order->first_name }} {{ $order->last_name }}</td>
</tr>
<tr>
<td>Email</td>
<td>: {{ $order->email }}</td>
</tr>
<tr>
<td>Telepon</td>
<td>: {{ $order->phone ?? '-' }}</td>
</tr>
<tr>
<td>Alamat</td>
<td>: {{ $order->address1 ?? '-' }}</td>
</tr>
<tr>
<td>Kota</td>
<td>: {{ $order->city_name ?? '-' }}</td>
</tr>
<tr>
<td>Provinsi</td>
<td>: {{ $order->province_name ?? '-' }}</td>
</tr>
<tr>
<td>Kode Pos</td>
<td>: {{ $order->post_code ?? '-' }}</td>
</tr>
</table>

<br>

<!-- DETAIL BARANG -->
<h3 style="margin-bottom:10px;">Detail Barang</h3>

<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
<tr style="background:#f0f2f5;">
<th align="left">Produk</th>
<th align="center">Qty</th>
<th align="right">Harga</th>
<th align="right">Subtotal</th>
</tr>

@foreach($order->cart as $item)
@php
    $productName = $item->product->title ?? 'Produk Dihapus';
    $unitPrice = (float) ($item->price ?? 0);
    $quantity = (int) ($item->quantity ?? 0);
    $lineTotal = (float) ($item->amount ?? $unitPrice * $quantity);
@endphp
<tr style="border-bottom:1px solid #eee;">
<td>{{ $productName }}</td>
<td align="center">{{ $quantity }}</td>
<td align="right">{{ $formatRp($unitPrice) }}</td>
<td align="right">{{ $formatRp($lineTotal) }}</td>
</tr>
@endforeach

</table>

<br>

<!-- RINGKASAN -->
<table width="100%" cellpadding="6" cellspacing="0" style="font-size:14px;">
<tr>
<td align="right">Subtotal ({{ $totalQty }} item)</td>
<td align="right" width="150">{{ $formatRp($subtotal) }}</td>
</tr>
<tr>
<td align="right">Ongkos Kirim</td>
<td align="right">{{ $formatRp($shipping) }}</td>
</tr>

@if($discount > 0)
<tr>
<td align="right" style="color:red;">Diskon</td>
<td align="right" style="color:red;">- {{ $formatRp($discount) }}</td>
</tr>
@endif

<tr style="font-size:16px;font-weight:bold;">
<td align="right">TOTAL</td>
<td align="right">{{ $formatRp($total) }}</td>
</tr>
</table>

<br><br>

<!-- PENUTUP -->
<p style="font-size:14px;">
Pesanan Anda sedang dipersiapkan dan akan segera dikirim sesuai metode pengiriman yang dipilih.
</p>

<p style="font-size:14px;">
Jika Anda memiliki pertanyaan terkait pesanan ini, silakan balas email ini atau hubungi layanan pelanggan kami.
</p>

<br>

<p style="font-size:13px;color:#777;">
Salam hangat,<br>
<strong>Tim {{ config('app.name') }}</strong>
</p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>