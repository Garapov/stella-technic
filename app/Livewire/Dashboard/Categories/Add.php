<?php

namespace App\Livewire\Dashboard\Categories;

use App\Models\Category;
use Livewire\WithFileUploads;
use Livewire\Component;

class Add extends Component
{
    use WithFileUploads;


    public string $name = "";
    public $file;

    public function render()
    {
        return view('livewire.dashboard.categories.add');
    }

    public function rules() 
    {
        return [
            'file' => 'image|max:1024', // 1MB Max
            'name' => 'required|min:3',
        ];
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
        $this->validate();

        Category::create([
            'name' => $this->name,
            'image' => $this->file->storePublicly('categories', 'public'),
        ]);
          
        $this->redirect(route('dashboard.categories.index'));

    }
}
