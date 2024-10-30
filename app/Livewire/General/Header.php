<?php

namespace App\Livewire\General;

use App\Models\Category;
use Livewire\Component;

class Header extends Component
{
    public $isCatalogOpened = false;
    public $isPopularOpened = false;
    public $isUserMenuOpened = false;

    public function render()
    {
        return view('livewire.general.header', [
            'categories' => Category::where('category_id', null)->get(),
        ]);
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

    public function toggleUserMenu()
    {
        $this->isUserMenuOpened = !$this->isUserMenuOpened;
    }
    public function closeUserMenu()
    {
        $this->isUserMenuOpened = false;
    }
}
