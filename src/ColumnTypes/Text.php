<?php

namespace RCV\DataGrid\ColumnTypes;

use RCV\DataGrid\Column;
use RCV\DataGrid\Enums\FilterTypeEnum;
use RCV\DataGrid\Exceptions\InvalidColumnExpressionException;

class Text extends Column
{
    /**
     * Process filter.
     */
    public function processFilter($requestedValues): array
    {
        $conditions = [];
        
        // Use exact match for dropdown
        if ($this->filterableType === FilterTypeEnum::DROPDOWN) {
            if (is_string($requestedValues)) {
                $conditions[] = " AND {$this->columnName} = $requestedValues";
            } elseif (is_array($requestedValues)) {
                $orConditions = [];
                foreach ($requestedValues as $value) {
                    $orConditions[] = "{$this->columnName} = $value";
                }
                $conditions[] = ' AND (' . implode(' OR ', $orConditions) . ')';
            } else {
                throw new InvalidColumnExpressionException('Only string and array are allowed for dropdown values.');
            }

            return $conditions;
        }

        // Use LIKE match for others
        if (is_string($requestedValues)) {
            $conditions[] = " AND {$this->columnName} LIKE %$requestedValues%";
        } elseif (is_array($requestedValues)) {
            $orConditions = [];
            foreach ($requestedValues as $value) {
                $orConditions[] = "{$this->columnName} LIKE '%$value%'";
            }
            $conditions[] = ' AND (' . implode(' OR ', $orConditions) . ')';
        } else {
            throw new InvalidColumnExpressionException('Only string and array are allowed for text column type.');
        }

        return $conditions;
    }

}
