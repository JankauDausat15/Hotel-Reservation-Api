<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique(); // contoh: INV-20250601-0001
            $table->integer('amount');                  // total yang harus dibayar
            $table->enum('payment_method', [
                'transfer_bank',
                'cash',
                'kartu_kredit',
                'e_wallet'
            ])->default('transfer_bank');
            $table->enum('status', [
                'pending',    // belum bayar
                'paid',       // sudah bayar
                'cancelled'   // dibatalkan
            ])->default('pending');
            $table->string('proof_of_payment')->nullable(); // bukti bayar (nama file)
            $table->timestamp('paid_at')->nullable();        // waktu pembayaran
            $table->text('notes')->nullable();               // catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};