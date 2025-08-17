<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RefahRegistrationController;

// Route::get('/', function () {
//     return view('refah.loading');
// });

// Main Index Route
Route::get('/', [RefahRegistrationController::class, 'index'])->name('refah.index');

// Refah Registration Routes
Route::get('/register', [RefahRegistrationController::class, 'showForm'])->name('refah.registration.form');
Route::post('/refah/registration', [RefahRegistrationController::class, 'store'])->name('refah.registration.store');

// AJAX Routes for dynamic dropdowns
Route::get('/refah/provinces/{country_id}', [RefahRegistrationController::class, 'getProvinces']);
Route::get('/refah/cities/{province_id}', [RefahRegistrationController::class, 'getCities']);
Route::post('/refah/check-mobile', [RefahRegistrationController::class, 'checkMobileAvailability']);
Route::post('/refah/convert-date', [RefahRegistrationController::class, 'convertPersianDate']);

// Payment Callback Route
Route::get('/payment/callback', [\App\Http\Controllers\PaymentController::class, 'callback'])->name('payment.callback');