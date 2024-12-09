<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetUserReserveStatusCommand extends Command
{
    protected $signature = 'users:reset-reserve-status';
    protected $description = 'Reset is_reserve status for all users daily';

    public function handle()
    {
        User::query()->update(['is_reserve' => false]);
        $this->info('User reserve status reset successfully');
    }
}
