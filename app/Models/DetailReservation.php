<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_id',
        'status'
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reservation(): BelongsTo{
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
}
