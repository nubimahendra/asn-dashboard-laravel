<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/sync-pegawai', [App\Http\Controllers\SyncController::class, 'sync'])->name('sync.pegawai');

use App\Http\Controllers\ChatAdminController;
Route::prefix('admin/chat')->name('admin.chat.')->group(function () {
    Route::get('/', [ChatAdminController::class, 'index'])->name('index');
    Route::get('/{phone}', [ChatAdminController::class, 'show'])->name('show');
    Route::post('/reply', [ChatAdminController::class, 'reply'])->name('reply');
});

use App\Http\Controllers\WhatsAppController;
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'handleWebhook'])->name('webhook.whatsapp');
