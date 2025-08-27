<div>
    @if (count($brands))
        <!-- ====== Brands Section Start -->
        <section class="py-5 px-4 dark:bg-dark">
            <div class="xl:px-[100px] px-[20px]">
                <div class="-mx-4 flex flex-wrap">
                    <div class="w-full px-4">
                        <div class="grid lg:grid-cols-6 grid-cols-2 gap-2">
                            @foreach ($brands as $brand)
                                <a href="{{ route('client.brands.show', ['slug' => $brand->slug]) }}"
                                class="flex items-center justify-center p-4 rounded bg-slate-100 filter grayscale hover:filter-none opacity-40 hover:opacity-100 hover:bg-white" wire:navigate>
                                    <img src="{{ Storage::disk(config('filesystems.default'))->url($brand->image) }}" alt="image"
                                        class="w-full" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ====== Brands Section End -->
    @endif
</div>