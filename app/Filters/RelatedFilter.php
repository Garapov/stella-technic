<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class RelatedFilter extends Filter
{
    protected static string $operator = '$related';

    public function apply(): Closure
    {
        return function (Builder $query) {
            $values = $this->values;

            // Приводим к массиву, если передано строкой
            if (is_string($values)) {
                $values = json_decode($values, true) ?? [$values];
            }

            // Убедимся, что $values — массив чисел
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

            if (empty($values)) {
                return; // нет смысла добавлять whereHas
            }

            $query->whereHas($this->column, function ($subquery) use ($values) {
                $table = $subquery->getModel()->getTable();
                $subquery->whereIn("{$table}.id", $values);
            });
        };
    }
}
