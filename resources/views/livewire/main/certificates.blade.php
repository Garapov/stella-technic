<div>
    {!! $this->scheme !!}

    @livewire('general.certificates_gallery', [
        'certificates' => $this->certificates,
    ])
</div>
