<?php

return [

    // Turn the public portfolio routes off without uninstalling the package.
    'enabled' => true,

    // URL prefix: /portfolio and /portfolio/{slug}.
    'prefix' => 'portfolio',

    'middleware' => ['web'],

    // Blade component the pages render inside. It receives `title` and `description` (nullable) props.
    'layout' => 'portfolio-frontend::layout',

    // Filesystem disk the project screenshots are stored on.
    'disk' => env('PORTFOLIO_DISK', 'public'),

    // Index page title and heading.
    'title' => 'Portfolio',

    // Index page meta description. Null leaves it out.
    'description' => null,

    // Paragraph under the index heading. Null leaves it out.
    'intro' => null,

    // Shown on the index page when there are no published projects.
    'empty_message' => 'No projects yet.',

    // Heading above a project's role on the case-study page.
    'role_heading' => 'Role',

    // Route the contact prompts link to. Null turns the prompts off; so does a route that doesn't exist.
    'contact_route' => 'contact',

    // Prompt after the empty message on the index page. [text] marks the link. Null leaves it out.
    'index_contact_prompt' => null,

    // Prompt at the end of a case study. [text] marks the link. Null leaves it out.
    'case_study_contact_prompt' => 'Interested in something similar? [Get in touch].',

];
