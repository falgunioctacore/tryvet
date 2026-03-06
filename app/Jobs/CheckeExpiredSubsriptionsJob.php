<?php

namespace App\Jobs;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckeExpiredSubsriptionsJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $today = Carbon::today();

        // // Fetch all subscriptions where the end_date is today or in the past
        // $subscriptions = Subscription::where('end_date', '<=', $today)->get();

        // // Loop through the subscriptions and update their flag if necessary
        // foreach ($subscriptions as $subscription) {
        //     if (Carbon::parse($subscription->end_date)->lte($today)) {
        //         $subscription->update(['flag' => 0]);
        //         Log::info("Subscription ID {$subscription->id} expired. Flag set to 0.");
        //     }
        // }

        // Log::info('Expired subscriptions check completed.');
    }
}
