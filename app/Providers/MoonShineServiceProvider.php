<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Laravel\DependencyInjection\MoonShine;
use MoonShine\Laravel\DependencyInjection\MoonShineConfigurator;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\MoonShine\Resources\MoonShineUserRole\MoonShineUserRoleResource;
use App\MoonShine\Resources\Account\AccountResource;
use App\MoonShine\Resources\Transaction\TransactionResource;
use App\MoonShine\Resources\JournalEntry\JournalEntryResource;
use App\MoonShine\Pages\TurnoverBalancePage;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use MoonShine\Laravel\DefaultRoutes;

class MoonShineServiceProvider extends ServiceProvider
{
    /**
     * @param  CoreContract<MoonShineConfigurator>  $core
     */
    public function boot(CoreContract $core): void
    {
        $core
            ->resources([
                MoonShineUserResource::class,
                MoonShineUserRoleResource::class,
                AccountResource::class,
                TransactionResource::class,
                JournalEntryResource::class,
            ])
            ->pages([
                ...$core->getConfig()->getPages(),
                TurnoverBalancePage::class,
            ])
        ;
        Route::moonshine(function () {
            Route::post('/turnover/calculate', [TurnoverBalancePage::class, 'calculate'])
                ->name('moonshine.turnover.calculate');
        });
    }
}
