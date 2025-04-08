<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
    @csrf

    <ul class="flex flex-wrap gap-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">
        <li class="grow">
            <div class="block px-4 py-3 rounded-lg @if ($user_type == 'natural') text-white bg-blue-500 active @else hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer @endif" wire:click="chageUserType('natural')">Физ. Лицо</div>
        </li>
        <li class="grow">
            <div class="block px-4 py-3 rounded-lg @if ($user_type == 'legal') text-white bg-blue-500 active @else hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white cursor-pointer @endif" wire:click="chageUserType('legal')">Юр. Лицо</div>
        </li>
    </ul>

    <!-- Name -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
    </div>
    @if ($user_type == 'legal')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="company_name" :value="__('Company name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('name')" required autofocus autocomplete="company_name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" :value="__('Phone')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="username" x-mask="+7 (999) 999-99-99" placeholder="+7 (999) 999-99-99" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="inn" :value="__('INN')" />
                <x-text-input id="inn" class="block mt-1 w-full" type="text" name="inn" :value="old('inn')" required autofocus autocomplete="inn" />
                <x-input-error :messages="$errors->get('inn')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="kpp" :value="__('KPP')" />
                <x-text-input id="kpp" class="block mt-1 w-full" type="text" name="kpp" :value="old('kpp')" required autofocus autocomplete="kpp" />
                <x-input-error :messages="$errors->get('kpp')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="bik" :value="__('BIK')" />
                <x-text-input id="bik" class="block mt-1 w-full" type="text" name="bik" :value="old('bik')" required autofocus autocomplete="bik" />
                <x-input-error :messages="$errors->get('bik')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="correspondent_account" :value="__('correspondent account')" />
                <x-text-input id="correspondent_account" class="block mt-1 w-full" type="text" name="correspondent_account" :value="old('correspondent_account')" required autofocus autocomplete="correspondent_account" />
                <x-input-error :messages="$errors->get('correspondent_account')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <x-input-label for="bank_account" :value="__('bank account')" />
                <x-text-input id="bank_account" class="block mt-1 w-full" type="text" name="bank_account" :value="old('bank_account')" required autofocus autocomplete="bank_account" />
                <x-input-error :messages="$errors->get('bank_account')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="yur_address" :value="__('yur address')" />
                <x-text-input id="yur_address" class="block mt-1 w-full" type="text" name="yur_address" :value="old('yur_address')" required autofocus autocomplete="yur_address" />
                <x-input-error :messages="$errors->get('yur_address')" class="mt-2" />
            </div>
        </div>
    @else
    <div class="grid grid-cols-2 gap-4">
        <!-- Phone -->
        <div class="col-span-2">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="username" x-mask="+7 (999) 999-99-99" placeholder="+7 (999) 999-99-99" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>
    </div>
    @endif
    @push('scripts')
        <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
    @endpush
    
    <div
        wire:ignore
        class="smart-captcha w-full"
        data-sitekey="{{ config('services.recaptcha.client_key') }}"
    ></div>
    @error('smart-token')
        <span class="text-sm text-red-600 space-y-1">Подтвердите что вы человек</span>
    @enderror

    <div class="flex items-center justify-end mt-4">
        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
            {{ __('Already registered?') }}
        </a>

        <x-primary-button class="ms-4">
            {{ __('Register') }}
        </x-primary-button>
    </div>
</form>