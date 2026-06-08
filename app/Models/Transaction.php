<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

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

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}