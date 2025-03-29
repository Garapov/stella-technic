<?php

namespace App\Filters;

use Abbasudo\Purity\Filters\Filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
            // Предварительная обработка значения
            $values = $this->values;

            // Если значение пришло в виде строки "[334]", преобразуем его в массив с числом
            if (
                is_string($values) &&
                preg_match('/^\[(\d+)\]$/', $values, $matches)
            ) {
                $values = [(int) $matches[1]];
            }
            // Если каждый элемент массива является строкой в формате "[334]"
            elseif (
                is_array($values) &&
                isset($values[0]) &&
                is_string($values[0]) &&
                preg_match('/^\[(\d+)\]$/', $values[0], $matches)
            ) {
                $values = [(int) $matches[1]];
            }

            $query->whereHas($this->column, function ($subquery) use ($values) {
                // Получим имя таблицы динамически
                $table = $subquery->getModel()->getTable();
                $subquery->whereIn("{$table}.id", $values);
            });
        };
    }
}
