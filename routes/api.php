<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PositionCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileAppController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\StuffController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TabController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// login dan register
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register/position-categories', [PositionCategoryController::class,'index']);

Route::middleware('json.response')->group(function () {
	Route::middleware('auth:api')->group(function () {
		// logout
		Route::post('/logout', [AuthController::class, 'logout']);
		// profile
		Route::get('/profile', [AuthController::class, 'profile']);
		// update profile
		Route::put('/profile/{id}', [AuthController::class, 'updateProfile']);
	});

	Route::middleware('role:super-admin')->group(function () {

		// data
		Route::apiResource('/member', MemberController::class);
		Route::apiResource('/sub-category', SubCategoryController::class);
		Route::apiResource('/product', ProductController::class);
		Route::apiResource('/stuff', StuffController::class);
		Route::apiResource('/position-category', PositionCategoryController::class);
		Route::apiResource('/role', RoleController::class);
		Route::apiResource('/profile-app', ProfileAppController::class);
		Route::get('/category', [CategoryController::class,'index']);


		// reset password
		Route::put('/change-password/{id}', [AuthController::class,'changePassword']);
		

		// transaksi
		Route::apiResource('/purchase', PurchaseController::class);
		Route::apiResource('/sale', SaleController::class);

		// auth
		Route::apiResource('/user', UserController::class);

		// simpanan
		Route::apiResource('/saving', SavingController::class);
		Route::get('/member-saving', [SavingController::class,'memberSaving']);

		// pinjaman
		Route::apiResource('/receivable', ReceivableController::class);
		Route::get('/sub-categories-receivable', [ReceivableController::class,'getSubCategories']);

		// pinjaman
		Route::apiResource('/installment', InstallmentController::class);

		// Invoicep
		Route::apiResource('invoice', InvoiceController::class);
		Route::get('/members-invoice', [InvoiceController::class, 'getMemberInvoice']);
		Route::get('/sub-categories-invoice', [InvoiceController::class, 'getSubCategoriesInvoice']);
		Route::post('/invoice-detail', [InvoiceController::class, 'storeDetailInvoice']);
		Route::get('/invoice-detail/{code}', [InvoiceController::class, 'detailInvoice']);
		

		// payment
		Route::apiResource('payment', PaymentController::class);

		// Export
		Route::get('/export/invoice-excel/{invoice_code}', [ExportController::class, 'detailInvoiceExportExcel']);
		Route::get('/export/invoice-pdf/{invoice_code}', [ExportController::class, 'detailInvoiceExportPdf']);
		Route::post('/export/invoice-member', [ExportController::class, 'invoiceMember']);

		Route::get('/export/members', [ExportController::class,'exportMembers']);

		// laporan
		Route::prefix('report')->group(function () {
			Route::get('/members', [MemberController::class, 'reportMembers']);
			Route::get('export/report-members', [ExportController::class,'ReportMembers']);
			Route::get('export/report-member/{id}', [ExportController::class,'ReportMember']);

			Route::get('/saving-members', [MemberController::class, 'reportSavingMembers']);
			Route::get('export/report-saving-members', [ExportController::class,'ReportSavingMembers']);
			Route::get('export/report-saving-member/{id}', [ExportController::class,'ReportSavingMember']);
			
			Route::get('/loan-members', [MemberController::class, 'reportLoanMembers']);
			Route::get('export/report-loan-members', [ExportController::class,'ReportLoanMembers']);
			Route::get('export/report-loan-member/{id}', [ExportController::class,'ReportLoanMember']);

		});

		// dashboard
		Route::get('/dashboard/admin', [MemberController::class, 'dashboardAdmin']);
	});

	Route:: middleware('role:super-admin|member')->group(function () {
		// golongan
		Route::get('group-member', [PositionCategoryController::class,'index']);

		// sub kategori
		Route::get('/sub-categories-saving', [SavingController::class,'getSubCategories']);
	});

	Route:: middleware('role:member')->group(function () {
		// dashboard
		Route::get('/dashboard/member', [MemberController::class, 'dashboardMember']);
		Route::put('/change-password-member', [AuthController::class,'changePasswordMember']);
	});
});
