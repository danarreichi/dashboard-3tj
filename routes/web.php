<?php

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

Route::get('/', function () {
    return redirect('login');
});

Route::get('/inventory', function () {
    return view('inventory');
})->name('inventory');

Route::get('/menu-category', function () {
    return view('menu-category');
})->name('menu-category');

Route::get('/menu', function () {
    return view('menu');
})->name('menu');

Route::get('/account', function () {
    return view('accounts');
})->name('account');

Route::get('login', function () {
    return view('login');
})->name('login');
