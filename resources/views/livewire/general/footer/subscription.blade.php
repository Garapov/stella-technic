<div>
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
        <form wire:submit.prevent="subscribe" class="w-full max-w-md grow p-6 bg-white border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
            <div class="flex flex-col gap-1">
                <div data-element="fields" data-stacked="false" class="flex items-center w-full">
                    <div class="relative w-full mr-3 formkit-field grow">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <x-carbon-email class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        </div>
                        <input wire:model="email" class="formkit-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500  @error('email') border-red-600 @enderror" name="email_address" aria-label="Email Address" placeholder="Введите ваш e-mail" type="email">
                    </div>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg cursor-pointer hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 flex items-center gap-2">
                        Подписаться
                        <x-fas-spinner class="animate-spin w-3 h-3" wire:loading wire:target="subscribe" />
                    </button>
                </div>
                @error('email')
                    <span class="text-red-600 text-xs">{{ $message }}</span> 
                @enderror
            </div>
            <div class="flex items-center mt-4">
                <input wire:model="confirmation" id="default-checkbox" type="checkbox"  class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="default-checkbox" class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 @error('confirmation') text-red-600 @enderror">Я даю согласие на обработку персональных данных</label>
            </div>
        </form>
    @endif
</div>