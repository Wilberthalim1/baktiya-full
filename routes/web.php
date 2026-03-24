<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardInventoryController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\InvoicingController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\InventoryController;

Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardInventoryController::class, 'index'])->name('dashboard');
    Route::get('/inventory', [DashboardInventoryController::class, 'inventory'])->name('inventory.index');

    // Sales
    Route::get('/sales', [SalesOrderController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesOrderController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesOrderController::class, 'store'])->name('sales.store');
    Route::get('/sales/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales.show');
    Route::patch('/sales/{salesOrder}/approve', [SalesOrderController::class, 'approve'])->name('sales.approve')->middleware('role:management,admin');
    Route::patch('/sales/{salesOrder}/cancel', [SalesOrderController::class, 'cancel'])->name('sales.cancel')->middleware('role:management,admin');

    // Purchase Request
    Route::get('/purchasing/pr', [PurchasingController::class, 'prIndex'])->name('purchasing.pr.index');
    Route::get('/purchasing/pr/create', [PurchasingController::class, 'prCreate'])->name('purchasing.pr.create');
    Route::post('/purchasing/pr', [PurchasingController::class, 'prStore'])->name('purchasing.pr.store');
    Route::get('/purchasing/pr/{pr}', [PurchasingController::class, 'prShow'])->name('purchasing.pr.show');
    Route::patch('/purchasing/pr/{pr}/approve', [PurchasingController::class, 'prApprove'])->name('purchasing.pr.approve')->middleware('role:management,admin');
    Route::patch('/purchasing/pr/{pr}/reject', [PurchasingController::class, 'prReject'])->name('purchasing.pr.reject')->middleware('role:management,admin');

    // Purchase Order
    Route::get('/purchasing/po', [PurchasingController::class, 'poIndex'])->name('purchasing.po.index');
    Route::get('/purchasing/po/create', [PurchasingController::class, 'poCreate'])->name('purchasing.po.create');
    Route::post('/purchasing/po', [PurchasingController::class, 'poStore'])->name('purchasing.po.store');
    Route::get('/purchasing/po/{po}', [PurchasingController::class, 'poShow'])->name('purchasing.po.show');
    Route::get('/purchasing/po/{po}/pdf', [PurchasingController::class, 'poPdf'])->name('purchasing.po.pdf');
    Route::patch('/purchasing/po/{po}/send', [PurchasingController::class, 'poSend'])->name('purchasing.po.send');
    Route::patch('/purchasing/po/{po}/cancel', [PurchasingController::class, 'poCancel'])->name('purchasing.po.cancel');
    Route::patch('/purchasing/po/{po}/grpo-cancel', [PurchasingController::class, 'grpoCancel'])->name('purchasing.grpo.cancel');
    Route::post('/purchasing/po/{po}/gr', [PurchasingController::class, 'grStore'])->name('purchasing.gr.store');

    // Invoicing
    Route::get('/invoicing/sales', [InvoicingController::class, 'salesIndex'])->name('invoicing.sales.index');
    Route::get('/invoicing/sales/create', [InvoicingController::class, 'salesCreate'])->name('invoicing.sales.create');
    Route::post('/invoicing/sales', [InvoicingController::class, 'salesStore'])->name('invoicing.sales.store');
    Route::get('/invoicing/sales/{invoice}', [InvoicingController::class, 'salesShow'])->name('invoicing.sales.show');
    Route::post('/invoicing/sales/{invoice}/payment', [InvoicingController::class, 'salesPayment'])->name('invoicing.sales.payment');
    Route::get('/invoicing/sales/{invoice}/pdf', [InvoicingController::class, 'salesPdf'])->name('invoicing.sales.pdf');
    Route::get('/invoicing/purchase', [InvoicingController::class, 'purchaseIndex'])->name('invoicing.purchase.index');
    Route::get('/invoicing/purchase/create', [InvoicingController::class, 'purchaseCreate'])->name('invoicing.purchase.create');
    Route::post('/invoicing/purchase', [InvoicingController::class, 'purchaseStore'])->name('invoicing.purchase.store')->middleware('role:warehouse,admin');

    // Accounting - Supplier Payment
    Route::get('/accounting/supplier', [AccountingController::class, 'supplierIndex'])->name('accounting.supplier.index');
    Route::get('/accounting/supplier/create', [AccountingController::class, 'supplierCreate'])->name('accounting.supplier.create');
    Route::post('/accounting/supplier', [AccountingController::class, 'supplierStore'])->name('accounting.supplier.store');
    Route::patch('/accounting/supplier/{payment}/approve', [AccountingController::class, 'supplierApprove'])->name('accounting.supplier.approve')->middleware('role:management,admin');
    Route::patch('/accounting/supplier/{payment}/reject', [AccountingController::class, 'supplierReject'])->name('accounting.supplier.reject')->middleware('role:management,admin');

    // Accounting - Customer Payment
    Route::get('/accounting/customer', [AccountingController::class, 'customerIndex'])->name('accounting.customer.index');
    Route::get('/accounting/customer/create', [AccountingController::class, 'customerCreate'])->name('accounting.customer.create');
    Route::post('/accounting/customer', [AccountingController::class, 'customerStore'])->name('accounting.customer.store');

    // Inventory Transfer
    Route::get('/inventory/transfer', [InventoryController::class, 'index'])->name('inventory.transfer.index');
    Route::get('/inventory/transfer/{transfer}', [InventoryController::class, 'show'])->name('inventory.transfer.show');
    Route::patch('/inventory/transfer/{transfer}/process', [InventoryController::class, 'process'])->name('inventory.transfer.process');
    Route::get('/inventory/transfer/{transfer}/print', [InventoryController::class, 'print'])->name('inventory.transfer.print');
});
