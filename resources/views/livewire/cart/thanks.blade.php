<section class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto container px-4 2xl:px-0 flex flex-col items-center gap-4">
        <div class="text-green-600">
            <x-fas-circle-check class="w-24 h-24" />
        </div>
        <div class="text-5xl text-gray-800 dark:text-gray-300">Спасибо за заказ!</div>
        <div class="text-2xl font-bold text-blue-500">Ваш заказ №2410/55 от 2024-10-16 22:13:12 успешно создан.</div>
        <div class="text-regular text-center text-gray-800 dark:text-gray-300">
            В ближайшее время менеджер свяжется с Вами для подтверждения заказа!<br />
            Спасибо, что выбрали нашу компанию.
        </div>

        <div class="text-1xl font-bold text-red-500">Обратите внимание!</div>

        <div class="text-regular text-center text-gray-800 dark:text-gray-300">
            Заказ считается подтвержденным только после его согласования с нашим оператором.<br />
            Часы приема заказов пн-пт с 9:30 до 18:00, сб-вс - выходные.
        </div>

        <a href="{{ route('client.index') }}" class="px-5 py-3 text-base font-medium text-center inline-flex items-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" wire:navigate>
            <x-fas-home class="w-4 h-4 text-white me-2" />
            На главную
        </a>
    </div>
</section>
