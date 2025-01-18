@aware(['page'])
<section class="py-10 dark:bg-dark">
    <div class="container mx-auto">
        <div class="flex items-center justify-between mb-10">
            <p class="text-4xl text-gray-900 dark:text-white">{{ $title ?? 'Популярные статьи' }}</p>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-2">
                    @foreach ($articles as $key=>$article)
                        <span class="w-2.5 h-2.5 @if($key == 0)bg-gray-600 @else bg-gray-400 @endif rounded-full"></span>
                    @endforeach
                </div>
                <a href="#"
                    class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    Смотреть все
                    <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                </a>
            </div>
        </div>
        @livewire('articles.items', ['articles' => $articles])
    </div>
</section>
