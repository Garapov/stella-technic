<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class RelatedFilter extends Filter
{
    /**
     * Operator string to detect in the query params.
     *
     * @var string
     */
    protected static string $operator = '$related';

    /**
     * Apply filter logic to $query.
     *
     * @return Closure
     */
    public function apply(): Closure
    {
        return function (Builder $query) {
            $values = $this->values;

            if (is_string($values)) {
                $values = json_decode($values, true) ?? [$values];
            }

            $values = collect($values)
                ->map(function ($val) {
                    if (is_string($val) && preg_match('/^\[(\d+)\]$/', $val, $m)) {
                        return (int) $m[1];
                    }
                    return (int) $val;
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($values)) return;

            foreach ($values as $value) {
                $query->whereHas($this->column, function ($subquery) use ($value) {
                    $table = $subquery->getModel()->getTable();
                    $subquery->where("{$table}.id", $value);
                });
            }
        };
    }
}
