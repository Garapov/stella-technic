@unless ($breadcrumbs->isEmpty())
    <nav class="flex" aria-label="Breadcrumb" x-data="{
        windowWidth: window.innerWidth,
        init() {
            window.addEventListener('resize', () => {
                this.windowWidth = window.innerWidth;
                console.log(this.windowWidth);
            })
        }
    }">
        <template x-if="windowWidth > 700" >
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                @foreach ($breadcrumbs as $breadcrumb)

                    @if (!is_null($breadcrumb->url) && !$loop->last)
                        <li class="inline-flex items-center">
                            <a href="{{ $breadcrumb->url }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white" wire:navigate>
                                @if ($breadcrumb->url == route("client.index"))
                                    <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                                    </svg>
                                @else
                                    <svg class="rtl:rotate-180 w-3 h-3 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                                    </svg>
                                @endif
                                {{ $breadcrumb->title }}
                            </a>
                        </li>
                    @else
                        <li class="inline-flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                                {{ $breadcrumb->title }}
                            </span>
                        </li>
                    @endif

                @endforeach
            </ol>
        </template>
        <template x-if="windowWidth <= 700">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white" href="{{ route("client.index") }}" wire::navigate>
                        <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                        </svg>
                    </a>
                </li>

                @if (count($breadcrumbs) > 2)
                    <li x-data="{
                        isDropdownOpened: false
                    }">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <div class="relative" @click.outside="isDropdownOpened = false">
                                <button id="dropdownProject" data-dropdown-toggle="dropdown-project" class="inline-flex items-center px-3 py-2 text-sm font-normal text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-900 dark:hover:bg-gray-800 dark:text-white dark:focus:ring-gray-700" @click="isDropdownOpened = true">
                                    <svg class="mr-1.5 w-3 h-3" aria-hidden="true" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path d="M80 104c13.3 0 24-10.7 24-24s-10.7-24-24-24S56 66.7 56 80s10.7 24 24 24zm80-24c0 32.8-19.7 61-48 73.3v87.8c18.8-10.9 40.7-17.1 64-17.1h96c35.3 0 64-28.7 64-64v-6.7C307.7 141 288 112.8 288 80c0-44.2 35.8-80 80-80s80 35.8 80 80c0 32.8-19.7 61-48 73.3V160c0 70.7-57.3 128-128 128H176c-35.3 0-64 28.7-64 64v6.7c28.3 12.3 48 40.5 48 73.3c0 44.2-35.8 80-80 80s-80-35.8-80-80c0-32.8 19.7-61 48-73.3V352 153.3C19.7 141 0 112.8 0 80C0 35.8 35.8 0 80 0s80 35.8 80 80zm232 0c0-13.3-10.7-24-24-24s-24 10.7-24 24s10.7 24 24 24s24-10.7 24-24zM80 456c13.3 0 24-10.7 24-24s-10.7-24-24-24s-24 10.7-24 24s10.7 24 24 24z"/>
                                    </svg>
                                    {{ $breadcrumbs[count($breadcrumbs) - 2]->title }}
                                    <svg class="w-5 h-5 ml-1" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd">
                                        </path>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700" x-show="isDropdownOpened">
                                    <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefault">
                                        @foreach ($breadcrumbs as $breadcrumb)
                                            @if ($loop->first || $loop->last)
                                                @continue
                                            @endif
                                            <li>
                                                <a href="{{ $breadcrumb->url }}" wire::navigate class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ $breadcrumb->title }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div> 
                            </div>
                        </div>
                    </li>
                @endif
                <li class="inline-flex items-center">
                    {{-- <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg> --}}
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">
                        {{ $breadcrumbs[count($breadcrumbs) - 1]->title }}
                    </span>
                </li>
            </ol>
        </template>
    </nav>
@endunless