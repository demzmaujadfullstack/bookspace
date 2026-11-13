<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\StockController as AdminStockController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\User\HelpController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');
Route::get('/api/search', [BookController::class, 'searchSuggest'])->name('books.search.suggest');

// Redirect for /dashboard
Route::get('/dashboard', function () {
    return auth()->user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('auth')->name('dashboard');

// ─── Authenticated user ───────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Add to cart & Review & Wishlist
    Route::post('/books/{book}/cart', [BookController::class, 'addToCart'])->name('books.cart.add');
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('books.reviews.store');
    Route::get('/favorit', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/favorit/{book}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('/cart/items/{item}', [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/{order}/payment', [CheckoutController::class, 'uploadProof'])->name('checkout.payment.upload');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/{order}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // User area
    Route::prefix('akun')->name('user.')->group(function () {
        Route::get('/', [UserProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [UserProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [UserProfileController::class, 'update'])->name('profile.update');
        Route::get('/pesanan', [OrderController::class, 'index'])->name('orders');
        Route::get('/pesanan/{order}', [OrderController::class, 'show'])->name('orders.show');
    });

    // Help / Live Chat
    Route::prefix('bantuan')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::post('/conversations', [HelpController::class, 'startConversation'])->name('conversations.start');
        Route::get('/conversations/{conversation}', [HelpController::class, 'show'])->name('conversations.show');
        Route::post('/conversations/{conversation}/messages', [HelpController::class, 'sendMessage'])->name('conversations.messages.store');
        Route::get('/conversations/{conversation}/poll', [HelpController::class, 'poll'])->name('conversations.poll');
    });
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Books
    Route::resource('books', AdminBookController::class);

    // Categories
    Route::resource('categories', AdminCategoryController::class);

    // Stock
    Route::get('/stok', [AdminStockController::class, 'index'])->name('stock.index');
    Route::patch('/stok/{book}', [AdminStockController::class, 'update'])->name('stock.update');
    Route::get('/stok/{book}/riwayat', [AdminStockController::class, 'history'])->name('stock.history');

    // Banners
    Route::resource('banners', AdminBannerController::class);
    Route::patch('/banners/{banner}/toggle', [AdminBannerController::class, 'toggle'])->name('banners.toggle');
    Route::patch('/banners/{banner}/order', [AdminBannerController::class, 'updateOrder'])->name('banners.order');

    // Orders / Transactions
    Route::get('/transaksi', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/transaksi/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/transaksi/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/transaksi/{order}/payment/verify', [AdminOrderController::class, 'verifyPayment'])->name('orders.payment.verify');
    Route::patch('/transaksi/{order}/payment/reject', [AdminOrderController::class, 'rejectPayment'])->name('orders.payment.reject');

    // Users
    Route::resource('users', AdminUserController::class)->only(['index', 'show', 'destroy']);

    // Reports
    Route::get('/laporan', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [AdminReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/pengaturan', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::patch('/pengaturan', [AdminSettingController::class, 'update'])->name('settings.update');

    // Admin Chat
    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [AdminChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages', [AdminChatController::class, 'sendMessage'])->name('chat.messages.store');
    Route::patch('/chat/{conversation}/close', [AdminChatController::class, 'close'])->name('chat.close');
    Route::get('/chat/{conversation}/poll', [AdminChatController::class, 'poll'])->name('chat.poll');
});

require __DIR__.'/auth.php';
