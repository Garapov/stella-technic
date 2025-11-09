@if (count($this->deliveries) > 0)
                    
    <div class="border border-b-0 border-gray-200 bg-slate-50 rounded-lg flex flex-col mt-4 overflow-hidden" x-data="{
        openedTab: 0,
        openTab(index) {
            this.openedTab = index;
        }
    }">
        @foreach($this->deliveries as $key => $delivery)
            <h2>
                <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 focus:ring-4 focus:ring-gray-200 hover:bg-gray-100 gap-3" @click="openTab({{ $key }})" :class="{'border border-x-0 border-t-0 border-gray-200': openedTab != {{ $key }}}">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 me-2 shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg> {{ $delivery->name }}
                    </span>
                    <svg class="w-3 h-3 rotate-180 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                    </svg>
                </button>
            </h2>
            <div :class="{'hidden': openedTab != {{ $key }}}">
                <div class="p-5 border border-x-0 border-gray-200" :class="{'bg-white': openedTab == {{ $key }}}">
                    @if ($delivery->type == 'map')

                        @if($delivery->points)
                            <div class="text-sm text-slate-600">
                                {{ explode("|", $delivery->points)[0] }}
                            </div>
                        @endif
                        
                    @endif
                    <div class="text-xs text-gray-500 font-semibold">
                        {!! $delivery->text !!}
                        @if($delivery->images)
                            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                                @foreach ($delivery->images as $image)
                                    <div class="rounded-lg overflow-hidden">
                                        <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif