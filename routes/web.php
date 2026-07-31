<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\PwaController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WidgetController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/articles/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{article:slug}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('articles.comments.store');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

Route::get('/widgets/{token}.js', [WidgetController::class, 'script'])->name('widgets.script');

Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');

Route::get('/rss.xml', [RssController::class, 'index'])->name('rss.index');
Route::get('/categories/{category:slug}/rss.xml', [RssController::class, 'category'])->name('rss.category');

Route::middleware('auth')->group(function () {
    Route::get('/abonnement', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('/abonnement/portail', [SubscriptionController::class, 'portal'])->name('subscription.portal');
    Route::post('/abonnement/resilier', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::get('/abonnement/confirmation', [SubscriptionController::class, 'success'])->name('subscription.success');

    Route::post('/push/subscribe', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    Route::get('/compte', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/compte', [AccountController::class, 'update'])->name('account.update');
    Route::post('/compte/interets', [AccountController::class, 'updateInterests'])->name('account.interests');
    Route::get('/compte/export', [AccountController::class, 'export'])->name('account.export');
    Route::delete('/compte', [AccountController::class, 'destroy'])->name('account.destroy');

    Route::get('/compte/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/compte/tickets/nouveau', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/compte/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/compte/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/compte/tickets/{ticket}/reponses', [TicketController::class, 'reply'])->name('tickets.reply');
});

Route::get('/login', [LoginController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');
Route::get('/inscription', [RegisterController::class, 'create'])->name('register')->middleware('guest');
Route::post('/inscription', [RegisterController::class, 'store'])->middleware('guest');

// Catch-all for CMS pages must stay last.
Route::get('/{page:slug}', [PageController::class, 'show'])->name('pages.show');
