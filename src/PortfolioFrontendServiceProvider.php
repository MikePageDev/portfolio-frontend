<?php

namespace MikePageDev\PortfolioFrontend;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PortfolioFrontendServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('portfolio-frontend')
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        if (! config()->boolean('portfolio-frontend.enabled')) {
            return;
        }

        Route::middleware(config()->array('portfolio-frontend.middleware'))
            ->prefix(config()->string('portfolio-frontend.prefix'))
            ->group(__DIR__.'/../routes/web.php');
    }
}
