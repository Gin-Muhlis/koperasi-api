<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\PaymentDeterminationController;

Route::apiResource('/member', MemberController::class);
Route::apiResource('/payment-determination', PaymentDeterminationController::class);
Route::apiResource('/saving-members', SavingController::class);

// laporan
Route::prefix('report')->group(function() {
    Route::get('/saving-members', [MemberController::class, 'reportSavingMembers']);
});