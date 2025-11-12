<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\master\master_barangController;
use App\Http\Controllers\system\AuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/postlogin', [AuthController::class, 'postLogin'])->name('postlogin');
Route::get('/logout', [HomeController::class, 'logout'])->name('home.logout');

Route::get('/', [HomeController::class, 'index'])->name('/');
Route::get('/check-session', [HomeController::class, 'checkSession'])->name('check-session');
Route::get('/fresh-data-customer', [HomeController::class, 'freshdatacustomer'])->name('fresh-data-customer');
