<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use App\Models\PortfolioProject;
use Illuminate\Support\Facades\Storage;

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

    private function demo(): PortfolioProject
    {
        return $this->project([
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
        $this->project(['title' => 'Earlier Project', 'slug' => 'earlier', 'sort_order' => 0]);
        $this->project(['title' => 'Draft Project', 'slug' => 'draft'], published: false);

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
            ->assertSee('No projects yet.');
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
        $this->project(['title' => 'Bare Project', 'slug' => 'bare', 'summary' => null]);

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
        $this->project(['slug' => 'draft'], published: false);

        $this->get('/portfolio/draft')->assertNotFound();
        $this->get('/portfolio/nope')->assertNotFound();
        $this->get('/portfolio/demo.app')->assertNotFound();
        $this->get('/portfolio/DEMO-APP')->assertNotFound();
    }

    public function test_project_text_is_html_escaped(): void
    {
        $this->project([
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

    public function test_screenshots_use_the_configured_disk(): void
    {
        Storage::fake('screenshots');
        config(['portfolio-frontend.disk' => 'screenshots']);
        $this->project(['slug' => 'pictured', 'images' => [['path' => 'a.png', 'alt' => 'A']]]);

        $this->get('/portfolio/pictured')
            ->assertOk()
            ->assertSee('src="'.Storage::disk('screenshots')->url('a.png').'"', false);
    }
}
