<?php

namespace App\Livewire\General\Panel;

use Livewire\Component;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearPageCacheButton extends Component
{
    public function clearCache($uri)
    {
        // dd($uri);
        ResponseCache::forget($uri);

        // session()->flash('message', 'Кеш страницы очищен!');

        $this->dispatch('cache-cleared'); 
    }
    
    public function render()
    {
        return view('livewire.general.panel.clear-page-cache-button');
    }
}
