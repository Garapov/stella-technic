@aware(['page'])
<div class="px-4 py-4 md:py-8">
    <div class="container mx-auto">
        @if ($image)
            <img src="{{ 'storage/' . $image }}" class="w-full h-[500px] object-cover object-center rounded-lg">
        @endif
    </div>
</div>
