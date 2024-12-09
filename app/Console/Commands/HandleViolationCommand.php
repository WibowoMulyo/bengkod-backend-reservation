<?php

namespace App\Console\Commands;

use App\Models\DetailReservation;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleViolationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:handle-violations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark reservations as violations if not confirmed within the allowed time slot.';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $oneHourBefore = $now->copy()->subHour();

        $violatedReservations = Reservation::where('status', 'Terverifikasi')
            ->whereDate('date', $now->toDateString())
            ->where(function ($query) use ($oneHourBefore) {
                $query->whereRaw("to_timestamp(split_part(time_slot, '-', 1), 'HH24:MI')::time <= ?", [$oneHourBefore->format('H:i')]);
            })
            ->get();

        $this->info('Found ' . $violatedReservations->count() . ' reservations that violated the time slot.');

        foreach ($violatedReservations as $reservation) {
            $reservation->update(['status' => 'Pelanggaran']);

            DetailReservation::where('reservation_id', $reservation->id)->update(['status' => 'Pelanggaran']);

            $detailReservations = DetailReservation::where('reservation_id', $reservation->id)->get();
            foreach ($detailReservations as $detail) {
                $user = User::find($detail->user_id);
                if ($user) {
                    $user->increment('penalty_count');
                }
            }

            $this->info("Reservation with code {$reservation->code} marked as violation.");
        }

        $this->info('Violations handling completed.');
    }
}
