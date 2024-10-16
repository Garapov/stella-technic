<?php

namespace App\Livewire\General;

use Livewire\Component;

class Header extends Component
{
    public $isCatalogOpened = false;
    public $isPopularOpened = false;

    public function render()
    {
        return view('livewire.general.header');
    }

    public function toggleCatalog()
    {
        $this->isCatalogOpened = !$this->isCatalogOpened;
    }
    public function closeCatalog()
    {
        $this->isCatalogOpened = false;
    }

    public function togglePopular()
    {
        $this->isPopularOpened = !$this->isPopularOpened;
    }
    public function closePopular()
    {
        $this->isPopularOpened = false;
    }
}
