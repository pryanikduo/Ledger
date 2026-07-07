<?php

use Illuminate\Support\Facades\Route;
// use Illuminate\Routing\Router;

// use App\Moonshine\Pages\TurnoverBalancePage;

// Route::moonshine(function () {
//     Route::post(
//         'turnover/calculate',
//         [TurnoverBalancePage::class, 'calculate']
//     )->name('moonshine.turnover.calculate');
// });
Route::get('/', function () {
    return redirect()->route('moonshine.index');
})->name('moonshine.index');
