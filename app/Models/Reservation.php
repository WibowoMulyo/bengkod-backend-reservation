<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    use HasFactory;

    const STATUS_OPTIONS = ['Menunggu', 'Terverifikasi', 'Terkonfirmasi', 'Pelanggaran', 'Dibatalkan', 'Selesai'];
    const TYPE_OPTIONS = ['Individu', 'Kelompok'];
    const PURPOSE_OPTIONS = ['Kerja Kelompok', 'Penelitian', 'Diskusi', 'Belajar Mandiri', 'Kegiatan Organisasi'];
    const TIME_SLOT_OPTIONS = ['08:00-10:00', '10:00-12:00', '12:00-13:00', '13:00-15:00', '15:00-17:00'];

    protected $table = 'reservations';

    protected $fillable = [
        'table_id',
        'code',
        'status',
        'type',
        'purpose',
        'time_slot',
        'date',
        'expires_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function detailReservations(): HasMany{
        return $this->hasMany(DetailReservation::class);
    }
    public function table(): BelongsTo{
        return $this->belongsTo(Table::class, 'table_id');
    }
}
