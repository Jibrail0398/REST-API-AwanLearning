<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/courses', function () {
    return view('courses');
});
Route::get('/payment', function () {
    return view('payment');
});

Route::get('/payment/{transactionId}', [TransactionController::class, 'showPaymentPage'])->name('payment');
 Route::post('/payment', [TransactionController::class, 'processPayment'])->name('process.payment');
