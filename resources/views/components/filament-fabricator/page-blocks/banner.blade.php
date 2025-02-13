@aware(['page'])
<div class="px-4 py-4 md:py-8">
    <div class="container mx-auto flex flex-center">
        @if ($image)
            <img src="{{ asset('storage/' . $image) }}" class="rounded-lg">
        @endif
    </div>
</div>
