<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Tipe Kamar
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // Tambahan: Untuk Junior Suite, Executive, dll
            $table->string('name');     // Contoh: Garden Suite, Pool Suite
            $table->text('description');
            $table->integer('price_per_night');
            $table->string('image')->nullable(); // Nama file gambar
            $table->timestamps();
        });

        // Tabel Reservasi
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relasi ke tabel users
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade'); // Relasi ke tipe kamar
            $table->string('customer_name');
            $table->string('customer_email');
            $table->date('check_in');
            $table->date('check_out');
            $table->integer('total_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Urutan drop harus benar karena ada foreign key
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('room_types');
    }
};