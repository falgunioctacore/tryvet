<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckSubscriptionFlags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-flags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check All Subscription';

    /**
     * Execute the console command.
     */
    public function handle()
    {
         // Get today's date
         $today = Carbon::today();

         // Get all subscriptions
        //  $subscriptions = Subscription::all();
        $subscriptions = Subscription::where('end_date', '<=', $today)->get(); 
         // Loop through each subscription and check its end_date
         foreach ($subscriptions as $subscription) {
             // Parse the subscription end_date
             $endDate = Carbon::parse($subscription->end_date);
 
             // If the end_date is today or in the past, set the flag to 0
             if ($endDate <= $today) {
                 $subscription->update([
                     'flag' => 0, // Set the flag to 0
                 ]);
 
                 // Optionally, log or display that the flag was updated
                 $this->info("Subscription ID {$subscription->id} expired. Flag set to 0.");
             }
         }
 
         // Final message to confirm the operation is complete
         $this->info('Subscription flag check completed.');
    }
}
