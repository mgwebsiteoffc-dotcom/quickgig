<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class ResetSubscriptionCredits extends Command
{
    protected $signature = 'subscriptions:roll {--dry-run}';

    protected $description = 'Zero the used credits and move the renewal date on for every company whose cycle has ended';

    public function handle(): int
    {
        $due = Company::whereNotNull('renews_on')
            ->whereDate('renews_on', '<=', now()->toDateString())
            ->get();

        $this->info($due->count() . ' company subscription(s) due to roll over.');

        foreach ($due as $company) {
            $this->line("  {$company->name}: {$company->credits_used}/{$company->monthly_credits} used → reset");

            if ($this->option('dry-run')) {
                continue;
            }

            $company->forceFill([
                'credits_used' => 0,
                'renews_on'    => $company->renews_on->addMonthNoOverflow(),
            ])->save();
        }

        return self::SUCCESS;
    }
}
