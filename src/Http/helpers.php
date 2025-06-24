<?php

use RCV\DataGrid\DataGrid;
use RCV\DataGrid\Exceptions\InvalidDataGridException;

if (!function_exists('fn_datagrid')) {

    function fn_datagrid(string $datagridClass, \Opencart\System\Engine\Registry $registry = null): DataGrid 
    {
        if (!class_exists($datagridClass)) {
            throw new InvalidDataGridException("Class '{$datagridClass}' does not exist.");
        }

        if (!is_subclass_of($datagridClass, DataGrid::class)) {
            throw new InvalidDataGridException("'{$datagridClass}' must extend '" . DataGrid::class . "'.");
        }
        
        return new $datagridClass($registry); // Directly instantiate the class
    }
}
