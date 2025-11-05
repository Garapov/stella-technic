<div class="xl:px-[100px] px-[20px] py-4">
    
    @if ($this->category)
        @if ($this->category->seo)
            @forelse($this->category->seo as $seo_tag)
                @foreach($seo_tag['data'] as $key => $tag)

                    @if ($key == 'image')
                        @seo(['image' => Storage::disk(config('filesystems.default'))->url($tag)])
                    @else
                        @seo([$key => $tag])
                    @endif
                @endforeach

            @empty
                @seo(['title' => $this->category->title])
                @seo(['description' => $this->category->description])
                @seo(['image' => Storage::disk(config('filesystems.default'))->url($this->category->image)])
            @endforelse
        @else
            @seo(['title' => $this->category->title])
            @seo(['description' => $this->category->description])
            @seo(['image' => Storage::disk(config('filesystems.default'))->url($this->category->image)])
        @endif
        <div class="mb-5">
            {{ Breadcrumbs::render('category', $this->category) }}
        </div>

    @endif
    <div class="grid grid-cols-9 gap-4" x-data="{
        isFilterOpened: false
    }">
        <div class="col-span-2" wire:loading.class="opacity-25 pointer-events-none">
            <livewire:catalog.items-lazy-filter :category="$this->category"  />
        </div>
        
        <div class="flex flex-col gap-4 md:col-span-7 col-span-full">
            <livewire:catalog.items-lazy-list :category="$this->category"  />
        </div>
    </div>
</div>
