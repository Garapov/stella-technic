<div class="grid grid-cols-2 gap-4">
    @foreach ($vacancies as $vacancy)
        @livewire('general.vacancy', [
            'vacancy' => $vacancy,
        ], key($vacancy->id))
    @endforeach
</div>
