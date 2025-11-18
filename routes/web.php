<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\SettingController;

Route::middleware(['auth', 'role:karyawan'])->prefix('pos')->name('pos.')->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::post('/order/{order}/status', [PosController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/board-data', [PosController::class, 'fetchKanbanData'])->name('boardData');
});

Route::get('/', [LandingPageController::class, 'index'])->name('landing.index');
Route::get('/order', [CustomerOrderController::class, 'showStartForm'])->name('order.start');

Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'showCart'])->name('cart.index');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::post('/order/place/counter', [OrderController::class, 'placeOrderCounter'])->name('order.place.counter');
Route::post('/order/place/online', [OrderController::class, 'placeOrderOnline'])->name('order.place.online');
Route::get('/order/success/{order}', [OrderController::class, 'showSuccess'])->name('order.success');

Route::post('/order/start', [CustomerOrderController::class, 'handleStartForm'])->name('order.start.submit');

Route::get('/menu', [CustomerOrderController::class, 'showMenu'])->name('order.menu');


require __DIR__.'/auth.php';
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::resource('tables', TableController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::resource('users', UserManagementController::class);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});