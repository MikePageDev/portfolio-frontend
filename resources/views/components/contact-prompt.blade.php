@props(['text' => null, 'linkClass' => '', 'wrapperClass' => null])
@php
    // The text is plain and gets escaped first; the first [text] then becomes the link.
    $routeName = config('portfolio-frontend.contact_route');
    $visible = filled($text) && filled($routeName) && \Illuminate\Support\Facades\Route::has($routeName);
    $html = $visible ? preg_replace_callback('/\[([^\]]+)\]/', fn (array $match): string => '<a href="' . e(route($routeName)) . '" class="' . e($linkClass) . '">' . $match[1] . '</a>', e($text), 1) : null;
@endphp
@if ($visible)
    @if ($wrapperClass)
        <p class="{{ $wrapperClass }}">{!! $html !!}</p>
    @else
        {!! $html !!}
    @endif
@endif
