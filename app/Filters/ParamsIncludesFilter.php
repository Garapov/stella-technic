<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ParamsIncludesFilter extends Filter
{

    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$includes';


    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function (Builder $query) {

            $data = $this->values;
            $key = array_key_first($data);
            $values = $data[$key];

            // dd($values);

            $query->where(function ($q) use ($values) {
                $q->whereHas('paramItems', function ($subquery) use ($values) {
                    $subquery->whereIn('product_param_items.id', $values);
                })->orWhereHas('parametrs', function ($subquery) use ($values) {
                    $subquery->whereIn('product_param_items.id', $values);
                });
            });
            
        };
    }
}
