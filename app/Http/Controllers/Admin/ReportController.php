<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReportController extends Controller
{
    // Opsional: Tampilkan pratinjau HTML
    public function preview(Request $request)
    {
        [$from, $to, $group] = $this->resolveRange($request);
        $data = $this->queryData($from, $to, $group);

        return view('backend.reports.orders_preview', [
            'from' => $from, 'to' => $to, 'group' => $group, 'data' => $data,
        ]);
    }

    // Export PDF / Excel
    public function export(Request $request)
    {
        $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date',
            'group'  => 'required|in:daily,weekly,monthly',
            'format' => 'required|in:pdf,xlsx,csv',
        ]);

        [$from, $to, $group] = $this->resolveRange($request);
        $data = $this->queryData($from, $to, $group);

        if ($request->format === 'pdf') {
            // PDF
            $pdf = PDF::loadView('backend.reports.orders_pdf', [
                'from' => $from, 'to' => $to, 'group' => $group, 'data' => $data,
            ])->setPaper('a4','portrait');

            $filename = "laporan-order_{$group}_{$from->format('Ymd')}-{$to->format('Ymd')}.pdf";
            return $pdf->download($filename);
        }

        // CSV/XLSX (tanpa paket Excel, kirim CSV sederhana)
        $filename = "laporan-order_{$group}_{$from->format('Ymd')}-{$to->format('Ymd')}.{$request->format}";
        $headers = [
            'Content-Type'        => $request->format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // Buat CSV sederhana
        $callback = function() use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Periode', 'Jumlah Order', 'Subtotal', 'Ongkir', 'Diskon', 'Total']);
            foreach ($data['rows'] as $row) {
                fputcsv($out, [
                    $row['label'], $row['orders_count'], $row['subtotal'], $row['shipping'], $row['discount'], $row['total'],
                ]);
            }
            // total baris terakhir
            fputcsv($out, ['TOTAL', $data['totals']['orders_count'], $data['totals']['subtotal'], $data['totals']['shipping'], $data['totals']['discount'], $data['totals']['total']]);
            fclose($out);
        };

        // NB: Untuk XLSX sungguhan, sebaiknya pakai maatwebsite/excel; ini shortcut CSV.
        return response()->stream($callback, 200, $headers);
    }

    private function resolveRange(Request $request)
    {
        // Default: bulan ini
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfMonth();
        $to   = $request->filled('to')   ? Carbon::parse($request->to)->endOfDay()   : now()->endOfDay();
        $group = $request->input('group', 'monthly'); // daily|weekly|monthly
        return [$from, $to, $group];
    }

    private function queryData(Carbon $from, Carbon $to, string $group){
        // Hanya order yang sudah delivered (final & masuk pemasukan)
        $q = Order::whereBetween('created_at', [$from, $to])
            ->where('status', 'delivered');   // ← filter utama

        // Tentukan label grouping
        switch ($group) {
            case 'daily':
                $label = DB::raw("DATE(created_at) as label_key");
                break;
            case 'weekly':
                $label = DB::raw("DATE_FORMAT(created_at, '%x-W%v') as label_key"); // ISO-Week
                break;
            default: // monthly
                $label = DB::raw("DATE_FORMAT(created_at, '%Y-%m') as label_key");
        }

        // Agregasi nilai (sesuaikan kolom sesuai skema tabelmu)
        $rows = $q->select([
                $label,
                DB::raw('COUNT(*) as orders_count'),
                DB::raw('SUM(COALESCE(sub_total,0)) as subtotal'),
                DB::raw('SUM(COALESCE(delivery_charge,0)) as shipping'),
                DB::raw('SUM(COALESCE(coupon,0)) as discount'),
                DB::raw('SUM(COALESCE(total_amount,0)) as total'),
            ])
            ->groupBy('label_key')
            ->orderBy('label_key')
            ->get();

        // Format label & casting ke array rapi
        $mapped = $rows->map(function($r) use ($group) {
            $labelText = $r->label_key;
            if ($group === 'daily') {
                $labelText = \Carbon\Carbon::parse($r->label_key)->format('d M Y');
            } elseif ($group === 'monthly') {
                $labelText = \Carbon\Carbon::createFromFormat('Y-m', $r->label_key)->format('M Y');
            }
            return [
                'label'        => $labelText,
                'orders_count' => (int) $r->orders_count,
                'subtotal'     => (int) $r->subtotal,
                'shipping'     => (int) $r->shipping,
                'discount'     => (int) $r->discount,
                'total'        => (int) $r->total,
            ];
        })->values()->all();

        $totals = [
            'orders_count' => array_sum(array_column($mapped, 'orders_count')),
            'subtotal'     => array_sum(array_column($mapped, 'subtotal')),
            'shipping'     => array_sum(array_column($mapped, 'shipping')),
            'discount'     => array_sum(array_column($mapped, 'discount')),
            'total'        => array_sum(array_column($mapped, 'total')),
        ];

        return ['rows' => $mapped, 'totals' => $totals];
    }


}