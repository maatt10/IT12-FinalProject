<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductComponentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\RecordsController;


Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:owner,employee'])
    ->name('dashboard');

Route::middleware(['auth', 'role:owner,employee'])->group(function () {

    /* POS */
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    /* INVENTORY (Products & Materials) */
    Route::resource('products', ProductController::class);

    Route::post('/products/{product}/unarchive', [ProductController::class, 'unarchive'])
        ->middleware('role:owner')
        ->name('products.unarchive');

    /* BOM (legacy — kept for compatibility) */
    Route::get('/products/{product}/components/create', [ProductComponentController::class, 'create'])
        ->name('products.components.create');

    Route::post('/products/{product}/components', [ProductComponentController::class, 'store'])
        ->name('products.components.store');

    Route::delete('/products/{product}/components/{materialProduct}', [ProductComponentController::class, 'destroy'])
        ->name('products.components.destroy');

    /* STOCK ADJUSTMENT */
    Route::get('/inventory/{product}/initial-stock', [InventoryController::class, 'initialStock'])
        ->name('inventory.initial-stock');

    Route::post('/inventory/{product}/initial-stock', [InventoryController::class, 'storeInitialStock'])
        ->name('inventory.initial-stock.store');

    Route::get('/inventory/{product}/adjustment', [InventoryController::class, 'adjustment'])
        ->name('inventory.adjustment');

    Route::post('/inventory/{product}/adjustment', [InventoryController::class, 'storeAdjustment'])
        ->name('inventory.adjustment.store');

    Route::get('/inventory/{product}/history', [InventoryController::class, 'history'])
        ->name('inventory.history');

    Route::post('/inventory/{product}/transfer', [InventoryController::class, 'storeTransfer'])
        ->name('inventory.transfer.store');

    /* SALES POS */
    Route::get('/sales/pos', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');

    /* CUSTOMERS */
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');

    /* REPORTS */
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportsController::class, 'print'])->name('reports.print');
    Route::get('/reports/export', [ReportsController::class, 'exportCsv'])->name('reports.export');
});

Route::middleware(['auth', 'role:owner'])->group(function () {

    /* STOCK-IN */
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

    /* ONLINE ORDERS */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');

    /* PRODUCTION */
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::get('/production/create', [ProductionController::class, 'create'])->name('production.create');
    Route::post('/production', [ProductionController::class, 'store'])->name('production.store');

    /* RECORDS */
    Route::get('/records', [RecordsController::class, 'index'])->name('records.index');

    /* AUDIT */
    Route::get('/audit-trail', [AuditLogController::class, 'index'])->name('audit.index');

    /* BACKUP */
    Route::get('/backup-recovery', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup-recovery/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup-recovery/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
});

Route::redirect('/', '/login');
