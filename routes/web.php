<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Vendor\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Customer Web Routes
|--------------------------------------------------------------------------
*/

// Home & Catalog
Route::get('/', [CustomerProductController::class, 'index'])->name('home');
Route::get('/products', [CustomerProductController::class, 'index'])->name('customer.products.index');
Route::get('/products/{slug}', [CustomerProductController::class, 'show'])->name('customer.products.show');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    // Forgot password
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetPassword'])->name('password.email');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer Protected Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Address Routes
    Route::resource('addresses', AddressController::class)->except(['show']);
    Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');

    // Customer Order History
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');

    // Customer Reviews
    Route::post('/products/{product}/reviews', [CustomerReviewController::class, 'store'])->name('customer.reviews.store');
    Route::put('/reviews/{id}', [CustomerReviewController::class, 'update'])->name('customer.reviews.update');
    Route::delete('/reviews/{id}', [CustomerReviewController::class, 'destroy'])->name('customer.reviews.destroy');

    // Customer Profile
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('customer.profile');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Admin Profile
        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        // Vendors
        Route::get('/vendors', [VendorController::class, 'index'])->name('vendors.index');
        Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendors.create');
        Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
        Route::get('/vendors/{id}', [VendorController::class, 'show'])->name('vendors.show');
        Route::get('/vendors/{id}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
        Route::put('/vendors/{id}', [VendorController::class, 'update'])->name('vendors.update');
        Route::delete('/vendors/{id}', [VendorController::class, 'destroy'])->name('vendors.destroy');
        Route::post('/vendors/{id}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
        Route::post('/vendors/{id}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
        Route::post('/vendors/{id}/block', [VendorController::class, 'block'])->name('vendors.block');
        Route::post('/vendors/{id}/unblock', [VendorController::class, 'unblock'])->name('vendors.unblock');

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
        Route::post('/customers/{id}/block', [CustomerController::class, 'block'])->name('customers.block');
        Route::post('/customers/{id}/unblock', [CustomerController::class, 'unblock'])->name('customers.unblock');

        // Categories CRUD
        Route::resource('categories', CategoryController::class);

        // Products
        Route::resource('products', ProductController::class)->except(['create', 'store']);

        // Orders
        Route::get('/orders/export', [OrderController::class, 'export'])->name('orders.export');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

/*
|--------------------------------------------------------------------------
| Vendor Web Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:vendor'])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Products CRUD
        Route::resource('products', App\Http\Controllers\Vendor\ProductController::class);
        Route::post('/products/{product}/images/{image}/primary', [App\Http\Controllers\Vendor\ProductController::class, 'setPrimaryImage'])->name('products.images.primary');
        Route::delete('/products/{product}/images/{image}', [App\Http\Controllers\Vendor\ProductController::class, 'destroyImage'])->name('products.images.destroy');

        // Orders
        Route::get('/orders/export', [App\Http\Controllers\Vendor\OrderController::class, 'export'])->name('orders.export');
        Route::get('/orders', [App\Http\Controllers\Vendor\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [App\Http\Controllers\Vendor\OrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{id}/status', [App\Http\Controllers\Vendor\OrderController::class, 'updateStatus'])->name('orders.status.update');
    });
