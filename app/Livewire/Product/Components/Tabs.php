<?php

namespace App\Livewire\Product\Components;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Tabs extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }

    public function render()
    {
        return view('livewire.product.components.tabs');
    }

    #[Computed()]
    public function files()
    {
        $files = [];

        if ($this->variation->product->category->files) {
            foreach($this->variation->product->category->files as $file) {
                $files[] = $file;
            }
        }
        
        if ($this->variation->show_category_files) {
        
            foreach($this->variation->product->categories as $category) {
                if (!$category->files) continue;
                foreach($category->files as $file) {
                    $files[] = $file;
                }
            }
        }

        // dd($this->variation);

        if (!empty($this->variation->files)) {
            foreach($this->variation->files as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    public function downloadFile($index) {
        if (Storage::disk(config('filesystems.default'))->exists($this->files[$index]['file'])) {
            $size = Storage::disk(config('filesystems.default'))->size($this->files[$index]['file']);
            $tempFileUrl = Storage::disk(config('filesystems.default'))->temporaryUrl($this->files[$index]['file'], now()->addMinutes(3));
            $filename = File::basename(Storage::disk(config('filesystems.default'))->url($this->files[$index]['file']));
            $headers = [
                'Content-Length' => $size,
            ];
            return response()->streamDownload(function () use ($tempFileUrl, $filename, $size) {
                if (! ($stream = fopen($tempFileUrl, 'r'))) {
                    throw new \Exception("'Could not open stream for reading file: ['.$filename.']'");
                }

                while (! feof($stream)) {
                    echo fread($stream, 1024);
                }

                fclose($stream);
            }, $filename, $headers);
        }
        // $this->variation->downloadAsset($this->files[$index]['file']);
    }
}
