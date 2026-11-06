<?php

namespace MikePageDev\PortfolioFrontend\Tests;

class PackageTest extends TestCase
{
    public function test_default_layout_renders_pages(): void
    {
        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('<title>Portfolio</title>', false)
            ->assertSee('<main>', false);
    }
}
