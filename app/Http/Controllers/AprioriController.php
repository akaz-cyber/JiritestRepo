<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AprioriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $viewType = $request->get('view', 'variant');

        $rules = DB::table('association_rules as ar')
            ->join('product_variants as pv_ant', 'ar.antecedent_variant_id', '=', 'pv_ant.id')
            ->join('products as p_ant', 'pv_ant.product_id', '=', 'p_ant.id')
            ->join('product_variants as pv_cons', 'ar.consequent_variant_id', '=', 'pv_cons.id')
            ->join('products as p_cons', 'pv_cons.product_id', '=', 'p_cons.id')
            ->select(
                'ar.id',
                'p_ant.title as antecedent_product',
                'pv_ant.variant_name as antecedent_variant',
                'p_cons.title as consequent_product',
                'pv_cons.variant_name as consequent_variant',
                'ar.support',
                'ar.confidence',
                'ar.lift'
            )
            ->orderBy('ar.confidence', 'desc')
            ->get();

        if ($viewType === 'product') {
            $rules = collect($rules)->unique(function (object $rule) {
                return $rule->antecedent_product . '_' . $rule->consequent_product;
            })->values();
        }

        return view('backend.apriori.index', compact('rules', 'viewType'));
    }


    public function setapriori()
    {

        $clean_datasets = DB::table('clean_dataset_apriori')->paginate(100);

        return view('backend.apriori.setapriori', compact('clean_datasets'));
    }

    public function tambahdataset()
    {
        $datasets = DB::table('apriori_datasets')->paginate(5000);
        return view('backend.apriori.tambahdataset', compact('datasets'));
    }

    // public function uploadDataset(Request $request)
    // {
    //     $request->validate([
    //         'file_csv' => 'required|mimes:csv,txt|max:10240',
    //     ]);

    //     $file = $request->file('file_csv');
    //     $handle = fopen($file->path(), 'r');
    //     $header = fgetcsv($handle, 1000, ';');
    //     $delimiter = ';';
    //     if (count($header) <= 1) {
    //         rewind($handle);
    //         $header = fgetcsv($handle, 1000, ',');
    //         $delimiter = ',';
    //     }

    //     $dataToInsert = [];
    //     while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
    //         $dataToInsert[] = [
    //             'order_id'         => $row[0] ?? null,
    //             'index_id'         => $row[1] ?? null,
    //             'item_description' => $row[2] ?? null,
    //             'harga_satuan'     => $row[3] ?? null,
    //             'qty'              => $row[4] ?? null,
    //             'harga_total'      => $row[5] ?? null,
    //             'berat'            => $row[6] ?? null,
    //             'berat_total'      => $row[7] ?? null,
    //             'lokasi'           => $row[8] ?? null,
    //         ];
    //         if (count($dataToInsert) >= 500) {
    //             DB::table('apriori_datasets')->insert($dataToInsert);
    //             $dataToInsert = [];
    //         }
    //     }
    //     if (!empty($dataToInsert)) {
    //         DB::table('apriori_datasets')->insert($dataToInsert);
    //     }

    //     fclose($handle);

    //     return redirect()->back()->with('success', 'Dataset CSV berhasil diupload dan disimpan ke database!');
    // }


    public function uploadDataset(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt|max:10240', // Maksimal 10MB
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->path(), 'r');

        // Deteksi pemisah CSV (bisa koma ',' atau titik koma ';')
        $header = fgetcsv($handle, 1000, ';');
        $delimiter = ';';
        if (count($header) <= 1) {
            rewind($handle);
            $header = fgetcsv($handle, 1000, ',');
            $delimiter = ',';
        }

        // --- TAMBAHKAN KODE INI UNTUK MENGHAPUS DATA LAMA (TRUNCATE) ---
        // Proses ini akan mereset tabel dan ID kembali dari 1
        DB::table('apriori_datasets')->truncate();

        $dataToInsert = [];

        // Mulai looping baris CSV (header akan terlewat karena sudah dibaca di atas)
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {

            // Lewati jika baris kosong
            if (empty(array_filter($row))) {
                continue;
            }

            $dataToInsert[] = [
                'order_id'         => $row[0] ?? null,
                'index_id'         => $row[1] ?? null,
                'item_description' => $row[2] ?? null,
                'harga_satuan'     => (isset($row[3]) && trim($row[3]) !== '') ? (int) preg_replace('/[^0-9]/', '', $row[3]) : null,
                'qty'              => (isset($row[4]) && trim($row[4]) !== '') ? (int) preg_replace('/[^0-9]/', '', $row[4]) : null,
                'harga_total'      => (isset($row[5]) && trim($row[5]) !== '') ? (int) preg_replace('/[^0-9]/', '', $row[5]) : null,
                'berat'            => (isset($row[6]) && trim($row[6]) !== '') ? (int) preg_replace('/[^0-9]/', '', $row[6]) : null,
                'berat_total'      => (isset($row[7]) && trim($row[7]) !== '') ? (int) preg_replace('/[^0-9]/', '', $row[7]) : null,
                'lokasi'           => $row[8] ?? null,
            ];

            // Insert per 500 baris agar memori tidak penuh
            if (count($dataToInsert) >= 500) {
                DB::table('apriori_datasets')->insert($dataToInsert);
                $dataToInsert = [];
            }
        }

        // Insert sisa data yang kurang dari 500
        if (!empty($dataToInsert)) {
            DB::table('apriori_datasets')->insert($dataToInsert);
        }

        fclose($handle);

        return redirect()->back()->with('success', 'Data lama berhasil dihapus. Dataset CSV baru berhasil diupload!');
    }

    public function cleanDataset()
    {
        try {
            // 1. Kosongkan tabel tujuan
            DB::table('clean_dataset_apriori')->truncate();

            // 2. Jalankan Query Cleaning langsung di database
            // Cara ini sangat cepat (hanya hitungan detik) untuk 55k data
            DB::statement("
                INSERT INTO clean_dataset_apriori (OrderID, product_variant_id, created_at, updated_at)
                SELECT
                    d.order_id AS OrderID,
                    v.id AS product_variant_id,
                    NOW(),
                    NOW()
                FROM apriori_datasets d
                JOIN product_variants v
                    ON TRIM(d.index_id) = TRIM(v.sku)
                JOIN products p
                    ON v.product_id = p.id
                WHERE d.order_id IS NOT NULL AND d.order_id != ''
            ");

            return redirect()->back()->with('success', 'Berhasil! Data telah dibersihkan dan dipindahkan ke tabel clean_dataset_apriori.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal cleaning data: ' . $e->getMessage());
        }
    }

    // public function generateApriori()
    // {
    //     $scriptPath = base_path('python_script\apriori_jirifarm.py');
    //     $pythonPath = 'C:\Users\Administrator\AppData\Local\Programs\Python\Python314\python.exe';
    //     $process = new Process([$pythonPath, $scriptPath]);
    //     $process->setTimeout(600);

    //     try {
    //         $process->run();
    //         if (!$process->isSuccessful()) {
    //             $errorOutput = $process->getErrorOutput();
    //             return redirect()->route('apriori.index')->with('error', 'Error dari Python: ' . $errorOutput);
    //         }

    //         return redirect()->route('apriori.index')->with('success', 'Algoritma Apriori berhasil dijalankan ulang dengan data transaksi terbaru!');

    //     } catch (\Exception $e) {
    //         return redirect()->route('apriori.index')->with('error', 'Gagal mengeksekusi script: ' . $e->getMessage());
    //     }
    // }

    public function generateApriori()
    {
        // Agar proses tidak timeout dan memori cukup saat menghitung algoritma
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '-1');

        try {
            //  Ambil data dari tabel clean_dataset_apriori & product_variants
            $rawData = DB::table('clean_dataset_apriori as d')
                ->join('product_variants as v', 'd.product_variant_id', '=', 'v.id')
                ->select('d.OrderID', 'v.product_id')
                ->distinct()
                ->get();

            if ($rawData->isEmpty()) {
                return redirect()->route('apriori.index')->with('error', 'Dataset bersih kosong. Silakan jalankan Clean Dataset terlebih dahulu.');
            }

            // Bentuk basket
            $transactions = []; // Menyimpan produk per OrderID
            $itemCounts = [];   // Menghitung frekuensi tiap produk muncul

            foreach ($rawData as $row) {
                $transactions[$row->OrderID][] = $row->product_id;
                $itemCounts[$row->product_id] = ($itemCounts[$row->product_id] ?? 0) + 1;
            }

            $totalOrders = count($transactions);

            // Hitung frekuensi kemunculan pasangan item (Kombinasi 2 Produk)
            $pairCounts = [];
            foreach ($transactions as $items) {
                $count = count($items);
                if ($count < 2) continue;

                sort($items);
                for ($i = 0; $i < $count - 1; $i++) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        if ($items[$i] != $items[$j]) {
                            $key = $items[$i] . '_' . $items[$j];
                            $pairCounts[$key] = ($pairCounts[$key] ?? 0) + 1;
                        }
                    }
                }
            }
            // $supportValues = [0.01, 0.02, 0.05];
            // $confidenceValues = [0.3, 0.5, 0.7];
            $supportValues = [0.001, 0.002, 0.005, 0.01];
            $confidenceValues = [0.2, 0.3, 0.5, 0.7];

            $bestRules = [];
            $maxRules = 0;
            $bestParams = '';

            foreach ($supportValues as $minSup) {
                foreach ($confidenceValues as $minConf) {
                    $currentRules = [];

                    foreach ($pairCounts as $pairKey => $pairCount) {
                        list($p1, $p2) = explode('_', $pairKey);

                        $supportPair = $pairCount / $totalOrders;

                        if ($supportPair >= $minSup) {
                            $supportP1 = $itemCounts[$p1] / $totalOrders;
                            $conf1to2 = $supportPair / $supportP1;
                            $supportP2 = $itemCounts[$p2] / $totalOrders;
                            $lift1to2 = $conf1to2 / $supportP2;

                            if ($conf1to2 >= $minConf && $lift1to2 > 1) {
                                $currentRules[] = [
                                    'ant' => $p1, 'con' => $p2,
                                    'sup' => $supportPair, 'conf' => $conf1to2, 'lift' => $lift1to2
                                ];
                            }
                            $conf2to1 = $supportPair / $supportP2;
                            $lift2to1 = $conf2to1 / $supportP1;

                            if ($conf2to1 >= $minConf && $lift2to1 > 1) {
                                $currentRules[] = [
                                    'ant' => $p2, 'con' => $p1,
                                    'sup' => $supportPair, 'conf' => $conf2to1, 'lift' => $lift2to1
                                ];
                            }
                        }
                    }

                    // Ambil parameter yang menghasilkan rule terbanyak
                    if (count($currentRules) > $maxRules) {
                        $maxRules = count($currentRules);
                        $bestRules = $currentRules;
                        $bestParams = "min_support={$minSup}, min_confidence={$minConf}";
                    }
                }
            }

            if ($maxRules === 0) {
                return redirect()->route('apriori.index')->with('error', 'Tidak ada aturan asosiasi yang memenuhi syarat minimum parameter.');
            }

            //  Menyebarkan Aturan ke Level Varian dan Menyimpan ke Database
            DB::table('association_rules')->truncate();

            // Ambil semua mapping varian produk
            $variants = DB::table('product_variants')->select('id', 'product_id')->get()->groupBy('product_id');

            $dataToInsert = [];
            foreach ($bestRules as $rule) {
                $antVariants = $variants->get($rule['ant']) ?? [];
                $conVariants = $variants->get($rule['con']) ?? [];

                foreach ($antVariants as $vAnt) {
                    foreach ($conVariants as $vCon) {
                        $dataToInsert[] = [
                            'antecedent_variant_id' => $vAnt->id,
                            'consequent_variant_id' => $vCon->id,
                            'support'               => $rule['sup'],
                            'confidence'            => $rule['conf'],
                            'lift'                  => $rule['lift'],
                            'created_at'            => now(),
                            'updated_at'            => now(),
                        ];
                    }
                }

                // Insert bertahap agar tidak over memory
                if (count($dataToInsert) >= 500) {
                    DB::table('association_rules')->insert($dataToInsert);
                    $dataToInsert = [];
                }
            }

            if (!empty($dataToInsert)) {
                DB::table('association_rules')->insert($dataToInsert);
            }

            return redirect()->route('apriori.index')->with('success', "Algoritma Apriori selesai! Parameter terbaik: {$bestParams}. Total {$maxRules} produk terkait ditemukan.");

        } catch (\Exception $e) {
            return redirect()->route('apriori.index')->with('error', 'Terjadi kesalahan saat menghitung algoritma: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
