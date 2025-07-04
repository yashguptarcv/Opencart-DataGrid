<?php

namespace RCV\DataGrid\ColumnTypes;

use RCV\DataGrid\Column;
use RCV\DataGrid\Exceptions\InvalidColumnExpressionException;

class Decimal extends Column
{
    /**
     * Process filter: builds SQL-safe condition strings for CS-Cart.
     */
    public function processFilter($requestedValues): array
    {
        $or_conditions = [];

        if (is_string($requestedValues)) {
            $this->applyDecimalCondition($or_conditions, $requestedValues);
        } elseif (is_array($requestedValues)) {
            foreach ($requestedValues as $value) {
                $this->applyDecimalCondition($or_conditions, $value);
            }
        } else {
            throw new InvalidColumnExpressionException('Only string and array are allowed for decimal column type.');
        }

        if (count($or_conditions) === 1) {
            $final_condition = ' AND ' . $or_conditions[0];
        } elseif (!empty($or_conditions)) {
            $final_condition = ' AND (' . implode(' OR ', $or_conditions) . ')';
        } else {
            $final_condition = '';
        }

        return [$final_condition];
    }

    /**
     * Converts a filter expression into SQL-safe condition string for decimal values.
     */
    private function applyDecimalCondition(array &$conditions, string $value): void
    {
        if (preg_match('/^([<>]=?|=)\s*(-?[\d.]+)$/', $value, $matches)) {
            $operator = $matches[1];
            $decimalValue = (float) $matches[2];
            $conditions[] = "{$this->columnName} {$operator} $decimalValue";

        } elseif (preg_match('/^(-?[\d.]+)\s*-\s*(-?[\d.]+)$/', $value, $matches)) {
            $min = (float) $matches[1];
            $max = (float) $matches[2];
            $conditions[] = "{$this->columnName} BETWEEN $min AND $max";

        } elseif (is_numeric($value)) {
            $conditions[] = "{$this->columnName} = ".(float) $value;
        }
    }
}
