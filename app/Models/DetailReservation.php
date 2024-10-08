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
        'table_id',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id');
    }
    public function reservation(): BelongsTo{
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }
    public function table(): BelongsTo{
        return $this->belongsTo(Table::class, 'table_id');
    }
}
