<div>
    @if (setting('map'))
        <h2 class="text-gray-900 text-lg mb-1 font-medium title-font">Остались вопросы ?</h2>
        <p class="leading-relaxed mb-5 text-gray-600">Оставьте свой номер телефона и мы перезвоним.</p>
        @livewire('general.flatformblock', ['form_id' => setting('map')])
    @endif
</div>