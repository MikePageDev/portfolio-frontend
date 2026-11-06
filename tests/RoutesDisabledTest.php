<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use Illuminate\Support\Facades\Route;

class RoutesDisabledTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('portfolio-frontend.enabled', false);
    }

    public function test_routes_are_not_registered_when_disabled(): void
    {
        $this->assertFalse(Route::has('portfolio.index'));
        $this->get('/portfolio')->assertNotFound();
    }
}
