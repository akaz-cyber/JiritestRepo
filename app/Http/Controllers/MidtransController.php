<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Mail;

class MidtransController extends Controller
{
    public function notificationHandler(Request $request) // Inject Request object
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true; // Recommended
        Config::$is3ds = true; // Recommended

        // Buat instance notifikasi Midtrans DARI INPUT
        try {
            // Gunakan payload dari request yang di-inject Laravel
            // Library Midtrans akan membaca JSON dari sini
            $notification = new \Midtrans\Notification(); // <-- UBAH DI SINI

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Instantiation Error: ' . $e->getMessage() . ' Payload: ' . $request->getContent());
            return response()->json(['message' => 'Invalid notification data.'], 400);
        }

        // Log data notifikasi yang diterima (berguna untuk debug)
        // Convert object ke array agar bisa di-log
        Log::info('Midtrans Notification Received:', (array) $notification->getResponse());

        // Ambil order_id dan status transaksi dari notifikasi
        $order_number = $notification->order_id;
        $transaction_status = $notification->transaction_status;
        $fraud_status = $notification->fraud_status ?? null; // Fraud status mungkin tidak selalu ada

        // Cari order di database
        $order = Order::where('order_number', $order_number)->first();

        if (!$order) {
            Log::warning('Midtrans Notification: Order not found for order_number: ' . $order_number);
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Lakukan verifikasi signature key
        // Pastikan Anda mendapatkan nilai dari objek notifikasi yang sudah terisi
        $signature_key_valid = false;
        try {
            // Gunakan metode isValidSignatureKey dari library Midtrans jika tersedia
            // Jika tidak, gunakan perhitungan manual Anda
            if (method_exists($notification, 'isValidSignatureKey')) {
                 $signature_key_valid = $notification->isValidSignatureKey();
            } else {
                // Perhitungan manual (pastikan semua variabel ada nilainya)
                $statusCode = $notification->status_code ?? '';
                $grossAmount = $notification->gross_amount ?? '';
                $serverKey = config('midtrans.server_key');
                $input = $order_number . $statusCode . $grossAmount . $serverKey;
                $calculated_signature_key = hash("sha512", $input);

                Log::info('Midtrans Signature Check - Input: ' . $input);
                Log::info('Midtrans Signature Check - Calculated: ' . $calculated_signature_key);
                Log::info('Midtrans Signature Check - Received: ' . ($notification->signature_key ?? 'NULL'));

                $signature_key_valid = ($notification->signature_key == $calculated_signature_key);
            }

        } catch (\Exception $e) {
             Log::error('Midtrans Signature Verification Error: ' . $e->getMessage());
             return response()->json(['message' => 'Signature verification error.'], 500);
        }


        if (!$signature_key_valid) {
            Log::error('Midtrans Notification: Invalid signature for order_number: ' . $order_number);
            return response()->json(['message' => 'Invalid signature.'], 403);
        }
        Log::info('Midtrans Notification: Signature verified for order_number: ' . $order_number);


        // Update status order berdasarkan notifikasi
        // Gunakan DB Transaction untuk keamanan
        DB::beginTransaction();

        $shouldSendInvoice = false;

        try {

            if ($transaction_status == 'capture') {

                if ($fraud_status == 'accept') {

                    if ($order->payment_status !== 'paid') {

                        $order->payment_status = 'paid';
                        $order->status = 'new';
                        $order->save();

                        $shouldSendInvoice = true;

                        Log::info('Order ' . $order_number . ' marked as PAID (capture).');
                    }
                }

            } elseif ($transaction_status == 'settlement') {

                if ($order->payment_status !== 'paid') {

                    $order->payment_status = 'paid';
                    $order->status = 'new';
                    $order->save();

                    $shouldSendInvoice = true;

                    Log::info('Order ' . $order_number . ' marked as PAID (settlement).');
                }

            } elseif ($transaction_status == 'pending') {

                Log::info('Order ' . $order_number . ' is PENDING.');

            } elseif (in_array($transaction_status, ['deny', 'expire', 'cancel'])) {

                if ($order->payment_status !== 'paid') {

                    $order->payment_status = 'unpaid';
                    $order->status = 'cancel';
                    $order->save();

                    Log::info('Order ' . $order_number . ' marked as CANCELLED.');
                } else {

                    Log::warning('Received ' . $transaction_status . ' for already PAID order ' . $order_number);
                }
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Failed to update order ' . $order_number . ': ' . $e->getMessage());

            return response()->json(['message' => 'Failed to update order.'], 500);
        }

        // if ($shouldSendInvoice) {

        //     try {
        //         Mail::to($order->email)->send(new OrderInvoiceMail($order));
        //         Log::info('Invoice sent for order ' . $order_number);
        //     } catch (\Exception $e) {
        //         Log::error('Failed to send invoice for order ' . $order_number . ': ' . $e->getMessage());
        //     }
        // } catch(\Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to update order status for ' . $order_number . ': ' . $e->getMessage());
        //      return response()->json(['message' => 'Failed to update order status.'], 500);
        // }
        if ($shouldSendInvoice) {
            try {
                Mail::to($order->email)->send(new OrderInvoiceMail($order));
                Log::info('Invoice sent for order ' . $order_number);
            } catch (\Exception $e) {
                // Jika gagal kirim email, jangan batalkan update database (jangan rollback)
                // Cukup log error-nya saja
                Log::error('Failed to send invoice for order ' . $order_number . ': ' . $e->getMessage());
            }
        }

        return response()->json(['message' => 'Notification handled successfully.']);
    }
}
