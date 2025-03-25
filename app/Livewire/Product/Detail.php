<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Models\Image;
use App\Models\ProductVariant;
use Livewire\Component;

class Detail extends Component
{
    public $product;
    public $variation;
    public $groupedParams;

    public function mount($slug)
    {
        $this->variation = ProductVariant::where("slug", $slug)->first();
        $this->product = $this->variation->product;
        $this->groupedParams = $this->getGroupedParams();
    }

    protected function getGroupedParams()
    {
        $variants = $this->variation->product->variants;
        $groupedParams = [];
        $currentVariantId = $this->variation->id;

        // Получаем текущие параметры
        $currentParams = $this->variation->paramItems
            ->unique(function ($item) {
                return $item->productParam->name . "_" . $item->title;
            })
            ->mapWithKeys(function ($param) {
                return [
                    $param->productParam->name => [
                        "id" => $param->id,
                        "title" => $param->title,
                        "value" => $param->value,
                    ],
                ];
            })
            ->toArray();

        \Illuminate\Support\Facades\Log::info("Текущие параметры", [
            "variant_id" => $currentVariantId,
            "params" => $currentParams,
        ]);

        // Собираем все возможные параметры и их комбинации
        $availableCombinations = [];
        foreach ($variants as $variant) {
            // Собираем уникальные параметры для варианта
            $variantParams = $variant->paramItems
                ->unique(function ($item) {
                    return $item->productParam->name . "_" . $item->title;
                })
                ->mapWithKeys(function ($param) {
                    return [
                        $param->productParam->name => [
                            "id" => $param->id,
                            "title" => $param->title,
                            "value" => $param->value,
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
                            "variant_id" => $variant->id,
                            "is_current" =>
                                isset($currentParams[$paramName]) &&
                                $currentParams[$paramName]["title"] ===
                                    $param["title"],
                            "is_available" => false,
                        ];
                    }
                }
            }
        }

        \Illuminate\Support\Facades\Log::info("Доступные комбинации", [
            "combinations" => $availableCombinations,
        ]);

        // Проверяем доступность значений
        foreach ($groupedParams as $paramName => &$paramGroup) {
            foreach ($paramGroup["values"] as &$value) {
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
                        foreach (
                            $combinationParams
                            as $checkParamName => $checkParam
                        ) {
                            if ($checkParamName === $paramName) {
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

                            \Illuminate\Support\Facades\Log::info(
                                "Параметр доступен",
                                [
                                    "param_name" => $paramName,
                                    "param_value" => $value["title"],
                                    "variant_id" => $combination["variant_id"],
                                    "combination" => $combinationParams,
                                ]
                            );

                            break;
                        }
                    }
                }

                \Illuminate\Support\Facades\Log::info("Статус параметра", [
                    "param_name" => $paramName,
                    "param_value" => $value["title"],
                    "is_current" => $value["is_current"],
                    "is_available" => $value["is_available"],
                    "variant_id" => $value["variant_id"],
                ]);
            }
        }

        return $groupedParams;
    }

    public function render()
    {
        return view("livewire.product.detail", [
            "product" => $this->product,
            "variation" => $this->variation,
            "groupedParams" => $this->groupedParams,
        ]);
    }
}
