
<div x-data="{
    activeTabIndex: 0
}" x-init="isLoaded = true" class="flex flex-col mb-10">
    @if (count($categories) > 0)
        <div class="flex items-center justify-between" x-cloak>
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                @php
                    $counter = 0;
                @endphp
                @foreach($categories as $key => $category)
                    @if (count($category->categories) < 1)
                        @continue
                    @endif
                    <li class="me-2">
                        <span class="inline-block p-4 rounded-t-lg border border-blue-500" :class="activeTabIndex == {{$counter}} ? 'text-white bg-blue-500 active ' : 'text-gray-900 bg-slate-50 cursor-pointer'" @click="activeTabIndex = {{$counter}}">{{ $category->title }}</span>
                    </li>

                    @php
                        $counter++;
                    @endphp
                @endforeach
            </ul>
            @php
                $counter = 0;
            @endphp
            @foreach($categories as $key => $category)
                @if (count($category->categories) < 1)
                    @continue
                @endif
                <a href="{{ route('client.catalog', $category->urlChain()) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800" x-show="activeTabIndex == {{$counter}}" x-cloak>Перейти в {{ $category->title }}</a>
                @php
                    $counter++;
                @endphp
            @endforeach
        </div>

        @php
            $counter = 0;
        @endphp
    
        @foreach($categories as $key => $category)
            @if (count($category->categories) < 1)
                @continue
            @endif
            <div class="grid xl:grid-cols-6 lg:grid-cols-4 md:grid-cols-3 grid-cols-2  gap-4 p-4 border border-slate-200 rounded-b-lg shadow-sm bg-slate-50 rounded-b-lg mt-[-1px]" x-show="activeTabIndex == {{ $counter }}" x-cloak>
                @foreach($category->categories as $key => $subcategory)
                    <a href="{{ route('client.catalog', $subcategory->urlChain()) }}" class="p-2.5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-slate-200 shadow-sm hover:shadow-lg shadow-slate-50/50 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 flex items-center justify-between gap-2">
                        <span>{{ $subcategory->title }}</span>
                        @if (!empty($subcategory->image))
                            <img class="min-w-10 max-w-10 min-h-10 max-h-10 object-cover" alt="{{ $subcategory->title }}" loading="lazy" class="" src="{{ Storage::disk(config('filesystems.default'))->url($subcategory->image) }}">
                        @endif
                    </a>
                @endforeach
            </div>
            @php
                $counter++;
            @endphp
        @endforeach
    @endif
    
</div>
