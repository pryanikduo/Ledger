<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\JournalEntryObserver;
use App\Models\JournalEntry;

use App\Observers\TransactionObserver;
use App\Models\Transaction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JournalEntry::observe(JournalEntryObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
