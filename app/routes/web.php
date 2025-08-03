<?php

use App\Http\Controllers\XeroController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('dashboard', function () {
//         return Inertia::render('dashboard');
//     })->name('dashboard');
// });


// Xero login route (overrides default login)
Route::get('/login', [XeroController::class, 'redirectToXero'])->name('login');
Route::get('/xero/callback', [XeroController::class, 'handleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [XeroController::class, 'dashboard'])->name('dashboard');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
