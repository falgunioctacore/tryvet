<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeUserTrialCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check-trial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users trial period and update after 3 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('is_trial', true)
        ->where('created_at', '<=', Carbon::now()->subDays(3))
        ->get();

         foreach ($users as $user) {
         $user->is_trial = false; // Trial ko false set karna
         $user->save(); // Save karna
         $this->info("User {$user->id} trial period ended and updated to false.");
         }
         
         $this->info('Trial check completed.');
    }
}
