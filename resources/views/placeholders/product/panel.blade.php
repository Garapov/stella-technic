<div class="w-full lg:sticky top-10 lg:col-span-4 col-span-full">
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-4 bg-slate-100 animate-pulse shadow p-4 rounded-xl">
            <div class="flex items-center flex-wrap justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-8 w-[100px]"></div>
                    

                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-[50px]"></div>

                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-5 w-[150px]"></div>
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-[50px] w-[50px]"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">

                <div class="bg-gray-200 rounded-sm lg:rounded-xl animate-pulse w-[110px] flex gap-2 items-center py-3 px-2">
                    <div class="flex-1"></div>
                    <div class="bg-gray-300 rounded-sm animate-pulse h-4 w-4"></div>
                    <div class="bg-gray-300 rounded-sm animate-pulse h-4 w-4"></div>
                </div>

                <div class="bg-gray-200 rounded-sm lg:rounded-xl animate-pulse w-[110px] flex gap-2 items-center py-1 px-2 flex-1"></div>

                <div class="bg-gray-200 rounded-sm lg:rounded-xl animate-pulse w-[110px] flex gap-2 items-center py-1 px-2 flex-1"></div>
            </div>
        </div>

        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-5 w-2/5"></div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                <ul class="flex flex-col gap-4">
                    @foreach (range(1,5) as $i)
                        <li class="flex items-center justify-between text-xs gap-2">
                            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-1/5"></div>
                            <span class="grow border-b border-slate-300 border-dashed"></span>
                            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-1/12"></div>
                        </li>
                    @endforeach
                </ul>
                <ul class="flex flex-col gap-4">
                    @foreach (range(1,5) as $i)
                        <li class="flex items-center justify-between text-xs gap-2">
                            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-1/5"></div>
                            <span class="grow border-b border-slate-300 border-dashed"></span>
                            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-1/12"></div>
                        </li>
                    @endforeach
                </ul>

            {{-- @foreach($variation->parametrs as $parametr)
                <li class="flex items-center justify-between text-sm gap-2">
                    <strong class="font-medium text-slate-500">{{ $parametr->productParam->name }}</strong>
                    <span class="grow border-b border-slate-300 border-dashed"></span>
                    <span class="font-medium">{{ $parametr->title }}</span>
                </li>
            @endforeach --}}
        </div>
        <div class="flex items-center justify-end">
            <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-5 w-2/5"></div>
        </div>
        <div class="md:grid lg:grid-cols-4 md:grid-cols-1 flex gap-4 md:overflow-x-visible overflow-x-auto">
            @foreach (range(1,4) as $i)
                <div class="bg-gray-100 shadow-md rounded-lg flex p-4 h-full items-center flex flex-col gap-2 items-center animate-pulse">
                    <svg class="w-2/6 text-gray-200 dark:text-gray-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                    </svg>
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-2 w-full"></div>
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-2 w-2/5"></div>
                </div>
            @endforeach
        </div>
    </div>






</div>