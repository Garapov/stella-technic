<article class="glide__slide p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700 flex flex-col justify-between">
    <div>
        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><a href="#">{{ $article->title }}</a></h2>
        <div class="mb-5 font-light text-gray-500 dark:text-gray-400">
            {!! $article->content !!}
        </div>
    </div>
    <div class="flex justify-between items-center">
        <span class="text-gray-500 text-sm">{{ $article->created_at->diffForHumans() }}</span>
        <a href="{{ route('client.articles.show', $article->slug) }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
            Читать далее
            <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
        </a>
    </div>
</article>