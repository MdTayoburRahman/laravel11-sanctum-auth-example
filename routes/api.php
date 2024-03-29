<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


//open routes
Route::post('/register', [ApiController::class,'register']);
Route::post('/login', [ApiController::class, 'login']);

//protected routes
Route::group([
    "middleware" => ["auth:sanctum"]
], function(){
    Route::get('/profile',[ApiController::class, 'profile']);
    Route::get('/logout',[ApiController::class, 'logout']);
});