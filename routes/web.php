<?php

use App\Http\Controllers\phonepeController;
use App\Http\Controllers\carouselcontroller;
use App\Http\Controllers\Automatically;
use Illuminate\Support\Facades\Route;




Route::post('/webhook',[Automatically::class,'payload']);
//Route::get('/webhook',[Automatically::class,'show']);
