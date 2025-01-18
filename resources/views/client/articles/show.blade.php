@php
    $article = App\Models\Article::where('slug', $slug)->first();
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
              {{ $article->title }}
            </h1>
          </div>
          <img src="{{ asset('storage/' . $article->image) }}" class="w-full object-cover lg:rounded" style="height: 28em;">
        </div>
  
        <div class="flex flex-col lg:flex-row lg:space-x-12">
  
            <div class="px-4 lg:px-0 mt-12 text-gray-700 dark:text-gray-200 text-lg leading-relaxed w-full lg:w-3/4">
                {!! $article->content !!}
            </div>
  
        </div>
      </main>
</x-guest-layout>