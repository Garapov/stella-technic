@aware(['page'])
<div class="py-4 md:py-8">
    <div class="container mx-auto">
        <div class="grid grid-cols-4 gap-4 items-{{ $alignment }}">
            <div class="col-span-2">
                <img src="{{ Storage::disk(config('filesystems.default'))->url($image) }}" class="rounded-lg w-full">
            </div>
            <div class="col-span-2">
                {!!html_entity_decode($text)!!}
            </div>
        </div>
    </div>
</div>
