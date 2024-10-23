<?php

namespace App\Livewire\Dashboard\Categories;

use App\Models\Category;
use Livewire\WithFileUploads;
use Livewire\Component;

class Edit extends Component
{
    use WithFileUploads;


    public string $name = "";
    public $file;

    public $category;

    public function mount($slug) {
        $this->category = Category::findBySlug($slug);
        if (!$this->category) return abort(404);

        $this->name = $this->category->name;
    }

    public function render()
    {
        return view('livewire.dashboard.categories.edit');
    }

    public function messages() 
    {
        return [
            'file' => 'Это поле обязательно для заполнения', // 1MB Max
            'name' => 'Это поле обязательно для заполнения',
        ];
    }

    public function save()
    {
        $this->validate([
            
            'name' => 'required|min:3',
        ]);
        if (!$this->file && !$this->category->image) {

            $this->validate([
                'file' => 'image|max:1024', // 1MB Max
            ]);
            
        }

        $this->category->update([
            'name' => $this->name,
            'image' => $this->file ? $this->file->storePublicly('categories', 'public') : $this->category->image,
        ]);
          
        $this->redirect(route('dashboard.categories.index'));

    }
}
