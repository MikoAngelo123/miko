<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products/{product}', [AdminController::class, 'showProduct'])->name('admin.products.show');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('admin.products.destroy');
    Route::get('/settings', [AdminController::class, 'showSettings'])->name('admin.settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{order}/complete', [AdminController::class, 'completeOrder'])->name('admin.orders.complete');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/products', [CustomerController::class, 'products'])->name('customer.products');
    Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
    Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::patch('/cart/{item}', [CustomerController::class, 'updateCartItem'])->name('customer.cart.update');
    Route::delete('/cart/{item}', [CustomerController::class, 'removeCartItem'])->name('customer.cart.remove');
    Route::delete('/orders/{order}', [CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
    Route::get('/settings', [CustomerController::class, 'showSettings'])->name('customer.settings');
    Route::put('/settings', [CustomerController::class, 'updateSettings'])->name('customer.settings.update');
});
