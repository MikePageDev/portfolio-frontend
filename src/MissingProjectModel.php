<?php

namespace MikePageDev\PortfolioFrontend;

use RuntimeException;

class MissingProjectModel extends RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            'portfolio-frontend needs a project model. Install mikepagedev/filament-portfolio, or run: php artisan vendor:publish --tag=portfolio-frontend-model'
        );
    }
}
