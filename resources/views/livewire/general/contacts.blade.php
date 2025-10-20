<section class="text-gray-600 body-font relative" >
  <div class="lg:absolute inset-0 bg-gray-300 h-60 lg:h-full" x-ignore>
    <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Af74d2d95ea3687afda44204e9ba0e8da341df786c24878b1b020b131c063a975&amp;source=constructor" width="100%" height="100%" frameborder="0"></iframe>
  </div>
  @if (setting('map'))
    <div class="lg:container px-4 lg:py-24 py-10 lg:mx-auto flex">
      <div class="lg:w-1/3 bg-white rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0 relative z-10 shadow-md">
        @livewire('general.forms.map')
      </div>
    </div>
  @endif
</section>