<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentDeterminationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\StuffController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// login dan register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('json.response')->group(function () {
	Route::middleware('auth:api')->group(function () {
		// logout
		Route::post('/logout', [AuthController::class, 'logout']);
		// profile
		Route::get('/profile', [AuthController::class, 'profile']);
	});

	Route::middleware('role:super-admin')->group(function () {

		// data
		Route::apiResource('/member', MemberController::class);
		Route::apiResource('/category', CategoryController::class);
		Route::apiResource('/sub-category', SubCategoryController::class);
		Route::apiResource('/product', ProductController::class);
		Route::apiResource('/stuff', StuffController::class);
		Route::apiResource('/role', RoleController::class);

		Route::get('/saving/sub-category', [SubCategoryController::class, 'subCategorySaving']);

		// transaksi
		Route::apiResource('/purchase', PurchaseController::class);
		Route::apiResource('/sale', SaleController::class);

		// auth
		Route::apiResource('/user', UserController::class);

		// app
		Route::apiResource('/payment-determination', PaymentDeterminationController::class);
		Route::apiResource('/saving-members', SavingController::class);

		// laporan
		Route::prefix('report')->group(function () {
			Route::get('/saving-members', [MemberController::class, 'reportSavingMembers']);
		});
	});
});
