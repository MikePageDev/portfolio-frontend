@php
    $disk = \Illuminate\Support\Facades\Storage::disk(config('filament-portfolio.disk'));
    $hasContact = \Illuminate\Support\Facades\Route::has('contact');
@endphp
<x-dynamic-component :component="config('portfolio-frontend.layout')" title="Portfolio" description="Selected projects and case studies: Laravel applications, admin panels and backend work.">
    <section class="max-w-2xl mx-auto py-12 w-full">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4">Portfolio</h1>
        <p class="text-lg mb-10">A selection of things I've built, with the problem each one solved and the part I played.</p>

        @forelse ($projects as $project)
            <article class="py-8 border-t border-neutral-200 dark:border-neutral-700">
                @if ($project->images)
                    <img src="{{ $disk->url($project->images[0]['path']) }}" alt="{{ $project->images[0]['alt'] }}" loading="lazy" class="rounded-md shadow-sm mb-4 w-full">
                @endif
                <h2 class="text-2xl font-bold mb-2">
                    <a href="{{ route('portfolio.show', $project->slug) }}" class="hover:underline">{{ $project->title }}</a>
                </h2>
                @if ($project->summary)
                    <p class="text-lg mb-3">{{ $project->summary }}</p>
                @endif
                <x-portfolio-frontend::tech-list :items="$project->tech ?? []" />
                <a href="{{ route('portfolio.show', $project->slug) }}" class="font-semibold underline hover:text-neutral-600 dark:hover:text-neutral-400">Read the case study<span class="sr-only">: {{ $project->title }}</span></a>
            </article>
        @empty
            <p class="text-lg">
                Project write-ups are on their way.
                @if ($hasContact)
                    In the meantime, <a href="{{ route('contact') }}" class="underline font-semibold">get in touch</a> to ask about my work.
                @endif
            </p>
        @endforelse
    </section>
</x-dynamic-component>
