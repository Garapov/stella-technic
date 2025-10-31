<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Sertificate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Spatie\SchemaOrg\Schema;

#[Lazy()]
class Certificates extends Component
{
    public $scheme;

    public function render()
    {
        return view('livewire.main.certificates');
    }

    #[Computed()]
    public function scheme(): string {
        $listItems = [];
        if ($this->certificates) {

            foreach ($this->certificates as $index => $certificate) {
                
                // элемент списка
                $listItems[] = Schema::listItem()
                    ->position($index + 1)
                    ->item(
                        Schema::imageObject()
                            ->contentUrl(Storage::disk(config('filesystems.default'))->url($certificate->image))
                            ->name($certificate->name)
                    );
            }

        }

        return Schema::itemList()
                ->name('Сертификаты компании')
                ->itemListElement($listItems)->toScript();;
    }

    #[Computed()]
    public function certificates()
    {
        return Sertificate::where('show_on_main', true)->get();
    }

    public function placeholder()
    {
        return view('placeholders.main.certificates');
    }
}
