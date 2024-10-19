<form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
    @csrf

    <ul class="flex flex-wrap gap-4 text-sm font-medium text-center text-gray-500 dark:text-gray-400">
        <li class="grow">
            <div class="block px-4 py-3 rounded-lg @if ($user_type == 'natural') text-white bg-blue-600 active @else hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white @endif" wire:click="chageUserType('natural')">Физ. Лицо</div>
        </li>
        <li class="grow">
            <div class="block px-4 py-3 rounded-lg @if ($user_type == 'legal') text-white bg-blue-600 active @else hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-white @endif" wire:click="chageUserType('legal')">Юр. Лицо</div>
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
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>
    @endif

    <div class="flex items-center justify-end mt-4">
        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
            {{ __('Already registered?') }}
        </a>

        <x-primary-button class="ms-4">
            {{ __('Register') }}
        </x-primary-button>
    </div>
</form>