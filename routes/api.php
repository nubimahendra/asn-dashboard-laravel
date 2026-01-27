<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/chat/history', [App\Http\Controllers\ChatController::class, 'history']);
    Route::post('/chat/send', [App\Http\Controllers\ChatController::class, 'send']);

    // Admin Chat
    Route::get('/chat/conversations', [App\Http\Controllers\ChatController::class, 'conversations']);
    Route::get('/chat/admin/history/{userId}', [App\Http\Controllers\ChatController::class, 'userHistory']);
    Route::post('/chat/admin/reply', [App\Http\Controllers\ChatController::class, 'reply']);
});


