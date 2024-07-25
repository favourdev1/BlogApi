<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\TokenMiddleware;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::middleware([TokenMiddleware::class])->group(function () {
    // ==================== Authentication Controller ===================
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');
    });

    // validating  routes authenticated user  
    Route::middleware('auth:api')->group(function () {
        // ======================Blog Controller Routes=================
        Route::controller(BlogController::class)->group(function () {
            Route::get('/blogs', 'index')->name('blogs');
            Route::post('/blogs', 'store')->name('blogs.store');
            Route::get('/blogs/{id}', 'show')->name('blogs.show');
            Route::put('/blogs/{id}', 'update')->name('blogs.update');
            Route::delete('/blogs/{id}', 'destroy')->name('blogs.destroy');
        });

        // =================Post Controller Routes==================
        Route::controller(PostController::class)->group(function () {
            Route::get('blogs/{blogId}/posts', 'index');
            Route::post('blogs/{blogId}/posts', 'store');
            Route::get('blogs/{blogId}/posts/{id}', 'show');
            Route::put('blogs/{blogId}/posts/{id}', 'update');
            Route::delete('blogs/{blogId}/posts/{id}', 'destroy');
            Route::post('posts/{postId}/like', 'likePost');
            Route::post('posts/{postId}/comment', 'commentOnPost');
        });
    });
});
