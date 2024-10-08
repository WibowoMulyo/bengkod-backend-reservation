<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'reservation_type',
        'reservation_code',
        'reservation_date',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'started_at' => 'datetime:H:i',
        'ended_at' => 'datetime:H:i',
    ];

    public function detailReservations(): HasMany{
        return $this->hasMany(DetailReservation::class);
    }
}
