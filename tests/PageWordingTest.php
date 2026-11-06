<?php

namespace MikePageDev\PortfolioFrontend\Tests;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;

class PageWordingTest extends TestCase
{
    private const DEFAULT_PROMPT = 'Interested in something similar?';

    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['view']->addLocation(__DIR__.'/Fixtures/views');
        $app['config']->set('portfolio-frontend.layout', 'test-layout');
    }

    private function contactRoute(): void
    {
        Route::get('/contact', fn () => 'contact')->name('contact');
    }

    public function test_defaults_are_neutral(): void
    {
        $this->get('/portfolio')
            ->assertOk()
            ->assertSee('<title>LAYOUT: Portfolio</title>', false)
            ->assertSee('Portfolio</h1>', false)
            ->assertSee('<meta name="description" content="NO-DESCRIPTION">', false)
            ->assertSee('No projects yet.')
            ->assertDontSee('my work')
            ->assertDontSee('I&#039;ve built', false);
    }

    public function test_custom_index_wording_is_used(): void
    {
        config([
            'portfolio-frontend.title' => 'Selected Work',
            'portfolio-frontend.description' => 'Things we made.',
            'portfolio-frontend.intro' => 'A few recent projects.',
            'portfolio-frontend.empty_message' => 'Nothing here yet.',
        ]);

        $this->get('/portfolio')
            ->assertSee('<title>LAYOUT: Selected Work</title>', false)
            ->assertSee('Selected Work</h1>', false)
            ->assertSee('<meta name="description" content="Things we made.">', false)
            ->assertSee('A few recent projects.')
            ->assertSee('Nothing here yet.');
    }

    public function test_empty_strings_are_treated_as_off(): void
    {
        config([
            'portfolio-frontend.title' => '',
            'portfolio-frontend.description' => '',
            'portfolio-frontend.intro' => '',
        ]);

        $this->get('/portfolio')
            ->assertSee('<title>LAYOUT: Portfolio</title>', false)
            ->assertSee('Portfolio</h1>', false)
            ->assertSee('<meta name="description" content="NO-DESCRIPTION">', false)
            ->assertDontSee('mb-10', false);
    }

    #[DataProvider('emptyMessages')]
    public function test_nothing_to_say_leaves_no_empty_paragraph(?string $message): void
    {
        config(['portfolio-frontend.empty_message' => $message]);

        $this->get('/portfolio')->assertOk()->assertDontSee('<p class="text-lg">', false);
    }

    /**
     * @return array<string, array{?string}>
     */
    public static function emptyMessages(): array
    {
        return ['empty string' => [''], 'null' => [null]];
    }

    public function test_index_prompt_shows_without_an_empty_message(): void
    {
        $this->contactRoute();
        config([
            'portfolio-frontend.empty_message' => '',
            'portfolio-frontend.index_contact_prompt' => 'In the meantime, [get in touch].',
        ]);

        $this->get('/portfolio')->assertSee('<p class="text-lg">In the meantime,', false);
    }

    public function test_index_prompt_shows_only_in_the_empty_state(): void
    {
        $this->contactRoute();
        config(['portfolio-frontend.index_contact_prompt' => 'In the meantime, [get in touch].']);

        $this->get('/portfolio')->assertSee('>get in touch</a>.', false);

        $this->project();

        $this->get('/portfolio')->assertDontSee('In the meantime,');
    }

    public function test_case_study_prompt_links_to_the_configured_route(): void
    {
        Route::get('/enquiries', fn () => 'enquiries')->name('enquiries');
        $this->project(['slug' => 'demo-app']);
        config([
            'portfolio-frontend.contact_route' => 'enquiries',
            'portfolio-frontend.case_study_contact_prompt' => 'Need something similar? [Get in touch].',
        ]);

        $this->get('/portfolio/demo-app')
            ->assertSee('Need something similar? <a href="'.route('enquiries').'"', false)
            ->assertSee('>Get in touch</a>.', false);
    }

    #[DataProvider('routesThatHideThePrompt')]
    public function test_prompt_is_hidden_without_a_usable_route(?string $route): void
    {
        $this->contactRoute();
        $this->project(['slug' => 'demo-app']);
        config(['portfolio-frontend.contact_route' => $route]);

        $this->get('/portfolio/demo-app')
            ->assertOk()
            ->assertDontSee(self::DEFAULT_PROMPT)
            ->assertDontSee('mt-12', false);
    }

    /**
     * @return array<string, array{?string}>
     */
    public static function routesThatHideThePrompt(): array
    {
        return ['null' => [null], 'empty string' => [''], 'undefined route' => ['enquiries']];
    }

    #[DataProvider('promptTexts')]
    public function test_prompt_brackets(string $prompt, string $expected, bool $linked): void
    {
        $this->contactRoute();
        $this->project(['slug' => 'demo-app']);
        config(['portfolio-frontend.case_study_contact_prompt' => $prompt]);

        $response = $this->get('/portfolio/demo-app')->assertSee($expected, false);

        $linked
            ? $response->assertSee('href="'.route('contact').'"', false)
            : $response->assertDontSee('href="'.route('contact').'"', false);
    }

    /**
     * @return array<string, array{string, string, bool}>
     */
    public static function promptTexts(): array
    {
        return [
            'no brackets' => ['Email us any time.', 'Email us any time.', false],
            'unclosed bracket' => ['Unclosed [bracket here', 'Unclosed [bracket here', false],
            'two pairs' => ['[One] and [two].', '>One</a> and [two].', true],
        ];
    }

    public function test_wording_is_html_escaped(): void
    {
        $this->contactRoute();
        $this->project(['slug' => 'demo-app']);
        config([
            'portfolio-frontend.intro' => 'Tom & <b>Jerry</b>',
            'portfolio-frontend.case_study_contact_prompt' => '<script>x</script> [<i>link</i>]',
        ]);

        $this->get('/portfolio')
            ->assertSee('Tom &amp; &lt;b&gt;Jerry&lt;/b&gt;', false)
            ->assertDontSee('<b>Jerry</b>', false);

        $this->get('/portfolio/demo-app')
            ->assertDontSee('<script>x</script>', false)
            ->assertSee('>&lt;i&gt;link&lt;/i&gt;</a>', false);
    }
}
