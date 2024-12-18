<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    const TYPE_OPTIONS = ['Individu', 'Kelompok', 'Bersama'];

    protected $fillable = [
        'total_seats',
        'available_seats',
        'table_number',
        'thumbnail',
        'type',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function Reservations(): HasMany{
        return $this->hasMany(Reservation::class);
    }
}
