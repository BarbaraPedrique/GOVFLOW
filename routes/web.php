<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/registro', function () {
    return view('register');
});

Route::get('/inicio', function () {
    return view('inicio');
});
