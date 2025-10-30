<div>

        <section class="text-gray-600 body-font bg-slate-50 flex flex-col">

            <div class="xl:px-[100px] px-[20px] xl:py-10 pt-4 pb-10 flex flex-col gap-10">
                 @if (count($this->features) > 0)
                    {!! $this->featuresScheme !!}
                    <div class="text-center order-2 lg:order-none">
                        <h1 class="md:text-4xl lg:text-3xl text-xl font-bold text-center title-font"><span class="text-blue-500">Более 30 лет</span> <span class="text-slate-700">на рынке оборудования и
                            хранения для складов</span></h1>
                    </div>
                    <div class="grid lg:grid-cols-5 sm:grid-cols-2 grid-cols-2 gap-4 order-3 lg:order-none">
                        @foreach ($this->features as $feature)
                            <div class="bg-slate-100 rounded flex flex-col lg:flex-row p-2 h-full items-center gap-4">
                                <div class="min-w-12 min-h-12 w-12 h-12 text-indigo-500">
                                    <img src="{{ Storage::disk(config("filesystems.default"))->url($feature->icon) }}" alt="">
                                </div>
                                <span class="md:text-md text-sm font-medium text-center lg:text-left">{{ $feature->text }}</span>
                            </div>
                        @endforeach
                    </div>
                @endIf


                @if ($this->categories && count($this->categories) > 0)
                    <div class="grid lg:grid-cols-4 md:grid-cols-3 grid-cols-2 gap-4">
                        @foreach ($this->categories as $category)
                            @livewire('general.category', [
                                'category' => $category,
                            ], key($category->id))
                        @endforeach

                    </div>
                @endIf
            </div>
        </section>

</div>
