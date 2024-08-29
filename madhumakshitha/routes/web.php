<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PhonepeController;
use App\Http\Controllers\AuthController;
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


// Route::get('/payment', function () {
//     return view('Products');
// });
Route::any('/product', [ProductsController::class, 'product'])->name('product');
Route::any('/carousel', [ProductsController::class, 'carousel'])->name('carousel');
Route::any('/scrape', [ProductsController::class, 'scrape'])->name('scrape');

//Route::post('/webhook', [WebhookController::class, 'handleWebhook'])->name('webhook.handle');
//Route::get('/webhook', [WebhookController::class, 'verify'])->name('webhook');
Route::any('/redirectProducts', [WebhookController::class, 'selectedProducts'])->name('redirectProducts');
Route::any('/newUser', [WebhookController::class, 'newUser'])->name('newUser');
Route::any('/ResumableUploadAPI', [WebhookController::class, 'ResumableUploadAPI'])->name('ResumableUploadAPI');
Route::any('/carousel_2', [WebhookController::class, 'carousel_2'])->name('carousel_2');
Route::any('/carousel', [WebhookController::class, 'carousel'])->name('carousel');

Route::any('/payment', [PhonepeController::class, 'payment'])->name('payment');
Route::any('/phonepe', [PhonepeController::class, 'phonepe'])->name('phonepe');
Route::any('/phonepeResponse', [PhonepeController::class, 'phonepeResponse'])->name('phonepeResponse');


Route::get('/', function () {
    return view('auth\login');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::any('/dashboard', [AuthController::class, 'dashboard'])->name('display')->middleware('auth');