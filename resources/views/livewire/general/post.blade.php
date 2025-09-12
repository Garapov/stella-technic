<div class="oglide__slide verflow-hidden bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
    <img class="w-full object-cover object-center rounded-t-lg" src="{{ Storage::disk(config('filesystems.default'))->url($post->image) }}"
        alt="blog">


    <div class="p-6">
        <h1 class="title-font text-lg font-medium text-blue-500 mb-3 whitespace-normal">{{$post->title}}</h1>
        <p class="leading-relaxed mb-3 dark:text-gray-200 text-gray-900 whitespace-normal">{{ \Illuminate\Support\Str::limit(strip_tags($post->short_content), 150) }}</p>
        <div class="flex items-center flex-wrap ">
            <a href="{{ route('client.posts.show', ['slug' => $post->slug]) }}" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" wire:navigate>Читать далее
                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5l7 7-7 7"></path>
                </svg>
            </a>
            <span
                class="text-gray-400 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm py-1">
                <svg class="w-4 h-4 mr-1" stroke="currentColor" stroke-width="2" fill="none"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>1.2K
            </span>
        </div>
    </div>
</div>
