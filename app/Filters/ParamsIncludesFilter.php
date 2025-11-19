<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ParamsIncludesFilter extends Filter
{
    protected static string $operator = '$includes';

    public function apply(): Closure
    {
        return function (Builder $query) {
            $data = $this->values; // массив параметров с диапазонами

            $query->where(function($q) use ($data) {
                foreach ($data as $paramName => $range) {
                    $q->where(function($q2) use ($paramName, $range) {
                        $q2->whereHas('paramItems', function($sub) use ($paramName, $range) {
                            $sub->whereHas('productParam', fn($q3) => $q3->where('name', $paramName))
                                ->whereBetween('value', [$range['min'], $range['max']]);
                        })->orWhereHas('parametrs', function($sub) use ($paramName, $range) {
                            $sub->whereHas('productParam', fn($q3) => $q3->where('name', $paramName))
                                ->whereBetween('value', [$range['min'], $range['max']]);
                        });
                    });
                }
            });

        };
    }
}
