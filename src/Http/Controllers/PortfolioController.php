<?php

namespace MikePageDev\PortfolioFrontend\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use MikePageDev\FilamentPortfolio\Models\Project;

class PortfolioController extends Controller
{
    public function index(): View
    {
        return view('portfolio-frontend::index', [
            'projects' => Project::query()->published()->ordered()->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::query()->published()->where('slug', $slug)->firstOrFail();

        return view('portfolio-frontend::show', ['project' => $project]);
    }
}
