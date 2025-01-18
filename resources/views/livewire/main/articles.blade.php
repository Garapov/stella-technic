@php
    $articles = \App\Models\Article::where('is_popular', true)->pluck('id');
@endphp
<section class="py-10 dark:bg-dark glide article_slider">
    <div class="container mx-auto">
        <div class="flex items-center justify-between mb-10">
            <p class="text-4xl text-gray-900 dark:text-white">{{ $title ?? 'Популярные статьи' }}</p>
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-2">
                    @if (count($articles) > 1)
                        <div class="flex items-center gap-2" data-glide-el="controls">
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-glide-dir="<">
                                <x-fas-arrow-left-long class="w-5 h-5" />
                                <span class="sr-only">Icon description</span>
                            </button>
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm p-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" data-glide-dir=">">
                                <x-fas-arrow-right-long class="w-5 h-5" />
                                <span class="sr-only">Icon description</span>
                            </button>
                        </div>
                    @endif  
                </div>
                <a href="#"
                    class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    Смотреть все
                    <x-fas-arrow-right class="w-4 h-4 ms-2 rtl:rotate-180" />
                </a>
            </div>
        </div>
        @livewire('articles.items', ['articles' => $articles])
    <div>
</section>