<?php

// Public portfolio routes. Registered by PortfolioFrontendServiceProvider with the configured prefix and middleware.

use Illuminate\Support\Facades\Route;
use MikePageDev\PortfolioFrontend\Http\Controllers\PortfolioController;

Route::get('/', [PortfolioController::class, 'index'])->name('portfolio.index');

Route::get('/{slug}', [PortfolioController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('portfolio.show');
