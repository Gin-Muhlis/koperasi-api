<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InstallmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PositionCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileAppController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SavingController;
use App\Http\Controllers\StuffController;
use App\Http\Controllers\SubCategoryController;
use Illuminate\Support\Facades\Route;


Route::middleware('json.response')->group(function () {
	// login dan register
	Route::post('/register', [AuthController::class, 'register'])->middleware('guest');
	Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
	Route::get('/register/position-categories', [PositionCategoryController::class, 'index'])->middleware('guest');

	Route::middleware('auth:api')->group(function () {
		// logout
		Route::post('/logout', [AuthController::class, 'logout']);
		// profile
		Route::get('/profile', [AuthController::class, 'profile']);
		// update profile
		Route::put('/profile/{id}', [AuthController::class, 'updateProfile']);
	});

	// Hanya dapat diakses admin
	Route::middleware('role:super-admin')->group(function () {

		// Data master
		Route::apiResource('/member', MemberController::class);
		Route::apiResource('/sub-category', SubCategoryController::class);
		Route::apiResource('/product', ProductController::class);
		Route::apiResource('/stuff', StuffController::class);
		Route::apiResource('/position-category', PositionCategoryController::class);
		Route::apiResource('/role', RoleController::class);
		Route::apiResource('/profile-app', ProfileAppController::class);
		Route::get('/category', [CategoryController::class, 'index']);

		// Reset password member
		Route::put('/change-password/{id}', [AuthController::class, 'changePassword']);

		// Transaksi
		// Route::apiResource('/purchase', PurchaseController::class);
		// Route::apiResource('/sale', SaleController::class);

		// Simpanan
		Route::apiResource('/saving', SavingController::class);
		Route::get('/member-saving', [SavingController::class, 'memberSaving']);

		// Pinjaman
		Route::apiResource('/receivable', ReceivableController::class);

		// Angsuran
		Route::apiResource('/installment', InstallmentController::class);

		// Invoice
		Route::apiResource('/invoice', InvoiceController::class);
		Route::get('/members-invoice', [InvoiceController::class, 'getMemberInvoice']);
		Route::get('/sub-categories-invoice', [InvoiceController::class, 'getSubCategoriesInvoice']);
		Route::post('/invoice-detail', [InvoiceController::class, 'storeDetailInvoice']);
		Route::get('/invoice-detail/{code}', [InvoiceController::class, 'detailInvoice']);

		// Payment invoice
		Route::apiResource('payment', PaymentController::class);

		// Export
		Route::get('/export/invoice-excel/{invoice_code}', [ExportController::class, 'detailInvoiceExportExcel']);
		Route::get('/export/invoice-pdf/{invoice_code}', [ExportController::class, 'detailInvoiceExportPdf']);
		Route::post('/export/invoice-member', [ExportController::class, 'invoiceMember']);
		Route::get('/export/members', [ExportController::class, 'exportMembers']);
		Route::get('/export/categories/pdf', [ExportController::class, 'exportCategoriesPdf']);
		Route::get('/export/sub-categories/pdf', [ExportController::class, 'exportSubCategoriesPdf']);
		Route::get('/export/products/pdf', [ExportController::class, 'exportProductsPdf']);
		Route::get('/export/stuffs/pdf', [ExportController::class, 'exportStuffsPdf']);
		Route::get('/export/position-categories/pdf', [ExportController::class, 'exportPositionCategoriesPdf']);

		// Import
		Route::post('/import/sub-categories', [ImportController::class, 'importSubCategories']);
		Route::post('/import/products', [ImportController::class, 'importProducts']);
		Route::post('/import/stuffs', [ImportController::class, 'importStuffs']);
		Route::post('/import/position-categories', [ImportController::class, 'importPositionCategories']);
		Route::post('/import/members', [ImportController::class, 'importMembers']);

		// laporan
		Route::prefix('report')->group(function () {
			// Laporan anggota
			Route::get('/members', [ReportController::class, 'reportMembers']);
			Route::get('/export/report-members', [ExportController::class, 'ReportMembers']);
			Route::get('/export/report-member/{id}', [ExportController::class, 'ReportMember']);

			// Laporan simpanan
			Route::get('/saving-members', [ReportController::class, 'reportSavingMembers']);
			Route::get('/export/report-saving-members', [ExportController::class, 'ReportSavingMembers']);
			Route::get('/export/report-saving-member/{id}', [ExportController::class, 'ReportSavingMember']);

			// Laporan pinjaman
			Route::get('/loan-members', [ReportController::class, 'reportLoanMembers']);
			Route::get('/export/report-loan-members', [ExportController::class, 'ReportLoanMembers']);
			Route::get('/export/report-loan-member/{id}', [ExportController::class, 'ReportLoanMember']);

		});

		// Dashboard
		Route::get('/dashboard/admin', [DashboardController::class, 'dashboardAdmin']);
	});

	// Dapat diakses member dan admin
	Route::middleware('role:super-admin|member')->group(function () {
		// Golongan
		Route::get('/group-member', [PositionCategoryController::class, 'index']);

		// Sub kategori
		Route::get('/sub-categories-saving', [SavingController::class, 'getSubCategories']);
		Route::get('/sub-categories-receivable', [ReceivableController::class, 'getSubCategories']);

	});

	// Hanya dapat diakses member
	Route::middleware('role:member')->group(function () {
		// Dashboard
		Route::get('/dashboard/member', [DashboardController::class, 'dashboardMember']);
		Route::put('/change-password-member', [AuthController::class, 'changePasswordMember']);

		Route::get('/saving-member', [MemberController::class, 'getSavingMember']);
		Route::get('/loan-member', [MemberController::class, 'getLoanMember']);

	});

});
