<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopMusiciansController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MusicianController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\PlaybackController;

use App\Http\Controllers\RadioController;
use App\Http\Controllers\MerchController;
use App\Http\Controllers\ConcertController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/radio', [RadioController::class, 'index'])->name('radio.index');

Route::get('/merchandising', [MerchController::class, 'index'])->name('merch.index');
Route::get('/conciertos', [ConcertController::class, 'index'])->name('concerts.index');
Route::get('/conciertos/map-data', [ConcertController::class, 'mapData'])->name('concerts.map-data');

Route::get('/top-musicos', [TopMusiciansController::class, 'index'])->name('top-musicians.index');
Route::get('/top-musicos/data', [TopMusiciansController::class, 'data'])->name('top-musicians.data');

// Endpoint para registrar reproducción de canción
Route::post('/songs/{song}/play', [PlaybackController::class, 'recordPlay'])->name('song.record-play');

Route::get('/radio/community/{community}', [RadioController::class, 'getSongsByCommunity'])->name('radio.community');
Route::get('/radio/{city}', [RadioController::class, 'getSongsByCity'])->name('radio.get');

// Stripe webhook (no auth / no CSRF — excluded in bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// Checkout (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/account/delete', [AccountController::class, 'delete'])->name('account.delete');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/checkout/{type}/{id}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/stripe-session', [CheckoutController::class, 'createStripeSession'])->name('checkout.stripe-session');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    // Ticket downloads (static route BEFORE dynamic to avoid conflict)
    Route::get('/orders/tickets', [TicketController::class, 'downloadMultiple'])->name('ticket.download-multiple');
    Route::get('/orders/{order}/ticket', [TicketController::class, 'download'])->name('ticket.download');
    // Email sending disabled - users can only download/print tickets
    // Route::post('/orders/{order}/ticket/email', [TicketController::class, 'sendEmail'])->name('ticket.email');
    // Route::post('/orders/tickets/email', [TicketController::class, 'sendEmailMultiple'])->name('ticket.email-multiple');

    // Cart routes - DISABLED: Direct checkout only
    // Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
    // Route::post('/carrito/add', [CartController::class, 'add'])->name('cart.add');
    // Route::post('/carrito/update', [CartController::class, 'update'])->name('cart.update');
    // Route::post('/carrito/remove', [CartController::class, 'remove'])->name('cart.remove');
    // Route::post('/carrito/clear', [CartController::class, 'clear'])->name('cart.clear');
    // Route::post('/carrito/checkout', [CheckoutController::class, 'cartCheckout'])->name('cart.checkout');
});

Route::get('/dashboard', function () {
    $communities = App\Http\Controllers\ConcertController::getCommunitiesMap();
    $orders = App\Models\Order::where('user_id', Auth::id())
        ->orderByDesc('created_at')
        ->get();
    return view('dashboard', compact('communities', 'orders'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/image', [ProfileController::class, 'updateImage'])->name('profile.update-image');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('musicians', MusicianController::class);
    Route::post('/songs', [SongController::class, 'store'])->name('songs.store');
    Route::delete('/songs/{song}', [SongController::class, 'destroy'])->name('songs.destroy');
    Route::post('/albums', [SongController::class, 'storeAlbum'])->name('albums.store');
    Route::resource('concerts', ConcertController::class);
    Route::resource('merch', MerchController::class);

    // Suscripciones
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/checkout', [SubscriptionController::class, 'createCheckoutSession'])->name('subscriptions.checkout');
    Route::get('/subscriptions/success', [SubscriptionController::class, 'success'])->name('subscriptions.success');
    Route::get('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::delete('/subscriptions', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    Route::post('/subscriptions/webhook', [SubscriptionController::class, 'webhook'])->name('subscriptions.webhook');

    // Panel de administración
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/musicians', [AdminController::class, 'musicians'])->name('musicians');
        Route::get('/musicians/{musician}', [AdminController::class, 'musicianDetail'])->name('musicians.detail');
        Route::delete('/musicians/{musician}', [AdminController::class, 'deleteMusician'])->name('musicians.destroy');
        Route::get('/subscriptions', [AdminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/subscriptions/{subscription}', [AdminController::class, 'subscriptionDetail'])->name('subscriptions.detail');
        Route::post('/subscriptions/{subscription}/cancel', [AdminController::class, 'cancelSubscription'])->name('subscriptions.cancel');
        Route::post('/subscriptions/{subscription}/reactivate', [AdminController::class, 'reactivateSubscription'])->name('subscriptions.reactivate');
        Route::delete('/songs/{song}', [AdminController::class, 'deleteSong'])->name('songs.destroy');
        Route::delete('/concerts/{concert}', [AdminController::class, 'deleteConcert'])->name('concerts.destroy');
        Route::delete('/merch/{merch}', [AdminController::class, 'deleteMerch'])->name('merch.destroy');
        Route::get('/api/stats', [AdminController::class, 'apiStats'])->name('api.stats');
    });
});

require __DIR__ . '/auth.php';
