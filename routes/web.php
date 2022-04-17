<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\StripeConnectController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Stripe connect on-boarding routes
    Route::group(['prefix' => 'stripe'], function () {
        Route::get('connect', [StripeConnectController::class, 'connect'])->name('stripe.connect');
        Route::get('connect/save/{token}', [StripeConnectController::class, 'saveStripeAccount'])->name('stripe.save.account');
    });

    Route::get('make-payment', [StripeConnectController::class, 'makePaymentPage'])->name('make-payment');
    Route::post('store-payment', [StripeConnectController::class, 'storePayment'])->name('store-payment');
});

