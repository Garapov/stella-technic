<div>
    <div class="flex flex-col text-center w-full mb-4">
        <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-gray-200">{{ $form->name }}</h1>
        {{-- <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Whatever cardigan tote bag tumblr hexagon brooklyn
            asymmetrical gentrify.</p> --}}
    </div>
    <div class="lg:w-1/2 md:w-2/3 mx-auto">
        @if (session('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @else
            <form class="flex flex-wrap -m-2" wire:submit.prevent="save">
                
                @foreach ($fields as $field)
                    <label class="p-2 w-full">
                        <div for="name" class="leading-7 text-sm text-gray-600 dark:text-gray-200">{{ $field['label'] }}</div>
                        @php
                            $modelName = 'fields.'.$field['name'].'.value';
                        @endphp
                        @switch($field['type'])
                            @case('text')
                                <input type="text" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" wire:model="{{ $modelName }}">
                                @break
                        
                            @case('textarea')
                                <textarea class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out" wire:model="{{$modelName}}"></textarea>
                                @break
                        
                            @default
                                <input type="text" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" wire:model="">
                        @endswitch
                        
                        @error($modelName)
                            <div class="leading-7 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </label>
                @endforeach
                <button type="submit" class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">Button</button>
            </form>
        @endif
    </div>

</div>
