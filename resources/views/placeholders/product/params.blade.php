<div class="flex flex-col gap-4 bg-slate-100 animate-pulse shadow p-4 rounded-xl">
    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-8 w-2/3"></div>
    <div class="parametrs">
        @foreach (range(1,3) as $i)
            <div class="mb-6">
                {{-- <h3 class="text-lg sm:text-sm font-semibold text-slate-900 dark:text-white @if (count($activeParamName) < 1) hidden @endif">{{ $paramGroup['name'] }} @if ($activeParamName->first())({{$activeParamName->first()['title']}})@endif</h3> --}}
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-2/3"></div>
                <div class="flex flex-wrap gap-4 mt-2">
                    @foreach (range(1,5) as $i)
                        <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-8 w-2/12"></div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>