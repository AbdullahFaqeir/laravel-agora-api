<?php

use Illuminate\Support\Facades\Route;
use AbdullahFaqeir\LaravelAgoraApi\Http\Controllers\AgoraController;

Route::controller(AgoraController::class)
     ->name('laravel-agora-api.')
     ->group(function () {
         Route::post('/retrieve-token', 'retrieveToken')
              ->name('laravel-agora-api.retrieve-token');
         Route::post('/place-call', 'placeCall')
              ->name('laravel-agora-api.place-call');
         Route::post('/accept-call', 'acceptCall')
              ->name('laravel-agora-api.accept-call');
         Route::post('/reject-call', 'rejectCall')
              ->name('laravel-agora-api.reject-call');
     });
