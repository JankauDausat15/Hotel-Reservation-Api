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
    // POST /api/transactions
    // Buat transaksi baru dari reservasi
    // =====================================================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id|unique:transactions,reservation_id',
            'payment_method' => 'required|in:transfer_bank,cash,kartu_kredit,e_wallet',
            'notes'          => 'nullable|string',
        ], [
            'reservation_id.required' => 'ID reservasi wajib diisi.',
            'reservation_id.exists'   => 'Reservasi tidak ditemukan.',
            'reservation_id.unique'   => 'Transaksi untuk reservasi ini sudah ada.',
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

        $reservation   = Reservation::with('roomType')->find($request->reservation_id);
        $invoiceNumber = 'INV-' . Carbon::now()->format('Ymd') . '-' . str_pad(Transaction::count() + 1, 4, '0', STR_PAD_LEFT);

        $transaction = Transaction::create([
            'reservation_id' => $request->reservation_id,
            'invoice_number' => $invoiceNumber,
            'amount'         => $reservation->total_price,
            'payment_method' => $request->payment_method,
            'status'         => 'pending',
            'notes'          => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibuat!',
            'data'    => [
                'transaction'    => $transaction,
                'invoice_number' => $invoiceNumber,
                'reservation'    => $reservation,
                'amount'         => $reservation->total_price,
                'status'         => 'pending',
            ]
        ], 201);
    }

    // =====================================================
    // GET /api/transactions
    // Ambil semua transaksi
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
    // GET /api/transactions/{id}
    // Ambil detail transaksi by ID
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
    // POST /api/transactions/{id}/pay
    // Upload bukti bayar & ubah status jadi paid
    // =====================================================
    public function pay(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah dibayar.',
            ], 400);
        }

        if ($transaction->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah dibatalkan.',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'proof_of_payment' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'proof_of_payment.required' => 'Bukti pembayaran wajib diupload.',
            'proof_of_payment.image'    => 'File harus berupa gambar.',
            'proof_of_payment.mimes'    => 'Format gambar harus jpg, jpeg, atau png.',
            'proof_of_payment.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $file     = $request->file('proof_of_payment');
        $filename = 'proof_' . $transaction->invoice_number . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/proofs', $filename);

        $transaction->update([
            'proof_of_payment' => $filename,
            'status'           => 'paid',
            'paid_at'          => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi!',
            'data'    => [
                'invoice_number'   => $transaction->invoice_number,
                'status'           => 'paid',
                'paid_at'          => $transaction->paid_at,
                'proof_of_payment' => $filename,
            ]
        ], 200);
    }

    // =====================================================
    // PUT /api/transactions/{id}/cancel
    // Batalkan transaksi
    // =====================================================
    public function cancel($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah dibayar tidak bisa dibatalkan.',
            ], 400);
        }

        if ($transaction->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi ini sudah dibatalkan sebelumnya.',
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
    // GET /api/transactions/status/{status}
    // Filter transaksi by status
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
    // DELETE /api/transactions/{id}
    // Hapus transaksi berdasarkan ID
    // =====================================================
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi dengan ID ' . $id . ' tidak ditemukan.',
            ], 404);
        }

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi yang sudah dibayar tidak bisa dihapus.',
            ], 400);
        }

        $transaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus.',
        ], 200);
    }
}