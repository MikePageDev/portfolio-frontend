@props(['items' => []])

@if ($items)
    <ul class="flex flex-wrap gap-2 mb-4">
        @foreach ($items as $item)
            <li class="text-sm px-2 py-1 rounded-sm bg-neutral-100 dark:bg-neutral-800">{{ $item }}</li>
        @endforeach
    </ul>
@endif
