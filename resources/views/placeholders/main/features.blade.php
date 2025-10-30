
<div class="xl:px-[100px] px-[20px] xl:py-10 pt-4 pb-10 flex flex-col gap-10 bg-slate-50">
        
        <div class="flex flex-col items-center gap-4">
            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-10 w-1/2"></div>
        </div>
        <div class="grid lg:grid-cols-5 sm:grid-cols-2 grid-cols-2 gap-4 order-3 lg:order-none">
            @foreach (range(1,5) as $i)
                <div class="rounded flex flex-col lg:flex-row p-4 items-center gap-4 bg-slate-100 rounded-sm lg:rounded-xl animate-pulse">
                    <div class="min-w-12 min-h-12 w-12 h-12">
                        <svg class="w-full text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                            <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                        </svg>
                    </div>
                    <div class="flex flex-col gap-2 flex-1 w-full">
                        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-2.5 w-full"></div>
                        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-2.5 w-1/2"></div>
                    </div>
                </div>
            @endforeach
        </div>



    <div class="grid lg:grid-cols-4 md:grid-cols-3 grid-cols-2 gap-4">
        @foreach (range(1,8) as $i)
            <div class="rounded flex flex-col lg:flex-row p-4 items-center gap-4 bg-slate-100 rounded-sm lg:rounded-xl animate-pulse">
                
                <div class="flex items-end gap-2 flex-1 w-full">
                    <div class="bg-gray-300 rounded-full animate-pulse h-4 w-4"></div>
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 flex-1"></div>
                </div>
                <div class="min-w-12 min-h-12 w-12 h-12">
                    <svg class="w-full text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                    </svg>
                </div>
            </div>
        @endforeach

    </div>
</div>
