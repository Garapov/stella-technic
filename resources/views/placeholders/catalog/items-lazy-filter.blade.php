<div class="rounded-lg bg-slate-50 p-3 shadow-sm flex flex-col gap-4 overflow-y-auto fixed md:static left-0 top-0 bottom-0 right-0 z-50 md:translate-x-0 transition-all" :class="isFilterOpened ? 'translate-x-0' : '-translate-x-full'" x-cloak>
    <div class="h-6 bg-gray-200 rounded w-1/3"></div>
    <div class="w-full space-y-3 animate-pulse">
        <!-- Один блок фильтра -->
        <div class="bg-white rounded-xl p-4 shadow-sm border">
            <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
            <div class="space-y-2">
                <div class="h-3 bg-gray-200 rounded"></div>
                <div class="h-3 bg-gray-200 rounded w-5/6"></div>
                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
            </div>
        </div>

        <!-- Несколько копий -->
        <div class="bg-white rounded-xl p-4 shadow-sm border">
            <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
            <div class="space-y-3">
                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-2/5"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-sm border">
            <div class="h-4 bg-gray-200 rounded w-2/5 mb-4"></div>
            <div class="space-y-2">
                <div class="h-3 bg-gray-200 rounded"></div>
                <div class="h-3 bg-gray-200 rounded w-3/4"></div>
            </div>
        </div>

        <!-- Слайдеры -->
        <div class="bg-white rounded-xl p-4 shadow-sm border">
            <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
            <div class="h-2 bg-gray-200 rounded-full w-full mb-3"></div>
            <div class="flex justify-between">
                <div class="h-3 w-12 bg-gray-200 rounded"></div>
                <div class="h-3 w-12 bg-gray-200 rounded"></div>
            </div>
        </div>

        <!-- Повтори при необходимости -->
    </div>
</div>
