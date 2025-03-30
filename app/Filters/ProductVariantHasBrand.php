<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ProductVariantHasBrand extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$hasbrand';

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function (Builder $query) {
            $values = $this->values;

            $query->whereHas("product", function ($subquery) use ($values) {
                $subquery->whereHas("brand", function ($brandQuery) use (
                    $values
                ) {
                    $brandQuery->whereIn("id", $values);
                });
            });
        };
    }
}
