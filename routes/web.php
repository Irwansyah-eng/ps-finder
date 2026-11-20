<?php

use App\Http\Controllers\RentalController;
use App\Http\Controllers\SurveyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RentalController::class, 'index'])->name('home');
Route::get('/api/ranking', [RentalController::class, 'ranking'])->name('ranking');
Route::get('/survei', [SurveyController::class, 'create'])->name('survei');
Route::post('/survei', [SurveyController::class, 'store'])->middleware('throttle:10,1')->name('survei.store');
