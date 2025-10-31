<div class="relative" >
  <div class="lg:absolute inset-0 h-60 lg:h-full bg-slate-50 rounded-sm lg:rounded-xl animate-pulse"></div>
    <div class="lg:container px-4 lg:py-24 py-10 lg:mx-auto flex">
      <div class="lg:w-1/3 rounded-lg p-8 flex flex-col md:ml-auto w-full mt-10 md:mt-0 relative z-10 bg-slate-100 animate-pulse">
        <div>
            @if (setting('map'))
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-6 w-2/3 mb-1"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-4 w-full mb-5"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-3 w-2/5 mb-1"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-8 w-full mb-2"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-3 w-2/5 mb-1"></div>
                <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-8 w-full mb-2"></div>
                <div class="flex justify-center">
                    <div class="bg-gray-300 rounded-sm lg:rounded-xl animate-pulse h-12 w-1/3"></div>
                </div>
            @endif
        </div>
      </div>
    </div>
</div>