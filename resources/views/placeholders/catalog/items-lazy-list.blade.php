<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @foreach (range(1,8) as $key=>$i)
        @include('placeholders.general.variation')
    @endforeach
</div>
