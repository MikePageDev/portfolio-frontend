<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use MikePageDev\FilamentPortfolio\FilamentPortfolioServiceProvider;
use MikePageDev\PortfolioFrontend\PortfolioFrontendServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FilamentPortfolioServiceProvider::class,
            PortfolioFrontendServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.name', 'Test Site');
    }

    protected function defineDatabaseMigrations(): void
    {
        // Resolve the model package's migration from wherever Composer installed it.
        $packageRoot = dirname((string) (new ReflectionClass(FilamentPortfolioServiceProvider::class))->getFileName(), 2);
        $migration = include $packageRoot.'/database/migrations/create_portfolio_projects_table.php.stub';
        $migration->up();
    }
}
