<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900 relative flex flex-col gap-4">
    @if($group)
        <div class="text-grey-600 dark:text-white">
            {{$group->pluck('id')}}
            {{ $batch->pluck('id') }}
        </div>
        <h3 class="text-lg sm:text-xl font-semibold text-slate-900 text-grey-600 dark:text-white">{{ $group->first()->batch->name }}</h3>
        <div class="grid grid-cols-4 gap-4">
            <div class="rounded-lg overflow-hidden">
                <img src="{{ Storage::disk(config('filesystems.default'))->url($group->first()->batch->image) }}" alt="" />
            </div>
            <div class="col-span-3 flex flex-col gap-4">
                <div class="flex flex-col gap-2 text-gray-600 dark:text-gray-400">
                    {!! str($group->first()->batch->description)->sanitizeHtml() !!}
                </div>
            </div>
        </div>
        @foreach($group as $item)
            <div class="text-grey-600 dark:text-white">
                {{ $item->sku }}
            </div>
        @endforeach
    @endif
</div>
