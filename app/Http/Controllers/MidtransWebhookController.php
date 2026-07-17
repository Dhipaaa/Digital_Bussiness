<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $transaction = Transaction::with('event')->where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if (in_array($transaction->status, ['settlement', 'success'], true)) {
            return response()->json(['message' => 'Already processed']);
        }

        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'challenge') {
                $transaction->status = 'challenge';
            } elseif ($fraudStatus === 'accept') {
                $transaction->status = 'success';
                $this->processSuccess($transaction);
            }
        } elseif ($transactionStatus === 'settlement') {
            $transaction->status = 'settlement';
            $this->processSuccess($transaction);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'], true)) {
            $transaction->status = 'failed';
        } elseif ($transactionStatus === 'pending') {
            $transaction->status = 'pending';
        }

        $transaction->save();

        Log::info('Midtrans webhook handled', [
            'order_id' => $orderId,
            'status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
        ]);

        return response()->json(['message' => 'OK']);
    }

    private function processSuccess(Transaction $transaction): void
    {
        // Placeholder untuk modul berikutnya; saat ini cukup menandai transaksi selesai.
        $transaction->status = 'success';
    }
}
