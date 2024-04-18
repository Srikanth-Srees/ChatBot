<?php

use App\Http\Controllers\phonepeController;
use App\Http\Controllers\carouselcontroller;
use Illuminate\Support\Facades\Route;

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
 

//Route::get('/',[phonepeController::class,'sendList']);
//Route::get('/replyToMessage',[phonepeController::class,'replyToMessage']);
//Route::post('/',[phonepeController::class,'pay']);
Route::post('/webhook',[phonepeController::class,'pay']);
Route::get('/webhook/phonepe',[phonepeController::class,'phonePe']);
//Route::any('/webhook/sendAddressConfirmation', [phonepeController::class, 'sendAddressConfirmation'])->name('/webhook/sendAddressConfirmation');
Route::any('/webhook/response', [phonepeController::class, 'response'])->name('response');

Route::get('/carousel',[phonepeController::class, 'carousel']);
//Route::get('/ResumableUploadAPI',[phonepeController::class, 'ResumableUploadAPI'])->name('ResumableUploadAPI');
Route::any('/uploadImage',[phonepeController::class, 'uploadImage'])->name('uploadImage');
Route::any('/uploadMedia', [phonepeController::class, 'uploadMedia'])->name('uploadMedia');
Route::any('/ResumableUploadAPI', [carouselcontroller::class, 'ResumableUploadAPI'])->name('ResumableUploadAPI');
Route::any('/carouselTemplate', [carouselcontroller::class, 'carouselTemplate'])->name('carouselTemplate');