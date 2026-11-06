<?php

namespace MikePageDev\PortfolioFrontend\Tests;

class LegacyConfigTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['view']->addLocation(__DIR__.'/Fixtures/views');
    }

    /**
     * Runs before the package providers register, so mergeConfigFrom sees this as the host app's config.
     */
    protected function resolveApplicationConfiguration($app): void
    {
        parent::resolveApplicationConfiguration($app);

        // A host app whose published config predates the wording keys.
        $app['config']->set('portfolio-frontend', [
            'enabled' => true,
            'prefix' => 'portfolio',
            'middleware' => ['web'],
            'layout' => 'test-layout',
        ]);
    }

    public function test_missing_wording_keys_fall_back_to_defaults(): void
    {
        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('Portfolio</h1>', false)
            ->assertSee('No projects yet.');
    }
}
