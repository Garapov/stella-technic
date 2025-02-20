<div class="grid grid-cols-2 gap-4">
    @foreach ($workers as $worker)
        @livewire('general.worker', [
            'worker' => $worker,
        ], key($worker->id))
    @endforeach
</div>
