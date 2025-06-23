<?php

namespace RCV\DataGrid\Enums;

use RCV\DataGrid\ColumnTypes\Date;
use RCV\DataGrid\ColumnTypes\Text;
use RCV\DataGrid\ColumnTypes\Boolean;
use RCV\DataGrid\ColumnTypes\Decimal;
use RCV\DataGrid\ColumnTypes\Integer;
use RCV\DataGrid\ColumnTypes\Datetime;
use RCV\DataGrid\ColumnTypes\Aggregate;
use RCV\DataGrid\Exceptions\InvalidColumnTypeException;

class ColumnTypeEnum
{
    /**
     * String.
     */
    const STRING    = 'string';

    /**
     * Integer.
     */
    const INTEGER   = 'integer';

    /**
     * Decimal.
     */
    const DECIMAL   = 'decimal';

    /**
     * Boolean.
     */
    const BOOLEAN   = 'boolean';

    /**
     * Date.
     */
    const DATE      = 'date';

    /**
     * Datetime.
     */
    const DATETIME  = 'datetime';

    /**
     * Aggreagate.
     */
    const AGGREGATE = 'aggregate';

    /**
     * Get the corresponding class name for the column type.
     *
     * @param string $type
     * @return string
     * @throws InvalidColumnTypeException
     */
    public static function getClassName(string $type): string
    {
        $map = [
            self::STRING    => Text::class,
            self::INTEGER   => Integer::class,
            self::DECIMAL   => Decimal::class,
            self::BOOLEAN   => Boolean::class,
            self::DATE      => Date::class,
            self::DATETIME  => Datetime::class,
            self::AGGREGATE => Aggregate::class,
        ];

        if (!isset($map[$type])) {
            throw new InvalidColumnTypeException("Invalid column type: {$type}");
        }

        return $map[$type];
    }
}
