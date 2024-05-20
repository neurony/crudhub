@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use Zbiller\Crudhub\Sorts\Sort;

class {{ $className }} extends Sort
{
    /**
     * @return string
     */
    public function field(): string
    {
        return Sort::DEFAULT_SORT_FIELD;
    }

    /**
     * @return string
     */
    public function direction(): string
    {
        return Sort::DEFAULT_DIRECTION_FIELD;
    }
}
