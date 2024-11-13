<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_seats',
        'table_number',
        'thumbnail',
        'type',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function detailReservations(): HasMany{
        return $this->hasMany(DetailReservation::class);
    }
}
