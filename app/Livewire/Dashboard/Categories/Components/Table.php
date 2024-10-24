<?php

namespace App\Livewire\Dashboard\Categories\Components;

use Livewire\Attributes\Url;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $category_id = null;

    #[Url()] 
    public ?string $query = '';

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function mount($category_id = null)
    {
        $this->category_id = $category_id;
    }

    public function render()
    {
        $search = '%'.$this->query.'%';

        return view('livewire.dashboard.categories.components.table', [
            'categories' => Category::where([
                ['name','like', $search],
                ['category_id', '=', $this->category_id]
            ])->paginate(5),
        ]);
    }
}
