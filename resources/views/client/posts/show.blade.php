@php
    $post = App\Models\Post::where('slug', $slug)->with(['author', 'category'])->first();
@endphp
<x-guest-layout>
    <section class="py-8 bg-white md:py-8 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->
    
            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @livewire('general.breadcrumbs')
                    {{-- <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Текстовая страница</h2> --}}
                </div>
            </div>
            
        </div>
    </section>
    <main class="py-20 container m-auto">

        <div class="md:mb-0 w-full mx-auto relative">
          <div class="px-4 lg:px-0">
            <h1 class="text-4xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
              {{ $post->title }}
            </h1>
            <a class="py-2 text-green-700 inline-flex items-center justify-center mb-2" target="_blank">
              {{ $post->category->name }}
            </a>
          </div>
          <img src="{{ $post->banner_url }}" class="w-full object-cover lg:rounded" style="height: 28em;">
        </div>
  
        <div class="flex flex-col lg:flex-row lg:space-x-12">
  
            <div class="px-4 lg:px-0 mt-12 text-gray-700 dark:text-gray-200 text-lg leading-relaxed w-full lg:w-3/4">
                {!! $post->content !!}
            </div>
  
          <div class="w-full lg:w-1/4 m-auto mt-12 max-w-screen-sm">
            <div class="p-4 border-t border-b md:border md:rounded">
              <div class="flex py-2">
                <img src="{{ asset($post->author->photo) }}" class="h-10 w-10 rounded-full mr-2 object-cover">
                <div>
                  <p class="font-semibold text-gray-700 dark:text-gray-200 text-sm"> {{ $post->author->name }} </p>
                  <p class="font-semibold text-gray-600 dark:text-gray-200 text-xs"> {{ $post->author->email }} </p>
                </div>
              </div>
              <div class="text-gray-700 dark:text-gray-200">
                {!! $post->author->bio !!}
              </div>
            </div>
          </div>
  
        </div>
      </main>
</x-guest-layout>