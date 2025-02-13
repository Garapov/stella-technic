@aware(['page'])
<div class="px-4 py-4 md:py-8">
    <div class="container mx-auto">
        <div class="grid grid-cols-{{ $grid }} gap-4">
            @foreach ($gallery as $image)
                <img src="{{ asset('/storage/' . $image->uuid . '/filament-thumbnail.' . $image->file_extension) }}" class="w-full h-full object-cover object-center rounded-lg">
            @endforeach
        </div>
    </div>
</div>
