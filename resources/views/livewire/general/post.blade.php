<div class="glide__slide bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700 flex flex-col justify-between h-auto">
    {{-- <div class="p-4 pb-0">
        <img class="w-full object-cover rounded-lg object-center" src="{{ Storage::disk(config('filesystems.default'))->url($post->image) }}"
        alt="blog">
    </div> --}}


    <div class="p-4 items-start flex flex-col justify-between">
        <h3 class="title-font text-md font-medium text-blue-500 mb-1 whitespace-normal">{{$post->title}}</h3>
        @if ($post->created_at)
            <span class="bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-300 mb-3">{{ $post->created_at->diffForHumans() }}</span>
        @endif
        <p class="leading-relaxed mb-3 dark:text-gray-200 text-gray-900 whitespace-normal">{{ \Illuminate\Support\Str::limit(strip_tags($post->short_content), 80) }} <a href="{{ route('client.posts.show', ['slug' => $post->slug]) }}" class="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0" wire:navigate>Читать далее
                <svg class="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5l7 7-7 7"></path>
                </svg>
            </a></p>
    </div>
</div>
