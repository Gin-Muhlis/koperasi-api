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
use Illuminate\Support\Facades\Route;


Route::middleware('json.response')->group(function () {
	// login dan register (used)
	Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
	Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
	Route::get('/register/position-categories', [PositionCategoryController::class, 'index'])->middleware('guest');

	Route::middleware('auth:api')->group(function () {
		// logout (used)
		Route::post('/logout', [AuthController::class, 'logout']);
		// profile (used)
		Route::get('/profile', [AuthController::class, 'profile']);
		// update profile (used)
		Route::put('/profile/{id}', [AuthController::class, 'updateProfile']);
	});

	// Hanya dapat diakses admin
	Route::middleware('role:super-admin')->group(function () {

		// Data master (used)
		Route::apiResource('/member', MemberController::class);
		Route::apiResource('/sub-category', SubCategoryController::class);
		Route::apiResource('/product', ProductController::class);
		Route::apiResource('/stuff', StuffController::class);
		Route::apiResource('/position-category', PositionCategoryController::class);
		Route::apiResource('/role', RoleController::class);
		Route::apiResource('/profile-app', ProfileAppController::class);
		Route::get('/category', [CategoryController::class, 'index']);

		// Reset password member (used)
		Route::put('/change-password/{id}', [AuthController::class, 'changePassword']);

		// Transaksi
		// Route::apiResource('/purchase', PurchaseController::class);
		// Route::apiResource('/sale', SaleController::class);

		// Simpanan (used)
		Route::apiResource('/saving', SavingController::class);
		Route::get('/member-saving', [SavingController::class, 'memberSaving']);

		// Pinjaman (used)
		Route::apiResource('/receivable', ReceivableController::class);

		// Angsuran (used)
		Route::apiResource('/installment', InstallmentController::class);

		// Invoice (used)
		Route::apiResource('/invoice', InvoiceController::class);
		Route::get('/members-invoice', [InvoiceController::class, 'getMemberInvoice']);
		Route::get('/sub-categories-invoice', [InvoiceController::class, 'getSubCategoriesInvoice']);
		Route::post('/invoice-detail', [InvoiceController::class, 'storeDetailInvoice']);
		Route::get('/invoice-detail/{code}', [InvoiceController::class, 'detailInvoice']);

		// Payment invoice (used)
		Route::apiResource('payment', PaymentController::class);

		// Export (used)
		Route::get('/export/invoice-excel/{invoice_code}', [ExportController::class, 'detailInvoiceExportExcel']);
		Route::get('/export/invoice-pdf/{invoice_code}', [ExportController::class, 'detailInvoiceExportPdf']);
		Route::post('/export/invoice-member', [ExportController::class, 'invoiceMember']);
		Route::get('/export/members', [ExportController::class, 'exportMembers']);

		// laporan (used)
		Route::prefix('report')->group(function () {
			// Laporan anggota (used)
			Route::get('/members', [MemberController::class, 'reportMembers']);
			Route::get('/export/report-members', [ExportController::class, 'ReportMembers']);
			Route::get('/export/report-member/{id}', [ExportController::class, 'ReportMember']);

			// Laporan simpanan (used)
			Route::get('/saving-members', [MemberController::class, 'reportSavingMembers']);
			Route::get('/export/report-saving-members', [ExportController::class, 'ReportSavingMembers']);
			Route::get('/export/report-saving-member/{id}', [ExportController::class, 'ReportSavingMember']);

			// Laporan pinjaman (used)
			Route::get('/loan-members', [MemberController::class, 'reportLoanMembers']);
			Route::get('/export/report-loan-members', [ExportController::class, 'ReportLoanMembers']);
			Route::get('/export/report-loan-member/{id}', [ExportController::class, 'ReportLoanMember']);

		});

		// Dashboard
		Route::get('/dashboard/admin', [MemberController::class, 'dashboardAdmin']);
	});

	// Dapat diakses member dan admin
	Route::middleware('role:super-admin|member')->group(function () {
		// Golongan
		Route::get('/group-member', [PositionCategoryController::class, 'index']);

		// Sub kategori
		Route::get('/sub-categories-saving', [SavingController::class, 'getSubCategories']);
	});

	// Hanya dapat diakses member
	Route::middleware('role:member')->group(function () {
		// Dashboard
		Route::get('/dashboard/member', [MemberController::class, 'dashboardMember']);
		Route::put('/change-password-member', [AuthController::class, 'changePasswordMember']);
	});
});
