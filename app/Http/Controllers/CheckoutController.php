<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventTicketMail;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();
        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Habis
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik)
        $orderId = 'TRX-' . time() . '-' . Str::random(5);
        $totalPrice = $event->price + 5000; // Menambahkan biaya admin (dummy)

        // 4. Merekam Transaksi ke Database
        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => $orderId,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price' => $totalPrice,
            'status' => 'pending', // Status Awal
        ]);

        // --- INTEGRASI SNAP MIDTRANS ---
        // Konfigurasi Kredensial Environment Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        \Midtrans\Config::$isProduction = filter_var(config('midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // Susun Paket Array Data Transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            // Perintah Tembak Generate Snap Token
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Update rekaman kita bahwa transaksi terkait sudah memiliki id token pelunasan
            $transaction->update(['snap_token' => $snapToken]);

            // Redirect ke halaman antarmuka pembayaran final pelanggan
            return redirect()->route('checkout.payment', $transaction->order_id);
        } catch (\Exception $e) {
            // Log full exception for debugging
            Log::error('Midtrans Snap error', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
                'params' => $params,
            ]);

            // Clean up created transaction to avoid dangling pending records
            try {
                $transaction->delete();
            } catch (\Exception $ex) {
                Log::warning('Failed to delete transaction after Midtrans error', ['id' => $transaction->id, 'error' => $ex->getMessage()]);
            }

            return back()->with('error', 'Gagal memproses pembayaran: periksa `MIDTRANS_SERVER_KEY` dan `MIDTRANS_IS_PRODUCTION` pada .env. Detail: ' . $e->getMessage());
        }
    }

    public function payment($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        // Mengambil daftar kategori untuk keperluan menu footer
        $categories = \App\Models\Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        // Validasi status pembayaran asli dari Midtrans (Mencegah manipulasi URL)
        \Midtrans\Config::$serverKey = config('midtrans.server_key', env('MIDTRANS_SERVER_KEY', ''));
        \Midtrans\Config::$isProduction = filter_var(config('midtrans.is_production', env('MIDTRANS_IS_PRODUCTION', false)), FILTER_VALIDATE_BOOLEAN);

        try {
            $midtransStatus = \Midtrans\Transaction::status($order_id);

            // Hanya ubah status menjadi sukses jika Midtrans mengonfirmasi pembayaran lunas
            $transactionStatus = is_object($midtransStatus) ? ($midtransStatus->transaction_status ?? null) : null;

            if (in_array($transactionStatus, ['capture', 'settlement'], true)) {
                // Hanya lakukan pembaruan lokal jika status saat ini masih pending
                if (strtolower($transaction->status) === 'pending') {
                    $transaction->update(['status' => 'success']);

                    // Kurangi stock jika tersedia
                    if ($transaction->event && $transaction->event->stock > 0) {
                        $transaction->event->stock = max(0, $transaction->event->stock - 1);
                        $transaction->event->save();
                    } else {
                        Log::warning('Stock habis setelah pembayaran berhasil (fallback). Order: ' . $transaction->order_id);
                    }

                    // Kirim email E-Ticket secara manual (fallback)
                    try {
                        Mail::to($transaction->customer_email)->send(new EventTicketMail($transaction));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim email ETicket secara manual (Bypass): ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Midtrans status check failed', ['order_id' => $order_id, 'error' => $e->getMessage()]);
            // Jika error (transaksi tidak ada di Midtrans, koneksi terputus), kembalikan ke beranda
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran. Periksa server key dan koneksi.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}
