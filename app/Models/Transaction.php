<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'reservation_id',
        'invoice_number',
        'amount',
        'payment_method',
        'status',
        'proof_of_payment',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    // Relasi ke Reservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}