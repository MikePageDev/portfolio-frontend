<?php

namespace MikePageDev\PortfolioFrontend\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\Controller;
use MikePageDev\PortfolioFrontend\ProjectModelResolver;

class PortfolioController extends Controller
{
    public function index(ProjectModelResolver $models): View
    {
        return view('portfolio-frontend::index', [
            'projects' => $models->resolve()::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function show(ProjectModelResolver $models, string $slug): View
    {
        $project = $models->resolve()::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('portfolio-frontend::show', ['project' => $project]);
    }
}
