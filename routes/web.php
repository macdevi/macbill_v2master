<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingSettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerExcelController;
use App\Http\Controllers\CustomerMikrotikImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/staff-home', function () {
        return response()->view('placeholders.staff-home');
    })->name('staff.home');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/pppoe', [DashboardController::class, 'pppoe'])->name('dashboard.pppoe');

Route::get('/customers/import/mikrotik', [CustomerMikrotikImportController::class, 'create'])->name('customers.import.mikrotik');
Route::post('/customers/import/mikrotik/preview', [CustomerMikrotikImportController::class, 'preview'])->name('customers.import.mikrotik.preview');
Route::post('/customers/import/mikrotik/store', [CustomerMikrotikImportController::class, 'store'])->name('customers.import.mikrotik.store');
        Route::get('/customers/export/excel', [CustomerExcelController::class, 'export'])->name('customers.export.excel');
        Route::get('/customers/import/excel', [CustomerExcelController::class, 'importForm'])->name('customers.import.excel');
        Route::post('/customers/import/excel', [CustomerExcelController::class, 'importStore'])->name('customers.import.excel.store');
        Route::get('/customers/template/excel', [CustomerExcelController::class, 'template'])->name('customers.template.excel');
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/{customer}/credit-balance', [CustomerController::class, 'addCreditBalance'])->name('customers.credit-balance');
        Route::post('/customers/{customer}/isolate', [CustomerController::class, 'isolate'])->name('customers.isolate');
        Route::post('/customers/{customer}/activate', [CustomerController::class, 'activate'])->name('customers.activate');

        Route::resource('packages', PackageController::class)->except(['show']);
        Route::get('/packages/import/{router}', [PackageController::class, 'import'])->name('packages.import');
        Route::post('/packages/import/store', [PackageController::class, 'importStore'])->name('packages.import.store');
        Route::post('/packages/{package}/sync', [PackageController::class, 'sync'])->name('packages.sync');

        Route::get('/invoices/create-page', [InvoiceController::class, 'createPage'])->name('invoices.create-page');
        Route::get('/billing/credit-balance', [InvoiceController::class, 'creditBalancePage'])->name('billing.credit-balance');
        Route::get('/invoices/{invoice}/pay', [InvoiceController::class, 'pay'])->name('invoices.pay');
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
        Route::post('/invoices/generate/manual', [InvoiceController::class, 'generateManual'])->name('invoices.generate.manual');
        Route::post('/invoices/generate/mass', [InvoiceController::class, 'generateMass'])->name('invoices.generate.mass');
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

        Route::resource('expenses', ExpenseController::class)->except(['show']);

        Route::get('/settings/billing', [BillingSettingController::class, 'edit'])
            ->name('settings.billing.edit');
        Route::put('/settings/billing', [BillingSettingController::class, 'update'])
            ->name('settings.billing.update');

        Route::resource('routers', RouterController::class)->except(['show']);
        Route::post('/routers/{router}/test', [RouterController::class, 'test'])->name('routers.test');

        Route::get('/users', [UserManagementController::class, 'index']);
        Route::get('/users/create', [UserManagementController::class, 'create']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::get('/users/{user}', [UserManagementController::class, 'show']);
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::post('/users/{user}/password', [UserManagementController::class, 'updatePassword']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });
});
