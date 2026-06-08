<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TransactionController extends Controller
{
    // =====================================================
    // 1. POST /api/transactions
    // Buat transaksi baru — status langsung PAID
    // =====================================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
            'payment_method' => 'required|in:transfer_bank,cash,kartu_kredit,e_wallet',
            'notes'          => 'nullable|string',
        ], [
            'reservation_id.required' => 'ID reservasi wajib diisi.',
            'reservation_id.exists'   => 'Reservasi tidak ditemukan.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in'       => 'Metode pembayaran tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $reservation = Reservation::with('roomType')->find($request->reservation_id);

            // Cek apakah transaksi sudah ada untuk reservasi ini
            $existing = Transaction::where('reservation_id', $request->reservation_id)->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi sudah ada.',
                    'data'    => [
                        'transaction'    => $existing,
                        'invoice_number' => $existing->invoice_number,
                        'reservation'    => $reservation,
                        'amount'         => $existing->amount,
                        'status'         => $existing->status,
                    ]
                ], 200);
            }

            $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);

            $transaction = Transaction::create([
                'reservation_id' => $request->reservation_id,
                'invoice_number' => $invoiceNumber,
                'amount'         => $reservation->total_price,
                'payment_method' => $request->payment_method,
                // FIX: Langsung paid, tidak perlu upload bukti
                'status'         => 'paid',
                'paid_at'        => Carbon::now(),
                'notes'          => $request->notes,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil!',
                'data'    => [
                    'transaction'    => $transaction,
                    'invoice_number' => $invoiceNumber,
                    'reservation'    => $reservation,
                    'amount'         => $reservation->total_price,
                    'status'         => 'paid',
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    // =====================================================
    // 2. GET /api/transactions
    // =====================================================
    public function index()
    {
        $transactions = Transaction::with(['reservation.roomType'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data transaksi berhasil diambil',
            'data'    => $transactions
        ], 200);
    }

    // =====================================================
    // 3. GET /api/transactions/{id}
    // =====================================================
    public function show($id)
    {
        $transaction = Transaction::with(['reservation.roomType'])->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail transaksi berhasil diambil',
            'data'    => $transaction
        ], 200);
    }

    // =====================================================
    // 4. PUT /api/transactions/{id}/cancel
    // =====================================================
    public function cancel($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah dibayar tidak bisa dibatalkan.'
            ], 400);
        }

        $transaction->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan.',
            'data'    => $transaction
        ], 200);
    }

    // =====================================================
    // 5. GET /api/transactions/status/{status}
    // =====================================================
    public function filterByStatus($status)
    {
        $allowedStatus = ['pending', 'paid', 'cancelled'];

        if (!in_array($status, $allowedStatus)) {
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid. Gunakan: pending, paid, atau cancelled.',
            ], 400);
        }

        $transactions = Transaction::with(['reservation.roomType'])
            ->where('status', $status)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data transaksi status ' . $status . ' berhasil diambil',
            'data'    => $transactions
        ], 200);
    }

    // =====================================================
    // 6. DELETE /api/transactions/{id}
    // =====================================================
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.'
            ], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah dibayar tidak bisa dihapus.'
            ], 400);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus.',
        ], 200);
    }
}