<?php

namespace App\Jobs;

use App\Models\DetailReservation;
use App\Models\Reservation;
use App\Models\Table;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckReservationConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tableId;
    protected $reservation;
    protected $reservationService;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation, int $tableId)
    {
        $this->tableId = $tableId;
        $this->reservation = $reservation;
        $this->reservationService = app(ReservationService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reservation = $this->reservation->fresh();

        // Pastikan status reservasi "Menunggu" dan waktu konfirmasi telah habis
        if ($reservation->status === 'Menunggu' && $reservation->expires_at < Carbon::now()) {
            $unconfirmedCount = DetailReservation::where('reservation_id', $reservation->id)
                ->where('status', 'Menunggu')
                ->count();

            if ($unconfirmedCount > 0) {
                // Kembalikan seat yang tersedia
                $this->reservationService->updateAvailableSeat($reservation->table_id, $reservation, true);

                // Rollback `is_reserve` untuk semua anggota
                $detailReservations = DetailReservation::with('user')
                    ->where('reservation_id', $reservation->id)
                    ->get();

                foreach ($detailReservations as $detail) {
                    $user = $detail->user;

                    if ($user) {
                        Log::info('Rollback is_reserve for user: ' . $user);
                        $user->update(['is_reserve' => false]);
                    } else {
                        Log::warning('User not found for DetailReservation ID: ' . $detail->id);
                    }
                }

                // Hapus semua detail reservasi
                DetailReservation::where('reservation_id', $reservation->id)->delete();

                // Hapus reservasi
                $reservation->delete();
            }
        }
    }
}
