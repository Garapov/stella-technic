<section class="fixed max-w-md p-4 mx-auto bg-white border border-gray-200 dark:bg-gray-800 left-12 bottom-16 dark:border-gray-700 rounded-2xl z-50" x-data="{
    isCookieConsentOpened: $persist(true),
    close() {
        this.isCookieConsentOpened = false;
    }
}" x-show="isCookieConsentOpened" x-cloak>
    <h2 class="font-semibold text-gray-800 dark:text-white">🍪 Этот сайт использует файлы cookie.</h2>

    <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">Продолжая использовать сайт, вы даете свое согласие на <a href="#" class="text-blue-500 hover:underline">использование файлов cookie</a>. </p>
    
    <div class="flex items-center justify-between mt-4 gap-x-4 shrink-0">
        <button class=" text-xs bg-gray-900 font-medium rounded-lg hover:bg-gray-700 text-white px-4 py-2.5 duration-300 transition-colors focus:outline-none" @click="close">
            Закрыть
        </button>
    </div>
</section>