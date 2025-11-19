<div class="bg-white dark:bg-gray-900 py-4">
    <div class="container mx-auto mb-4">
        @if($title)
            <h2 class="text-2xl font-bold dark:text-white">{{ $title }}</h2>
        @endIf
    </div>
    <div class="grid grid-cols-{{$grid}} gap-4 container mx-auto">
        @foreach ($columns as $column)
            <div class="text-sm content_block">
                {!! html_entity_decode($column) !!}
            </div>
        @endforeach
    </div>
</div>