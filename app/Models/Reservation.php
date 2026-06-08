<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations'; // Memastikan nama tabel jamak

    protected $fillable = [
        'user_id',
        'room_type_id',
        'customer_name',
        'customer_email',
        'check_in',
        'check_out',
        'total_price'
    ];

    /**
     * Casts attributes to specific types.
     * Ini membantu agar tanggal tidak dianggap string biasa.
     */
    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_price' => 'integer',
    ];

    // 1. Relasi ke RoomType (Sangat Penting untuk TransactionController)
    // Pastikan file RoomType.php ada di folder Models
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    // 2. Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 3. Relasi ke Transaction
    // Satu reservasi biasanya hanya punya satu transaksi (invoice)
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'reservation_id');
    }
}