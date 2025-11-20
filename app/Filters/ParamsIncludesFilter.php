<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ParamsIncludesFilter extends Filter
{
    protected static string $operator = '$includes';

    public function apply(): Closure
    {
        return function (Builder $query) {
            $data = $this->values; // массив параметров с диапазонами
            Log::info('ParamsIncludesFilter data:', ['data' => $data]);

            $query->where(function($q) use ($data) {
                foreach ($data as $paramName => $range) {
                    Log::info('Processing param:', ['name' => $paramName, 'range' => $range]);
                    $q->where(function($q2) use ($paramName, $range) {
                        $applyNumericFilter = function($sub) use ($paramName, $range) {
                            $sub->whereHas('productParam', fn($q3) => $q3->where('name', $paramName));
                            
                            $driver = DB::connection()->getDriverName();
                            
                            if ($driver === 'pgsql') {
                                $sub->whereRaw("value ~ '^[0-9]+(\.[0-9]+)?$'")
                                    ->whereRaw("CAST(value AS NUMERIC) BETWEEN ? AND ?", [$range['min'], $range['max']]);
                            } elseif ($driver === 'sqlite') {
                                $sub->whereRaw("CAST(value AS REAL) BETWEEN ? AND ?", [$range['min'], $range['max']]);
                            } else {
                                // MySQL / MariaDB
                                $sub->whereRaw("CAST(value AS DECIMAL(10,2)) BETWEEN ? AND ?", [$range['min'], $range['max']]);
                            }
                        };

                        $q2->whereHas('paramItems', $applyNumericFilter)
                           ->orWhereHas('parametrs', $applyNumericFilter);
                    });
                }
            });

            Log::info('ParamsIncludesFilter SQL:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
            // Clone the query to avoid modifying the original query instance for subsequent execution if needed,
            // though here it is the end of the apply closure.
            // Eager load paramItems to avoid N+1 and ensure data is available.
            $debugResults = $query->clone()->with('paramItems')->get();
            
            $debugValues = $debugResults->map(function ($variant) use ($data) {
                $matches = [];
                foreach ($data as $paramName => $range) {
                    $matchingItems = $variant->paramItems->filter(function($item) use ($paramName, $range) {
                        // Ensure we are checking the correct param name and range
                        return isset($item->productParam) && 
                               $item->productParam->name === $paramName && 
                               (float)$item->value >= (float)$range['min'] && 
                               (float)$item->value <= (float)$range['max'];
                    });

                    if ($matchingItems->isNotEmpty()) {
                        $matches[$paramName] = $matchingItems->pluck('value')->toArray();
                    }
                }

                return [
                    'id' => $variant->id,
                    'matches' => $matches,
                    'all_values' => $variant->paramItems->pluck('value')->toArray(),
                ];
            });

            Log::info('ParamsIncludesFilter Result:', ['result' => $debugValues->toArray()]);

        };
    }
}
