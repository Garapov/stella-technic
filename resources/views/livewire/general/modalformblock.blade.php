<!-- Modal content -->
<div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700" @click.stop>
    <!-- Modal header -->
    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{ $form->name }}
        </h3>

        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal" @click="$store.application.forms.{{ $form_name }} = false">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">Close modal</span>
        </button>
    </div>
    <!-- Modal body -->
    <div class="p-4 md:p-5 space-y-4">
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
            <form class="flex flex-col gap-4" wire:submit.prevent="save">
                @foreach ($fields as $field)
                    <label class="w-full">
                        @php
                            $modelName = 'fields.'.$field['name'].'.value';
                        @endphp
                        <div class="leading-7 text-sm @error($modelName) text-red-500 @else text-gray-600 dark:text-gray-200 @enderror">{{ $field['label'] }}</div>
                        @switch($field['type'])
                            @case('text')
                                <input type="text" class="w-full bg-white rounded focus:bg-white focus:ring-2 text-base outline-none py-1 px-3 leading-8 transition-colors duration-200 ease-in-out @error($modelName) bg-red-50 border border-red-500 text-red-900 placeholder-red-700 @else border border-gray-300 focus:border-indigo-500 focus:ring-indigo-200 text-gray-700 @enderror" wire:model="{{ $modelName }}" @if($field['mask_enabled']) x-mask="{{ $field['mask'] }}" @endif wire:loading.attr="disabled" wire:target="save">
                                @break
                            @case('email')
                                <input type="email" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" wire:model="{{ $modelName }}" wire:loading.attr="disabled" wire:target="save">
                                @break

                            @case('textarea')
                                <textarea class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out" wire:model="{{$modelName}}" wire:loading.attr="disabled" wire:target="save"></textarea>
                                @break

                            @case('select')
                                @php
                                    $options = explode(',', $field['options']);
                                @endphp

                                <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:model="{{$modelName}}" wire:loading.attr="disabled" wire:target="save">
                                    <option value="Ничего не выбрано">Ничего не выбрано</option>
                                    @foreach ($options as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>

                                @break

                            @default
                                <input type="text" class="w-full bg-white rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" wire:model="{{$modelName}}" wire:loading.attr="disabled" wire:target="save">
                        @endswitch
                    </label>
                @endforeach

                @if ($captcha)
                    <div  x-data="{
                        init() {
                            this.smartCaptchaInit();
                        },
                        smartCaptchaInit() {
                            if (!window.smartCaptcha) return;

                            window.smartCaptcha.render($refs.smartCaptcha, {
                                sitekey: '{{ config('services.recaptcha.client_key') }}',
                                hl: 'ru',
                                callback: (token) => {
                                    $wire.set('captcha_token', token);
                                }
                            });
                        }
                    }" class="w-full">
                        <div
                            x-ref="smartCaptcha"
                            wire:ignore
                        >
                        </div>
                        @error('captcha_token')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div class="flex items-center">
                    <input checked id="checked-checkbox" type="checkbox" class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" wire:model="confirmation"
                    >
                    <label for="checked-checkbox" class="ms-2 text-sm font-medium @error('confirmation') text-red-500 @else text-gray-900 dark:text-gray-300  @enderror">Я согласен на обработку моих
                        @if (setting('politics'))
                            <a href="{{ \Z3d0X\FilamentFabricator\Facades\FilamentFabricator::getPageUrlFromId(setting('politics')) }}" class="text-blue-600" wire:navigate>персональных данных</a>
                        @else
                            персональных данных
                        @endif
                    </label>
                </div>

                <button type="submit" class="flex items-center gap-4 mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg" wire:loading.attr="disabled" wire:target="save">
                    <svg aria-hidden="true" role="status" class="inline w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg" wire:loading wire:target="save">
                        <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"/>
                        <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"/>
                    </svg>
                    {{ $form->button_text }}
                </button>
            </form>
        @endif
    </div>
    <!-- Modal footer -->
    {{-- <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
        <button data-modal-hide="default-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>
        <button data-modal-hide="default-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
    </div> --}}
</div>
