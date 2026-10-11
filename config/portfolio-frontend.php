<?php

return [

    // Turn the public portfolio routes off without uninstalling the package.
    'enabled' => true,

    // URL prefix: /portfolio and /portfolio/{slug}.
    'prefix' => 'portfolio',

    'middleware' => ['web'],

    // Blade component the pages render inside. It receives `title` and `description` (nullable) props.
    'layout' => 'portfolio-frontend::layout',

];
