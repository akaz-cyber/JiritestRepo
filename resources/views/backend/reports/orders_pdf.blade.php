<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Order ({{ strtoupper($group) }}) {{ $from->format('d M Y') }} - {{ $to->format('d M Y') }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color:#333; }
    table { width:100%; border-collapse: collapse; }
    th, td { padding: 6px 8px; border:1px solid #ddd; }
    thead th { background:#f5f5f5; }
    .text-right { text-align:right; }
    .mb-2 { margin-bottom: 10px; }
  </style>
</head>
<body>
  <h3 class="mb-2">Laporan Order ({{ ucfirst($group) }})</h3>
  <div class="mb-2">Periode: {{ $from->format('d M Y') }} s/d {{ $to->format('d M Y') }}</div>

  <table>
    <thead>
      <tr>
        <th>Periode</th>
        <th class="text-right">Jumlah Order</th>
        <th class="text-right">Subtotal</th>
        <th class="text-right">Ongkir</th>
        <th class="text-right">Diskon</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data['rows'] as $row)
        <tr>
          <td>{{ $row['label'] }}</td>
          <td class="text-right">{{ number_format($row['orders_count'],0,',','.') }}</td>
          <td class="text-right">Rp{{ number_format($row['subtotal'],0,',','.') }}</td>
          <td class="text-right">Rp{{ number_format($row['shipping'],0,',','.') }}</td>
          <td class="text-right">Rp{{ number_format($row['discount'],0,',','.') }}</td>
          <td class="text-right"><strong>Rp{{ number_format($row['total'],0,',','.') }}</strong></td>
        </tr>
      @endforeach
      <tr>
        <th>TOTAL</th>
        <th class="text-right">{{ number_format($data['totals']['orders_count'],0,',','.') }}</th>
        <th class="text-right">Rp{{ number_format($data['totals']['subtotal'],0,',','.') }}</th>
        <th class="text-right">Rp{{ number_format($data['totals']['shipping'],0,',','.') }}</th>
        <th class="text-right">Rp{{ number_format($data['totals']['discount'],0,',','.') }}</th>
        <th class="text-right">Rp{{ number_format($data['totals']['total'],0,',','.') }}</th>
      </tr>
    </tbody>
  </table>
</body>
</html>
