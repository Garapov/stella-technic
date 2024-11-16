@aware(['page'])
<div>
    <section class="text-gray-600 body-font relative bg-gray-200 dark:bg-gray-700">
        <div class="container px-5 py-24 mx-auto">
            <div class="flex flex-col text-center w-full mb-4">
                <h1 class="sm:text-3xl text-2xl font-medium title-font mb-4 text-gray-900 dark:text-gray-200">Contact Us</h1>
                {{-- <p class="lg:w-2/3 mx-auto leading-relaxed text-base">Whatever cardigan tote bag tumblr hexagon brooklyn
                    asymmetrical gentrify.</p> --}}
            </div>

            @livewire('general.formblock', ['form_id' => $form])
        </div>
    </section>
</div>