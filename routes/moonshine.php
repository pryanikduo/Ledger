<?php

use Illuminate\Support\Facades\Route;
use App\MoonShine\Pages\TurnoverBalancePage;

Route::moonshine(function () {
    Route::post('turnover/calculate', [TurnoverBalancePage::class, 'calculate'])
        ->name('moonshine.turnover.calculate');
});