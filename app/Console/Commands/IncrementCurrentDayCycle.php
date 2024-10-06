<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SavingGroup;
use Carbon\Carbon;

class IncrementCurrentDayCycle extends Command
{
    protected $signature = 'increment:day-cycle';
    protected $description = 'Increment current day and cycle for saving groups';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Fetch all saving groups
        $savingGroups = SavingGroup::all();

        foreach ($savingGroups as $savingGroup) {
            // Check if the current day equals the days per cycle
            if ($savingGroup->current_day >= $savingGroup->days_per_cycle) {
                // Reset current day to 1 and increment cycle
                $savingGroup->current_day = 1;
                $savingGroup->current_cycle += 1;
            } else {
                // Increment current day
                $savingGroup->current_day += 1;
            }

            // Save the changes
            $savingGroup->save();
        }

        $this->info('Successfully incremented current day and cycle.');
    }
}
