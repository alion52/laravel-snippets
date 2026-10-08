<?php

use App\Http\Controllers\GeoController;
use Illuminate\Support\Facades\Route;

// Составные URL
Route::get('/{region}/{plant}/', [GeoController::class, 'showRegionPlant'])
    ->where('region', '[a-z0-9-]+')
    ->where('plant', '[a-z0-9-]+');

// Одиночные URL
Route::get('/{slug}/', [GeoController::class, 'show'])
    ->where('slug', '[a-z0-9-]+');
