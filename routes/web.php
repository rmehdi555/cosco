<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RefahRegistrationController;

// Route::get('/', function () {
//     return view('refah.loading');
// });

// Refah Registration Routes
Route::get('/', [RefahRegistrationController::class, 'showForm'])->name('refah.registration');
Route::post('/refah/registration', [RefahRegistrationController::class, 'store'])->name('refah.registration.store');

// AJAX Routes for dynamic dropdowns
Route::get('/refah/provinces/{country_id}', [RefahRegistrationController::class, 'getProvinces']);
Route::get('/refah/cities/{province_id}', [RefahRegistrationController::class, 'getCities']);