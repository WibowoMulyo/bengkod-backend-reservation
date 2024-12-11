<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class HandleCompletedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:handle-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle reservations that have been completed and update their statuses or perform necessary actions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $completedReservations = Reservation::whereIn('status', ['Terkonfirmasi', 'Pelanggaran'])
            ->whereDate('date', $now->toDateString())
            ->whereRaw("to_timestamp(split_part(time_slot, '-', 2), 'HH24:MI')::time <= ?", [$now->format('H:i')])
            ->get();

        foreach ($completedReservations as $reservation){
            $this->info("Reservation with code {$reservation->code} has been completed.");
            $reservation->update(['status' => 'Selesai']);
        }

        $this->info('Completed reservations handling completed.');
    }
}
