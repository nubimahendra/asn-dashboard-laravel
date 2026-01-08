<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/sync-pegawai', [App\Http\Controllers\SyncController::class, 'sync'])->name('sync.pegawai');
