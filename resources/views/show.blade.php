@php
    $disk = \Illuminate\Support\Facades\Storage::disk(config('filament-portfolio.disk'));
    $hasContact = \Illuminate\Support\Facades\Route::has('contact');
@endphp
<x-dynamic-component :component="config('portfolio-frontend.layout')" :title="$project->title" :description="$project->summary ?: null">
    <article class="max-w-2xl mx-auto py-12 w-full">
        <p class="mb-4">
            <a href="{{ route('portfolio.index') }}" class="text-sm text-neutral-500 hover:text-neutral-400">&larr; All projects</a>
        </p>
        <h1 class="text-4xl sm:text-5xl font-bold mb-4">{{ $project->title }}</h1>
        @if ($project->summary)
            <p class="text-lg sm:text-xl mb-8">{{ $project->summary }}</p>
        @endif

        @foreach ($project->images ?? [] as $image)
            <img src="{{ $disk->url($image['path']) }}" alt="{{ $image['alt'] }}" loading="lazy" class="rounded-md shadow-sm mb-8 w-full">
        @endforeach

        @if ($project->problem)
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-3">The problem</h2>
                <p class="text-lg">{{ $project->problem }}</p>
            </section>
        @endif

        @if ($project->role)
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-3">My role</h2>
                <p class="text-lg">{{ $project->role }}</p>
            </section>
        @endif

        @foreach ($project->case_study ?? [] as $section)
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-3">{{ $section['heading'] }}</h2>
                <p class="text-lg">{{ $section['body'] }}</p>
            </section>
        @endforeach

        @if ($project->tech)
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-3">Technologies</h2>
                <x-portfolio-frontend::tech-list :items="$project->tech" />
            </section>
        @endif

        @if ($project->live_url || $project->source_url)
            <div class="flex flex-wrap gap-4">
                @if ($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="px-5 py-3 rounded-md font-semibold bg-neutral-900 text-white hover:bg-neutral-700 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-200 transition">View live site</a>
                @endif
                @if ($project->source_url)
                    <a href="{{ $project->source_url }}" target="_blank" rel="noopener noreferrer" class="px-5 py-3 rounded-md font-semibold border-2 border-neutral-900 hover:bg-neutral-100 dark:border-white dark:hover:bg-neutral-800 transition">View source code</a>
                @endif
            </div>
        @endif

        <p class="text-lg mt-12">
            Need something similar?
            @if ($hasContact)
                <a href="{{ route('contact') }}" class="underline font-semibold hover:text-neutral-600 dark:hover:text-neutral-400">Get in touch</a>.
            @endif
        </p>
    </article>
</x-dynamic-component>
