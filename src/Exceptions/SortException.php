<?php

namespace Zbiller\Crudhub\Exceptions;

use Exception;
use Zbiller\Crudhub\Sorts\Sort;

class SortException extends Exception
{
    /**
     * @param string $direction
     * @return static
     */
    public static function invalidDirectionSupplied(string $direction): self
    {
        return new self(
            'Invalid sorting direction.' . PHP_EOL .
            'You provided the direction: "' . $direction . '".' . PHP_EOL .
            'Please provide one of these directions: ' . implode('|', Sort::$directions) . '.'
        );
    }

    /**
     * @param string $relation
     * @param string $type
     * @return static
     * @internal param string $direction
     */
    public static function wrongRelationToSort(string $relation, string $type): self
    {
        return new self(
            'You can only sort records by the following relations: HasOne, BelongsTo.' . PHP_EOL .
            'The relation "' . $relation . '" is of type ' . $type . ' and cannot be sorted by.'
        );
    }
}
