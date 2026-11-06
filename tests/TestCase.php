<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use App\Models\PortfolioProject;
use Illuminate\Support\Str;
use MikePageDev\PortfolioFrontend\PortfolioFrontendServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

// The suite runs against the same model file users publish.
require_once __DIR__.'/../stubs/PortfolioProject.php.stub';

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
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
        $migration = include __DIR__.'/../database/migrations/create_portfolio_projects_table.php.stub';
        $migration->up();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function project(array $attributes = [], bool $published = true): PortfolioProject
    {
        return PortfolioProject::query()->create([
            'title' => 'A Project',
            'slug' => 'project-'.Str::lower(Str::random(8)),
            'summary' => 'A summary.',
            'is_published' => $published,
            'sort_order' => 0,
            ...$attributes,
        ]);
    }
}
