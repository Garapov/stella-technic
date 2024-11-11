<?php

namespace App\Models;

use Datlechin\FilamentMenuBuilder\Concerns\HasMenuPanel;
use Datlechin\FilamentMenuBuilder\Contracts\MenuPanelable;

use Z3d0X\FilamentFabricator\Models\Page as ModelsPage;

class Page extends ModelsPage implements MenuPanelable
{
    use HasMenuPanel;

    public function getMenuPanelTitleColumn(): string
    {
        return 'title';
    }
 
    public function getMenuPanelUrlUsing(): callable
    {
        return fn (self $model) => '/' . $model->slug;
    }
}
