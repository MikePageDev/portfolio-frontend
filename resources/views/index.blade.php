@php
    $disk = \Illuminate\Support\Facades\Storage::disk(config('portfolio-frontend.disk'));
    $title = config('portfolio-frontend.title') ?: 'Portfolio';
    $intro = config('portfolio-frontend.intro');
    $emptyMessage = config('portfolio-frontend.empty_message');
    $contactPrompt = config('portfolio-frontend.index_contact_prompt');
@endphp
<x-dynamic-component :component="config('portfolio-frontend.layout')" :title="$title" :description="config('portfolio-frontend.description') ?: null">
    <section class="max-w-2xl mx-auto py-12 w-full">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4">{{ $title }}</h1>
        @if ($intro)
            <p class="text-lg mb-10">{{ $intro }}</p>
        @endif

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
            @if (filled($emptyMessage))
                <p class="text-lg">
                    {{ $emptyMessage }}
                    <x-portfolio-frontend::contact-prompt :text="$contactPrompt" link-class="underline font-semibold" />
                </p>
            @else
                <x-portfolio-frontend::contact-prompt :text="$contactPrompt" link-class="underline font-semibold" wrapper-class="text-lg" />
            @endif
        @endforelse
    </section>
</x-dynamic-component>
