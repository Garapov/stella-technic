<?php

namespace App\Livewire\Product\Components;

use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class Params extends Component
{
    public $variation;
    
    public function mount($variation = null)
    {
        $this->variation = $variation;
    }

    public function render()
    {
        return view('livewire.product.components.params');
    }

    #[Computed()]
    public function groupedParams()
    {
        return Cache::rememberForever('grouped-params:product_' . $this->variation->sku . '_params', function () {
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
        });
    }

    public function placeholder()
    {
        return view('placeholders.product.params');
    }
}
