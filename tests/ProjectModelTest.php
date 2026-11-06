<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use App\Models\PortfolioProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use MikePageDev\PortfolioFrontend\MissingProjectModel;
use MikePageDev\PortfolioFrontend\PortfolioFrontendServiceProvider;
use MikePageDev\PortfolioFrontend\ProjectModelResolver;
use stdClass;

class ProjectModelTest extends TestCase
{
    public function test_the_published_model_is_looked_for_before_the_filament_model(): void
    {
        $this->assertSame(
            ['App\Models\PortfolioProject', 'MikePageDev\FilamentPortfolio\Models\Project'],
            app(ProjectModelResolver::class)->candidates(),
        );
    }

    public function test_the_first_existing_model_is_used(): void
    {
        $resolver = new ProjectModelResolver(['Missing\Model', stdClass::class, PortfolioProject::class, Model::class]);

        $this->assertSame(PortfolioProject::class, $resolver->resolve());
    }

    public function test_pages_explain_what_to_do_when_there_is_no_model(): void
    {
        $this->app->instance(ProjectModelResolver::class, new ProjectModelResolver(['Missing\Model']));
        $this->withoutExceptionHandling();

        $this->expectException(MissingProjectModel::class);
        $this->expectExceptionMessage('vendor:publish --tag=portfolio-frontend-model');

        $this->get('/portfolio');
    }

    public function test_the_model_tag_publishes_the_model_and_the_migration(): void
    {
        $targets = array_values(ServiceProvider::pathsToPublish(PortfolioFrontendServiceProvider::class, 'portfolio-frontend-model'));

        $this->assertCount(2, $targets);
        $this->assertStringEndsWith('app/Models/PortfolioProject.php', $targets[0]);
        $this->assertMatchesRegularExpression('#database/migrations/\d{4}_\d{2}_\d{2}_\d{6}_create_portfolio_projects_table\.php$#', $targets[1]);
    }

    public function test_the_migration_leaves_an_existing_table_alone(): void
    {
        $this->project(['slug' => 'kept']);

        $migration = include __DIR__.'/../database/migrations/create_portfolio_projects_table.php.stub';
        $migration->up();

        $this->assertSame(1, PortfolioProject::query()->where('slug', 'kept')->count());
    }
}
