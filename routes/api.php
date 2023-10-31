<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;

Route::apiResource('/member', MemberController::class);

// laporan
Route::prefix('report')->group(function() {
    Route::get('/saving-members', [MemberController::class, 'reportSavingMembers']);
});