@php
    $post = App\Models\Post::where('slug', $slug)->first();
@endphp
<x-guest-layout>
    <section class="py-8 bg-white md:py-8 dark:bg-gray-900 antialiased">
        <div class="mx-auto container">
            <!-- Heading & Filters -->

            <div class="items-end justify-between space-y-4 sm:flex sm:space-y-0">
                <div>
                    @livewire('general.breadcrumbs')
                </div>
            </div>

        </div>
    </section>
    <main class="pb-20 container m-auto">

        <div class="md:mb-0 w-full mx-auto relative">
          <div class="px-4 lg:px-0 mb-4">
            <h1 class="text-4xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
              {{ $post->title }}
            </h1>
          </div>
          <img src="{{ Storage::disk(config('filesystems.default'))->url($post->image) }}" class="w-full object-cover lg:rounded" style="height: 28em;">
        </div>

        <div class="flex flex-col gap-4">
            <x-filament-fabricator::page-blocks :blocks="$post->content" />
        </div>
      </main>

      <x-floating-control-panel>
        <livewire:general.panel.clear-page-cache-button />

        @if ($slug)
            <livewire:general.panel.edit-resource-button link="{{ \App\Filament\Resources\PostResource::getUrl('edit', ['record' => $slug ]) }}" title="Редактировать новость" />
        @endif
    </x-floating-control-panel>
</x-guest-layout>
