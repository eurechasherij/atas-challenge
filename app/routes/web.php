<?php

use App\Http\Controllers\XeroController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

// Xero login route
Route::get('/login', [XeroController::class, 'redirectToXero'])->name('login');
Route::get('/xero/callback', [XeroController::class, 'handleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [XeroController::class, 'dashboard'])->name('dashboard');
    Route::post('/xero/sync', [XeroController::class, 'syncXeroData'])->name('xero.sync');
});

// Xero tenant selection
Route::get('/xero/select-tenant', [XeroController::class, 'selectTenantPage']);
Route::post('/xero/select-tenant', [XeroController::class, 'selectTenant']);


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
