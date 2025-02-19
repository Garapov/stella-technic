<div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $vacancy->title }}</h5>
    <p class="font-normal text-gray-700 dark:text-gray-400">{!! $vacancy->description !!}</p>
    @if (!empty($vacancy->badges))
        <div class="flex flex-wrap gap-2 mt-4">
            @foreach ($vacancy->badges as $badge)
                <span class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-full dark:bg-purple-900 dark:text-purple-300">{{ $badge }}</span>
            @endforeach
        </div>
    @endif
</div>