<x-auth-layout>
    <section class="p-4 bg-white dark:bg-gray-900 antialiased flex items-center">
        <div class="mx-auto w-full max-w-md">
            <div class="mb-4 text-sm text-gray-600">
                {{ __('Забыли пароль? Не проблема. Просто сообщите нам свой адрес электронной почты, и мы вышлем вам ссылку для сброса пароля, по которой вы сможете выбрать новый.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        Восстановить пароль
                    </x-primary-button>
                </div>
            </form>
        </div>
    </section>
</x-guest-layout>
