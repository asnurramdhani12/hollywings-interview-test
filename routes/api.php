<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth;
use App\Http\Middleware\JwtMiddleware;

// for auth routes
Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [Auth::class, 'register']);
    Route::post('/login', [Auth::class, 'login']);
    Route::post('/logout', [Auth::class, 'logout']);
    Route::post('/refresh', [Auth::class, 'refresh'])->middleware([JwtMiddleware::class]);
    Route::get('/me', [Auth::class, 'me'])->middleware([JwtMiddleware::class . ':*']);
});

// for category book routes
Route::group(['prefix' => 'category_book', 'middleware' => [JwtMiddleware::class . ':admin']], function () {
    Route::get('/', [App\Http\Controllers\Api\CategoryBook::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\CategoryBook::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\CategoryBook::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\CategoryBook::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\CategoryBook::class, 'destroy']);
});

// for book routes
Route::group(['prefix' => 'book', 'middleware' => [JwtMiddleware::class . ':admin']], function () {
    Route::get('/', [App\Http\Controllers\Api\Book::class, 'index']);
    Route::post('/', [App\Http\Controllers\Api\Book::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\Book::class, 'show']);
    Route::put('/{id}', [App\Http\Controllers\Api\Book::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\Book::class, 'destroy']);
});

// for borrowing and returning book routes
Route::group(['prefix' => 'borrowing_books'], function () {
    Route::post('/borrow', [App\Http\Controllers\Api\BorrowingBooks::class, 'borrow'])->middleware([JwtMiddleware::class . ':user']);
    Route::post('/accepted/{id}', [App\Http\Controllers\Api\BorrowingBooks::class, 'accepted'])->middleware([JwtMiddleware::class . ':admin']);
    Route::post('/returned/{id}', [App\Http\Controllers\Api\BorrowingBooks::class, 'returned'])->middleware([JwtMiddleware::class . ':admin']);
    Route::get('/list', [App\Http\Controllers\Api\BorrowingBooks::class, 'list'])->middleware([JwtMiddleware::class . ':*']);
});
