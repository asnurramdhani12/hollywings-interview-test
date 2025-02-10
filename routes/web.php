<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth;

Route::get('/', function () {
    return view('welcome');
});
