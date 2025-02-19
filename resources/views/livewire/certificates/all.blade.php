<div class="grid grid-cols-5 gap-4">
    @foreach ($certificates as $certificate)
        @livewire('general.certificate', [
            'certificate' => $certificate,
        ], key($certificate->id))
    @endforeach
</div>
