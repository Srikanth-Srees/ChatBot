<?php

use App\Http\Controllers\phonepeController;
use App\Http\Controllers\carouselcontroller;
use App\Http\Controllers\Automatically;
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
//Route::post('/webhook',[phonepeController::class,'pay']);
//Route::get('/webhook',[phonepeController::class,'show']);


Route::any('/ResumableUploadAPI', [carouselcontroller::class, 'ResumableUploadAPI'])->name('ResumableUploadAPI');
Route::any('/carouselTemplate', [carouselcontroller::class, 'carouselTemplate'])->name('carouselTemplate');
Route::any('/test', [carouselcontroller::class, 'test'])->name('test');
Route::any('/sendTemplate', [Automatically::class, 'sendTemplate'])->name('sendTemplate');
Route::any('/formAddress', [Automatically::class, 'formAddress'])->name('formAddress');
Route::any('/sendList', [Automatically::class, 'sendList'])->name('sendList');
Route::post('/webhook',[Automatically::class,'pay']);
//Route::get('/webhook',[Automatically::class,'show']);
Route::get('/welcome', [Automatically::class, 'welcome'])->name('welcome');
Route::get('/bookFood',[Automatically::class,'bookFood']);