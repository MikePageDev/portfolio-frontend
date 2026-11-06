<?php

namespace MikePageDev\PortfolioFrontend;

use Illuminate\Contracts\Foundation\Application;
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

    public function packageRegistered(): void
    {
        $this->app->bind(ProjectModelResolver::class, fn (Application $app): ProjectModelResolver => new ProjectModelResolver([
            $app->getNamespace().'Models\\PortfolioProject',
            'MikePageDev\\FilamentPortfolio\\Models\\Project',
        ]));
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../stubs/PortfolioProject.php.stub' => app_path('Models/PortfolioProject.php'),
        ], 'portfolio-frontend-model');

        $this->publishesMigrations([
            __DIR__.'/../database/migrations/create_portfolio_projects_table.php.stub' => database_path('migrations/'.date('Y_m_d_His').'_create_portfolio_projects_table.php'),
        ], 'portfolio-frontend-model');

        if (! config()->boolean('portfolio-frontend.enabled')) {
            return;
        }

        Route::middleware(config()->array('portfolio-frontend.middleware'))
            ->prefix(config()->string('portfolio-frontend.prefix'))
            ->group(__DIR__.'/../routes/web.php');
    }
}
