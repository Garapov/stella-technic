<div class="py-10 xl:px-[100px] px-[20px]">
    <div class="flex items-center justify-between mb-10">
        <div class="bg-gray-300 rounded-lg lg:rounded-xl animate-pulse h-10 w-1/5"></div>
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="bg-gray-300 rounded-lg lg:rounded-xl animate-pulse h-10 w-10"></div>
                <div class="bg-gray-300 rounded-lg lg:rounded-xl animate-pulse h-10 w-10"></div>
            </div>
        </div>
    </div>
    <div class="grid justify-between gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 text-gray-400">
        @foreach (range(1,3) as $key=>$i)
            <div role="status" class="p-4 border border-gray-200 rounded-lg shadow-sm animate-pulse md:p-6 dark:border-gray-700">
                <div class="h-3 bg-gray-200 rounded-full dark:bg-gray-700 w-full mb-4"></div>
                <div class="h-3 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
                <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                <div class="flex items-center justify-between mt-4">
                    <div class="h-2 w-1/4 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                    <div class="h-2 w-1/4 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                </div>
                <span class="sr-only">Loading...</span>
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