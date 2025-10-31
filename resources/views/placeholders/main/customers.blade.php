<div class="bg-slate-50 py-10 xl:px-[100px] px-[20px]">
    <div class="flex items-center justify-between mb-10">
        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-1/5"></div>
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-10"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-10"></div>
            </div>
        </div>
    </div>
    <div class="grid justify-between gap-4 grid-cols-1 md:grid-cols-3 lg:grid-cols-6 text-gray-400">
        @foreach (range(1,6) as $key=>$i)
            <div class="rounded  p-4 justify-center bg-slate-100 rounded-sm lg:rounded-xl animate-pulse @if ($key < 1) flex md:flex @endif @if ($key > 0 && $key < 3) hidden md:flex @endif @if ($key >= 3) hidden lg:flex @endif  ">
                <div class="aspect-1/1">
                    <svg class="w-full text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                    </svg>
                </div>
            </div>
        @endforeach
        {{-- <div class="glide">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides items-stretch">
                
                @foreach ($brands as $brand)
                    <li class="glide__slide h-auto">
                        <div class="flex justify-center items-center h-full p-4 rounded bg-white">
                            <img src="{{ Storage::disk(config('filesystems.default'))->url($brand->image) }}" alt="">    
                        </div>
                    </li>
                
                @endforeach
                </ul>
            </div>
        </div> --}}
    
    </div>
</div>