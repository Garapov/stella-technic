<div class="pt-8" x-data="{
    activeTab: 0,
    downloadFile(key) {
        $wire.downloadFile(key).then(result => {
            console.log(result);
        })
    }
}">

    <ul
        class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-100 dark:border-gray-700 dark:text-gray-400">
        <li class="me-2" id="params">
            <span aria-current="page" class="inline-block p-4 rounded-t-lg"
                :class="activeTab == 0 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'"
                aria-current="page" @click="activeTab = 0">Технические характеристики</span>
        </li>
        @if ($variation->description)
            <li class="me-2">
                <span aria-current="page" class="inline-block p-4 rounded-t-lg"
                    :class="activeTab == 1 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'"
                    aria-current="page" @click="activeTab = 1">Подробное описание</span>
            </li>
        @endif
        @if ($this->files ?? !empty($this->files))
            <li class="me-2">
                <span aria-current="page" class="inline-block p-4 rounded-t-lg"
                    :class="activeTab == 2 ? 'active bg-blue-600 text-white' : 'text-gray-600 bg-white shadow cursor-pointer'"
                    aria-current="page" @click="activeTab = 2">Файлы</span>
            </li>
        @endif
    </ul>
    <div class="text-medium rounded-b-lg bg-white shadow w-full p-4" x-show="activeTab == 0">
        <div class="grid grid-cols-1 gap-x-4 md:grid-cols-2 text-slate-700">
            @foreach($variation->paramItems->merge($variation->parametrs)->where('is_hidden', false)->sortBy('productParam.sort')->split(2) as $paramItemGroup)

                <dl class="flex flex-col gap-4">
                    @foreach ($paramItemGroup as $paramItem)
                        @if ($paramItem->productParam->is_hidden)
                            @continue
                        @endif
                        <li class="flex items-center justify-between text-sm gap-2 px-3 py-2">
                            <strong class="font-medium">{{ $paramItem->productParam->name }}</strong>
                            <span class="grow border-b border-gray-200 border-dotted border-b-2"></span>
                            <span class="font-bold">{{ $paramItem->title }}</span>
                        </li>
                    @endforeach
                </dl>
            @endforeach
        </div>
    </div>
    @if ($variation->description)
        <div class="text-medium text-gray-900 bg-white shadow rounded-b-lgw-full py-8 px-10 content_block flex flex-col gap-4"
            x-show="activeTab == 1">
            <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Подробное описание</h3> -->
            {!! str($variation->description)->sanitizeHtml() !!}
        </div>
    @endif
    @if ($this->files && !empty($this->files))
        <div class="text-medium text-gray-500 bg-white shadow w-full" x-show="activeTab == 2">
            <!-- <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Файлы</h3> -->
            <ul role="list" class="divide-y divide-gray-100 rounded-b-lg">
                @foreach($this->files as $key => $file)
                    <li class="flex items-center justify-between py-4 pr-5 pl-4 text-sm/6">
                        <div class="flex w-0 flex-1 items-center">
                            <x-fas-file-import class="size-5 shrink-0 text-gray-400" />
                            <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                <span class="truncate font-bold">{{ $file['name'] }}</span>
                                <span
                                    class="truncate font-medium">{{ File::basename(Storage::disk(config('filesystems.default'))->url($file['file'])) }}</span>
                                <span
                                    class="shrink-0 text-gray-400">{{ $variation->formatBytes(Storage::disk(config('filesystems.default'))->size($file['file'])) }}</span>
                            </div>
                        </div>
                        <div class="ml-4 shrink-0">
                            {{-- <a href="{{ Storage::disk(config('filesystems.default'))->url($file['file']) }}"
                                class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer"
                                download="{{ File::basename(Storage::disk(config('filesystems.default'))->url($file['file'])) }}">Скачать</a>
                            --}}
                            <div class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer"
                                @click="downloadFile({{ $key }})">Скачать</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif



</div>