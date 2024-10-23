<?php

namespace App\Livewire\Dashboard\Categories;

use Livewire\Attributes\Url;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url()] 
    public ?string $query = '';

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function render()
    {
        // dd($this->search);
        $search = '%'.$this->query.'%';
        return view('livewire.dashboard.categories.index', [
            'categories' => Category::where([
                ['name','like', $search],
            ])->paginate(5),
        ]);
    }

    public function delete($category_id)
    {
        $category = Category::findOrFail($category_id); 
        $category->delete();
        // dd($category_id);
    }
}
