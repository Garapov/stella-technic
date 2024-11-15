@aware(['page'])
<div>
    @if (count($partners))
        <!-- ====== Brands Section Start -->
        <section class="py-10 dark:bg-dark">
            <div class="container mx-auto">
                <div class="-mx-4 flex flex-wrap">
                    <div class="w-full px-4">
                        <div class="flex flex-wrap items-center justify-center">
                            @foreach ($partners as $partner)
                                <a href="{{ $partner->link }}"
                                class="mx-4 flex w-[150px] items-center justify-center py-5 2xl:w-[180px]" wire:navigate>
                                    <img src="{{ asset($partner->image) }}" alt="image"
                                        class="h-10 w-full" />
                                    <img src="https://cdn.tailgrids.com/2.2/assets/images/brands/graygrids-white.svg" alt="image"
                                        class="hidden h-10 w-full dark:hidden" />
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
