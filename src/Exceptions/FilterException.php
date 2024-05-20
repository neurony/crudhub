<?php

namespace Zbiller\Crudhub\Exceptions;

use Exception;

class FilterException extends Exception
{
    /**
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noOperatorSupplied(string $field, string $class): self
    {
        return new self(
            'For each field declared as filterable, you must specify an operator type.' . PHP_EOL .
            'Please specify an operator for "' . $field . '" in "' . $class . '".' . PHP_EOL .
            'Example: ---> "field" => "...operator:like..."'
        );
    }

    /**
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noConditionSupplied(string $field, string $class): self
    {
        return new self(
            'For each field declared as filterable, you must specify a condition type.' . PHP_EOL .
            'Please specify a condition for "' . $field . '" in "' . $class . '".' . PHP_EOL .
            'Example: ---> "field" => "...condition:or..."'
        );
    }

    /**
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noColumnsSupplied(string $field, string $class): self
    {
        return new self(
            'For each field declared as filterable, you must specify the used columns.' . PHP_EOL .
            'Please specify the columns for "' . $field . '" in "' . $class . '"' . PHP_EOL .
            'Example: ---> "field" => "...columns:name,content..."'
        );
    }
}
