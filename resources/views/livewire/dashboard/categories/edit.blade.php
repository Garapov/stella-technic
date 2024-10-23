<div class="grid grid-cols-3 gap-4">
    <div>
        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden">
            <div class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 dark:text-white">
                Our products
                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">Browse a list of Flowbite products designed to help you work and play, stay organized, get answers, keep in touch, grow your business, and more.</p>
            </div>
            <form class="flex flex-col gap-4 p-5 bg-white dark:bg-gray-800" wire:submit.prevent="save">
                <div>
                    <x-input-label for="name" :value="__('Название категории')" />
                    <x-text-input class="block w-full mt-1" type="text" wire:model="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>
        
                <div>
                    <x-input-label for="file" :value="__('Изображение категории')" />
                    <label class="cursor-pointer mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                        {{-- {{ $category }} --}}
                        @if (!$file && !$category->image)
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                            <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        @else
                            @if (!$file && $category->image)
                                <img src="{{ asset('storage/' . $category->image) }}" alt="">
                            @endif
                            @if ($file)
                                <img src="{{ $file->temporaryUrl() }}" alt="">
                            @endif
                        @endif
                        <input class="hidden" type="file" wire:model="file">
                    </label>
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />
                </div>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
            </form>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg col-span-2">
        <div class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 dark:text-white">
            Our products
            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">Browse a list of Flowbite products designed to help you work and play, stay organized, get answers, keep in touch, grow your business, and more.</p>
        </div>
        <div class="p-5">
            @livewire('dashboard.categories.components.products', ['category_id' => $category->id], key($category->id))
        </div>
    </div>
</div>
  