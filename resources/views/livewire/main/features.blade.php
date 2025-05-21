<div>
    @if (count($features) > 0)
        <section class="text-gray-600 body-font bg-slate-50 dark:bg-gray-700 px-4">
            <div class="lg:container py-10 lg:mx-auto">
                <div class="text-center mb-10">
                    <h1 class="md:text-4xl text-3xl font-bold text-center title-font mb-2"><span class="text-blue-500">Более 30 лет</span> <span class="text-slate-700">на рынке оборудования и
                        хранения для складов</span></h1>
                </div>
                <div class="grid lg:grid-cols-5 grid-cols-1 gap-4">
                    @foreach ($features as $feature)
                        <div class="bg-slate-100 rounded flex p-4 h-full items-center">
                            <div class="min-w-12 min-h-12 w-12 h-12 text-indigo-500 mr-4">
                                <img src="{{ Storage::disk(config("filesystems.default"))->url($feature->icon) }}" alt="">
                            </div>
                            <span class="title-font font-medium">{{ $feature->text }}</span>
                        </div>
                    @endforeach
                </div>



                <div class="grid lg:grid-cols-4 grid-cols-1 gap-4 mt-10">
                    @foreach ($categories as $category)
                        @if ($category->variationsCount() < 1)
                            @continue
                        @endif
                        @livewire('general.category', [
                            'category' => $category,
                        ], key($category->id))
                    @endforeach
                    
                </div>
            </div>
        </section>
    @endIf
</div>
