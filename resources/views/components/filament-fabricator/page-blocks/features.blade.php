@aware(['page'])
<section class="text-gray-600 body-font bg-gray-200 dark:bg-gray-700">
    <div class="container py-24 mx-auto">
        <div class="text-center mb-10">
            <h2 class="md:text-4xl text-3xl font-medium text-center title-font text-gray-900 dark:text-gray-200 mb-2">
                {{$title}}</h2>
            <p class="text-base dark:text-gray-400 leading-relaxed xl:w-2/4 lg:w-3/4 mx-auto">{{ $subtitle }}</p>
        </div>
        <div class="flex flex-wrap lg:w-4/5 sm:mx-auto sm:mb-2 -mx-2">
            @foreach ($features as $feature)
                <div class="p-2 sm:w-1/2 w-full">
                    <div class="bg-gray-100 rounded flex p-4 h-full items-center">
                        {{-- {{$feature->icon}} --}}
                        <div class="w-8 h-8 text-indigo-500 mr-4">
                            {{ svg($feature->icon) }}
                        </div>
                        <span class="title-font font-medium">{{ $feature->text }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

