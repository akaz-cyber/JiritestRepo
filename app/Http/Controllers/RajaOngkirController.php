<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RajaOngkirController extends Controller
{
    protected function komerceRequest()
    {
        return Http::withHeaders([
            'key' => config('rajaongkir.api_key')
        ])->baseUrl(config('rajaongkir.base_url'));
    }

    public function getProvinces()
    {
        $provinces = Cache::remember('rajaongkir_provinces', 60 * 24, function () {
            try {
                $response = $this->komerceRequest()->get('/api/v1/destination/province');
                if ($response->successful()) {
                    return $response->json()['data'];
                }
                return [];
            } catch (\Exception $e) {
                Log::error('Gagal mengambil data provinsi dari API: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json($provinces);
    }


    //     try {
    //         $response = $this->komerceRequest()->get('/api/v1/destination/province');

    //         Log::info('API Response Status: ' . $response->status());

    //         Log::info('API Response Body: ' . $response->body());
    //         if ($response->successful()) {
    //             $provinces = $response->json()['data'];
    //             return response()->json($provinces);
    //         }

    //         return response()->json([], $response->status());

    //     } catch (\Exception $e) {
    //         Log::error('Gagal terhubung ke API Komerce: ' . $e->getMessage());
    //         return response()->json(['error' => 'Gagal terhubung ke server API.'], 500);
    //     }
    // }

    /**
     * Mengambil daftar kota berdasarkan ID provinsi.
     */
    public function getCities($province_id)
    {

        $cacheKey = 'rajaongkir_cities_province_' . $province_id;

        $cities = Cache::remember($cacheKey, 60 * 24, function () use ($province_id) {
            try {
                $response = $this->komerceRequest()->get('/api/v1/destination/city/' . $province_id);
                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
                return [];
            } catch (\Exception $e) {
                Log::error('Gagal mengambil data kota dari API untuk provinsi ' . $province_id . ': ' . $e->getMessage());
                return [];
            }
        });

        return response()->json($cities);
        // try {

        //     $response = $this->komerceRequest()->get('/api/v1/destination/city/' . $province_id);


        //     Log::info('Get Cities for Province ID ' . $province_id . ': Status ' . $response->status());
        //     Log::info('Get Cities Response Body: ' . $response->body());

        //     if ($response->successful()) {
        //         $cities = $response->json()['data'] ?? [];
        //         return response()->json($cities);
        //     }

        //     return response()->json([], $response->status());

        // }  catch (\Exception $e) {
        //     Log::error('Gagal mengambil data kota: ' . $e->getMessage());
        //     return response()->json(['error' => 'Gagal mengambil data kota.'], 500);
        // }
    }

    public function getDistricts($city_id)
    {
        $cacheKey = 'rajaongkir_districts_city_' . $city_id;
        $districts = Cache::remember($cacheKey, 60 * 24, function () use ($city_id) {
            try {
                $response = $this->komerceRequest()->get('/api/v1/destination/district/' . $city_id);
                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
                return [];
            } catch (\Exception $e) {
                Log::error('Gagal mengambil data kecamatan: ' . $e->getMessage());
                return [];
            }
        });
        return response()->json($districts);
    }

    public function getSubDistricts($district_id)
    {
        // Kita gunakan cache agar tidak selalu request ke API
        $cacheKey = 'rajaongkir_subdistricts_district_' . $district_id;

        $subdistricts = Cache::remember($cacheKey, 60 * 24, function () use ($district_id) {
            try {
                // Panggil API endpoint yang Anda berikan
                $response = $this->komerceRequest()->get('/api/v1/destination/sub-district/' . $district_id);

                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
                return [];
            } catch (\Exception $e) {
                Log::error('Gagal mengambil data kelurahan (sub-district) dari API: ' . $e->getMessage());
                return [];
            }
        });

        return response()->json($subdistricts);
    }

    /**
     * Menghitung ongkos kirim berdasarkan tujuan, berat, dan kurir.
     */
    public function checkOngkir(Request $request)
    {
        $request->validate([
            'destination_district_id' => 'required|integer',
            'courier' => 'required|string',
            'destination_city_name' => 'nullable|string',
            'destination_district_name' => 'nullable|string',
        ]);

        $total_weight = 0;
        $carts = Cart::with('variant')
        ->where('user_id', auth()->user()->id)
        ->where('is_checked', true)
        ->whereNull('order_id')
        ->get();

        foreach ($carts as $cart) {
            $total_weight += $cart->line_weight_gram;
        }
        if ($total_weight <= 0) {
            $total_weight = 1000; // Default 1kg (1000 gram)
        }

        // (Log debug dari chat sebelumnya)
     $origin_id = config('rajaongkir.origin_city_id');
    $origin_name = 'TANGERANG (dari .env)';
    $destination_district_id = $request->destination_district_id;
    $destination_district_name = $request->destination_district_name;
    $destination_city_name = $request->destination_city_name;
    Log::info('===== DEBUG API REQUEST ONGKIR (DISTRICT) =====');
    Log::info('MENGIRIM DARI: ' . $origin_name . ' (ID: ' . $origin_id . ')');
    Log::info('MENGIRIM KE  : ' . $destination_city_name . ' - ' . $destination_district_name . ' (ID: ' . $destination_district_id . ')');
    Log::info('===============================================');

    $response = $this->komerceRequest()
    ->asForm()
    ->post('https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost', [
        'origin'        => $origin_id,
        'destination'   => $destination_district_id,
        'weight'        => $total_weight,
        'courier'       => strtolower($request->courier),
    ]);

        Log::info('Check Ongkir Response: ' . $response->body());

        $costs = $response->successful() && isset($response->json()['data'])
                 ? $response->json()['data']
                 : [];

        $formattedCosts = [
            [
                "code" => strtoupper($request->courier),
                "name" => strtoupper($request->courier),
                "costs" => $costs
            ]
        ];

        return response()->json($formattedCosts);
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'waybill' => 'required|string',
            'courier' => 'required|string'
        ]);

        $resi       = trim($request->waybill);
        $courierRaw = strtolower(trim($request->courier));
        $courier    = ($courierRaw === 'j&t' || $courierRaw === 'jnt') ? 'jnt' : $courierRaw;

        // Gunakan API Key BinderByte kamu
        $apiKey = env('BINDERBYTE_API_KEY', 'sk_dbgjcpwmlqsrmcnjmtiqmcsk5m9a7igsmrbjpfx58izxxtxyet1prqvhdvmdivvh');

        // Parameter dasar GET sesuai dokumentasi BinderByte
        $queryParams = [
            'api_key' => $apiKey,
            'courier' => $courier,
            'awb'     => $resi
        ];

        // Khusus JNE: Sertakan parameter 'number' (5 digit terakhir nomor HP)
        if ($courier === 'jne') {
            $order = \App\Models\Order::where('resi_number', $resi)->first();

            // Prioritaskan kolom jne_phone_verify dari admin, jika kosong ambil dari nomor telepon pembeli
            if ($order && !empty($order->jne_phone_verify)) {
                $queryParams['number'] = trim($order->jne_phone_verify);
            } elseif ($order && !empty($order->phone)) {
                $phoneOnlyNumbers = preg_replace('/[^0-9]/', '', $order->phone);
                $queryParams['number'] = strlen($phoneOnlyNumbers) >= 5
                    ? substr($phoneOnlyNumbers, -5)
                    : $phoneOnlyNumbers;
            } else {
                $queryParams['number'] = '';
            }
        }

        try {
            // Panggil endpoint resmi BinderByte menggunakan HTTP GET
            $response = Http::get('https://api.binderbyte.com/v1/track', $queryParams);
            $result   = $response->json();

            // Jika respons dari BinderByte sukses (status 200)
            if ($response->successful() && isset($result['status']) && $result['status'] == 200) {

                $formattedHistory = [];
                if (isset($result['data']['history']) && is_array($result['data']['history'])) {
                    foreach ($result['data']['history'] as $item) {
                        $formattedHistory[] = [
                            'desc' => $item['desc'] ?? '-',
                            'date' => $item['date'] ?? ''
                        ];
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'data'   => [
                        'airway_bill' => $result['data']['summary']['awb'] ?? $resi,
                        'courier'     => $result['data']['summary']['courier'] ?? strtoupper($courier),
                        'last_status' => $result['data']['summary']['status'] ?? 'DELIVERED',
                        'history'     => $formattedHistory
                    ]
                ], 200);
            }

            return response()->json([
                'status'  => 'error',
                'message' => $result['message'] ?? 'Data resi belum tersedia atau nomor resi tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('BinderByte Error: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal terhubung ke server pelacakan ekspedisi.'
            ], 500);
        }
    }


}
