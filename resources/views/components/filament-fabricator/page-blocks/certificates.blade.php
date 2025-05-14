@aware(['page'])
<div class="px-4 py-4 md:py-8 bg-gray-200 dark:bg-gray-800">
    <div class="container mx-auto">
        @if ($title && $type !== 'slider')
            <div class="text-4xl text-slate-600 dark:text-white font-semibold mb-4">
                {{ $title }}
            </div>
        @endif
        @if (count($certificates))
            @php
                /** @var \App\Models\Sertificate[] $certificates */
                $certificates = \App\Models\Sertificate::whereIn('id', $certificates)->get();
            @endphp

            @livewire('general.certificates_gallery', [
                'certificates' => $certificates,
                'title' => $title,
                'type' => $type
            ])
        @endif
    </div>
</div>

