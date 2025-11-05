<div>
    <div class="w-1/5 h-8 bg-gray-200 rounded animate-pulse mb-4"></div>
    <div class="flex justify-between items-center mb-4">
        <div class="w-[60px] h-10 bg-gray-200 rounded animate-pulse"></div>
        <div class="w-1/6 h-10 bg-gray-200 rounded animate-pulse"></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach (range(1,8) as $key=>$i)
            @include('placeholders.general.variation')
        @endforeach
    </div>
</div>
