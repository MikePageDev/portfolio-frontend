<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use MikePageDev\FilamentPortfolio\Models\Project;

class PortfolioPagesTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['view']->addLocation(__DIR__.'/Fixtures/views');
        $app['config']->set('portfolio-frontend.layout', 'test-layout');
    }

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    private function demo(): Project
    {
        return Project::factory()->published()->create([
            'title' => 'Demo App',
            'slug' => 'demo-app',
            'summary' => 'Demo summary.',
            'problem' => 'Demo problem.',
            'role' => 'Demo role.',
            'tech' => ['Laravel', 'Filament'],
            'case_study' => [['heading' => 'Demo Approach', 'body' => 'Demo approach body.']],
            'images' => [['path' => 'portfolio/demo.png', 'alt' => 'Demo screenshot']],
            'live_url' => 'https://demo.example.test',
            'source_url' => 'https://github.com/example/demo',
            'sort_order' => 1,
        ]);
    }

    public function test_index_lists_published_projects_in_order_inside_the_layout(): void
    {
        $this->demo();
        Project::factory()->published()->create(['title' => 'Earlier Project', 'slug' => 'earlier', 'sort_order' => 0]);
        Project::factory()->create(['title' => 'Draft Project', 'slug' => 'draft']);

        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('<title>LAYOUT: Portfolio</title>', false)
            ->assertSeeInOrder(['Earlier Project', 'Demo App', 'Demo summary.', 'Laravel'])
            ->assertDontSee('Draft Project')
            ->assertSee('href="'.route('portfolio.show', 'demo-app').'"', false)
            ->assertSee('src="'.Storage::disk('public')->url('portfolio/demo.png').'"', false);
    }

    public function test_index_shows_an_empty_state(): void
    {
        $this->get('/portfolio')
            ->assertOk()
            ->assertDontSee('<article', false)
            ->assertSee('Project write-ups are on their way.');
    }

    public function test_case_study_shows_all_sections(): void
    {
        $this->demo();

        $this->get('/portfolio/demo-app')
            ->assertOk()
            ->assertSee('<title>LAYOUT: Demo App</title>', false)
            ->assertSee('<meta name="description" content="Demo summary.">', false)
            ->assertSee('src="'.Storage::disk('public')->url('portfolio/demo.png').'"', false)
            ->assertSee('alt="Demo screenshot"', false)
            ->assertSeeInOrder(['Demo problem.', 'Demo role.', 'Demo Approach', 'Demo approach body.', 'Filament'])
            ->assertSee('href="https://demo.example.test" target="_blank" rel="noopener noreferrer"', false)
            ->assertSee('href="https://github.com/example/demo" target="_blank" rel="noopener noreferrer"', false);
    }

    public function test_title_only_project_renders_without_empty_parts(): void
    {
        Project::factory()->published()->create(['title' => 'Bare Project', 'slug' => 'bare', 'summary' => null]);

        $this->get('/portfolio/bare')
            ->assertOk()
            ->assertSee('Bare Project</h1>', false)
            ->assertSee('<meta name="description" content="NO-DESCRIPTION">', false)
            ->assertDontSee('<img', false)
            ->assertDontSee('The problem')
            ->assertDontSee('Technologies')
            ->assertDontSee('View live site')
            ->assertDontSee('View source code');
    }

    public function test_drafts_unknown_and_malformed_slugs_are_404(): void
    {
        Project::factory()->create(['slug' => 'draft']);

        $this->get('/portfolio/draft')->assertNotFound();
        $this->get('/portfolio/nope')->assertNotFound();
        $this->get('/portfolio/demo.app')->assertNotFound();
        $this->get('/portfolio/DEMO-APP')->assertNotFound();
    }

    public function test_project_text_is_html_escaped(): void
    {
        Project::factory()->published()->create([
            'title' => 'Tom & Jerry <b>',
            'slug' => 'odd',
            'summary' => '<script>alert(1)</script>',
        ]);

        $this->get('/portfolio/odd')
            ->assertOk()
            ->assertSee('<title>LAYOUT: Tom &amp; Jerry &lt;b&gt;</title>', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->get('/portfolio')->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_contact_links_are_omitted_without_a_contact_route(): void
    {
        $this->demo();

        $this->get('/portfolio/demo-app')
            ->assertOk()
            ->assertSee('Need something similar?')
            ->assertDontSee('/contact', false);
    }

    public function test_contact_links_use_the_contact_route_when_it_exists(): void
    {
        Route::get('/contact', fn () => 'contact')->name('contact');
        $this->demo();

        $this->get('/portfolio/demo-app')
            ->assertSee('href="'.route('contact').'"', false);
    }
}
