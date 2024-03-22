<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\API\CommentsController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\Auth\UserProfileController;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\UserProfileImageController;

Route::middleware('cors')->group(function () {
    Route::prefix('authenticate')->group(function () {
        Route::middleware('validate.login')->post('login', [UserController::class, 'login']);
        Route::middleware('jwt.refresh')->post('refresh', [UserController::class, 'refresh']);
        Route::middleware('auth:api')->post('logout', [UserController::class, 'logout']);
        Route::middleware('validate.register')->post('register', [UserRegisterController::class, 'register']);
    });

    Route::prefix('password')->group(function () {
        Route::post('/reset-link', [PasswordResetController::class, 'sendResetLink']);
        Route::post('/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');
    });

    Route::middleware('auth:api')->group(function () {
        Route::get('/user/me', [UserProfileController::class, 'me']);
        Route::get('/user/{user}', [UserProfileController::class, 'profile']);
        Route::middleware('validate.profile')->put('profile/update', [UserProfileController::class, 'updateProfile']);
        Route::middleware('validate.profile.image')->post('profile/upload-image', [UserProfileImageController::class, 'uploadProfileImage']);
        Route::delete('profile/delete-image', [UserProfileImageController::class, 'deleteProfileImage']);
    });

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('post', PostController::class);
        Route::get('post/search/{query}', [PostController::class, 'search']);
        Route::post('post/{post}/comments', [CommentsController::class, 'store']);
        Route::put('/post/comments/{comments}', [CommentsController::class, 'update']);
        Route::delete('/post/comments/{comments}', [CommentsController::class, 'destroy']);
    });

    Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'getNotifications']);
    Route::middleware('auth:api')->delete('/notifications', [NotificationController::class, 'deleteNotifications']);
});
