<?php

namespace App\Livewire\Main;

use Livewire\Component;
use App\Models\Sertificate;
use Illuminate\Support\Facades\Storage;
use Spatie\SchemaOrg\Schema;

class Certificates extends Component
{
    public $certificates;
    public $scheme;

    public function mount()
    {
        $this->certificates = Sertificate::where('show_on_main', true)->get();

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

            // ItemList (общий список)
            $this->scheme = Schema::itemList()
                ->name('Сертификаты компании')
                ->itemListElement($listItems)->toScript();
        }
    }
    public function render()
    {
        return view('livewire.main.certificates', [
            'certificates' => $this->certificates,
        ]);
    }
}
