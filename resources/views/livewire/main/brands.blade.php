<div>
    @if (count($partners))
        <!-- ====== Brands Section Start -->
        <section class="py-10 dark:bg-dark">
            <div class="container mx-auto">
                <div class="-mx-4 flex flex-wrap">
                    <div class="w-full px-4">
                        <div class="flex flex-wrap items-stretch justify-center gap-2">
                            @foreach ($partners as $partner)
                                <a href="{{ $partner->link }}"
                                class="flex w-[150px] items-center justify-center p-4 2xl:w-[180px] rounded bg-white" wire:navigate>
                                    <img src="{{ asset('storage/' . $partner->image) }}" alt="image"
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