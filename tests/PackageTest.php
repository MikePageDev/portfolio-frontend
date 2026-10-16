<?php

namespace MikePageDev\PortfolioFrontend\Tests;

class PackageTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertTrue(config('portfolio-frontend.enabled'));
        $this->assertSame('portfolio', config('portfolio-frontend.prefix'));
        $this->assertSame(['web'], config('portfolio-frontend.middleware'));
        $this->assertSame('portfolio-frontend::layout', config('portfolio-frontend.layout'));
    }

    public function test_default_layout_renders_pages(): void
    {
        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('<title>Portfolio</title>', false)
            ->assertSee('<main>', false);
    }
}
