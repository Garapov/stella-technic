<div class="w-full lg:sticky top-10 lg:col-span-3 col-span-full">
    <div class="flex flex-col-reverse gap-4 p-4 rounded-lg bg-slate-100 animate-pulse shadow">
        <div class="grid grid-cols-8 gap-4">
            <section class="splide gallery-thumbnails !invisible pointer-events-none md:pointer-events-auto md:!visible absolute md:relative">
                <div class="h-full flex flex-col justify-between gap-4">
                    @foreach (range(1,5) as $i)
                        <div class="w-full  flex-1 object-cover bg-gray-300 rounded-sm lg:rounded-xl animate-pulse flex justify-center items-center">
                            <svg class="w-5/6 text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                            </svg>
                        </div>
                    @endforeach
                </div>
            </section>
            <section class="md:col-span-7 col-span-full">
                <div class="w-full aspect-[1/1] bg-gray-300 rounded-sm lg:rounded-xl animate-pulse flex justify-center items-center">
                    <svg class="w-5/6 text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                    </svg>
                </div>
            </section>

        </div>
    </div>
</div>