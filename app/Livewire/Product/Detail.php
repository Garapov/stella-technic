<?php

namespace App\Livewire\Product;

use App\Models\Delivery;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Detail extends Component
{
    public $product;
    public $variation;
    public $groupedParams;
    public $path;
    public $files = [];
    public $deliveries;
    public $features;

    public function mount($path = null, $variation = null)
    {
        $this->path = $path;
        $this->deliveries = Delivery::where("is_active", true)->get();

        
        $this->variation = $variation;
        
        if (!$this->variation) abort(404);
        
        if ($this->variation->is_hidden || $this->variation->product->is_hidden) abort(404);
        
        $this->features = $this->variation->product->features->sortBy('sort');
        // dd($this->variation->parametrs->pluck('value', 'productParam.name')->toArray());

        $this->product = $this->variation->product;
        $this->groupedParams = $this->getGroupedParams();

        if ($this->variation->show_category_files) {
        
            foreach($this->product->categories as $category) {
                if (!$category->files) continue;
                foreach($category->files as $file) {
                    $this->files[] = $file;
                }
            }
        }

        if (!empty($this->variation->files)) {
            foreach($this->variation->files as $file) {
                $this->files[] = $file;
            }
        }
    }

    protected function getGroupedParams()
    {
        $variants = $this->variation->product->variants;
        $groupedParams = [];
        $currentVariantId = $this->variation->id;

        // Получаем текущие параметры
        
        $currentParams = $this->variation->paramItems->sortBy('productParam.sort')
            ->unique(function ($item) {
                
                return $item->productParam->name . "_" . $item->title;
            })
            ->mapWithKeys(function ($param) {
                
                return [
                    $param->productParam->name => [
                        "id" => $param->id,
                        "title" => $param->title,
                        "value" => $param->value,
                        "is_fixed" => $param->productParam->fixed,
                        "sort" => $param->sort
                    ],
                ];
            })
            ->toArray();
        
        // Собираем все возможные параметры и их комбинации
        $availableCombinations = [];

        foreach ($variants as $variant) {
            // Собираем уникальные параметры для варианта
            $variantParams = $variant->paramItems->sortBy('productParam.sort')
                ->unique(function ($item) {
                    return $item->productParam->name . "_" . $item->title;
                })
                ->mapWithKeys(function ($param) {
                    return [
                        $param->productParam->name => [
                            "id" => $param->id,
                            "title" => $param->title,
                            "value" => $param->value,
                            "is_fixed" => $param->productParam->fixed,
                            "sort" => $param->sort
                        ],
                    ];
                })
                ->toArray();

            if (!empty($variantParams)) {
                $availableCombinations[] = [
                    "variant_id" => $variant->id,
                    "params" => $variantParams,
                ];

                foreach ($variantParams as $paramName => $param) {
                    if (!isset($groupedParams[$paramName])) {
                        $groupedParams[$paramName] = [
                            "name" => $paramName,
                            "values" => [],
                        ];
                    }

                    $existingValue = collect(
                        $groupedParams[$paramName]["values"]
                    )->firstWhere("title", $param["title"]);

                    if (!$existingValue) {
                        $groupedParams[$paramName]["values"][] = [
                            "id" => $param["id"],
                            "title" => $param["title"],
                            "value" => $param["value"],
                            "is_fixed" => $param["is_fixed"],
                            "variant_id" => $variant->id,
                            "is_current" =>
                                isset($currentParams[$paramName]) &&
                                $currentParams[$paramName]["title"] ===
                                    $param["title"],
                            "is_available" => false,
                            "sort" => $param['sort']
                        ];
                    }
                }
            }
        }

        // Проверяем доступность значений
        foreach ($groupedParams as $paramName => &$paramGroup) {
            foreach ($paramGroup["values"] as &$value) {
                // Если параметр fixed, то он всегда доступен и не влияет на доступность других опций
                if ($value["is_fixed"]) {
                    $value["is_available"] = true;
                    continue;
                }

                if ($value["is_current"]) {
                    $value["is_available"] = true;
                    continue;
                }

                foreach ($availableCombinations as $combination) {
                    $combinationParams = $combination["params"];

                    if (
                        isset($combinationParams[$paramName]) &&
                        $combinationParams[$paramName]["title"] ===
                            $value["title"]
                    ) {
                        $isCompatible = true;

                        // Проверяем только те параметры, которые есть в текущей комбинации
                        // Игнорируем fixed параметры при проверке совместимости
                        foreach (
                            $combinationParams
                            as $checkParamName => $checkParam
                        ) {
                            if (
                                $checkParamName === $paramName ||
                                $checkParam["is_fixed"]
                            ) {
                                continue;
                            }

                            if (
                                isset($currentParams[$checkParamName]) &&
                                $currentParams[$checkParamName]["title"] !==
                                    $checkParam["title"]
                            ) {
                                $isCompatible = false;
                                break;
                            }
                        }

                        if ($isCompatible) {
                            $value["is_available"] = true;
                            $value["variant_id"] = $combination["variant_id"];
                            break;
                        }
                    }
                }
            }
        }

        return $groupedParams;
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

    public function render()
    {
        return view("livewire.product.detail");
    }
}
