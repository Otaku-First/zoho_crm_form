<?php

use App\Http\Controllers\ZohoAuthController;
use App\Http\Controllers\ZohoCrmFormController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [ZohoCrmFormController::class, 'index']);
Route::get('/auth', fn () => Inertia::render('Auth'));

Route::get('/zoho/auth', [ZohoAuthController::class, 'redirect']);
Route::get('/zoho/callback', [ZohoAuthController::class, 'callback']);

Route::post('/crm/account', [ZohoCrmFormController::class, 'createAccount']);
Route::post('/crm/deal', [ZohoCrmFormController::class, 'createDeal']);
