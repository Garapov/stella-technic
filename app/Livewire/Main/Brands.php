<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Brand;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Lazy;
use Spatie\SchemaOrg\Schema;

#[Lazy()]
class Brands extends Component
{
    public $brands;
    public $schema;
    public function mount()
    {
        $this->brands = Brand::all();

        $listItems = [];

        if ($this->brands) {

            foreach ($this->brands as $index => $brand) {
                // элемент списка
                // dd($brand);
                $listItems[] = Schema::listItem()
                    ->position($index + 1)
                    ->item(
                        Schema::brand()
                            ->name($brand->name)
                            ->url(route('client.brands.show', ['slug' => $brand->slug]))
                            ->logo(
                                Schema::imageObject()
                                    ->url(Storage::disk(config('filesystems.default'))->url($brand->image))
                                    ->contentUrl(Storage::disk(config('filesystems.default'))->url($brand->image))
                                    ->caption($brand->name)
                            )
                    );
            }

            // ItemList (общий список)
            $this->schema = Schema::itemList()
                ->name('Список производителей')
                ->itemListElement($listItems)->toScript();
        }
    }
    public function render()
    {
        // sleep(20);
        return view('livewire.main.brands', [
            'brands' => $this->brands,
            'schema' => $this->schema
        ]);
    }

    public function placeholder()
    {
        return view('placeholders.main.brands');
    }
}
