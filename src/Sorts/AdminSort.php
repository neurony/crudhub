<?php

namespace Zbiller\Crudhub\Sorts;

use Zbiller\Crudhub\Contracts\AdminSortContract;

class AdminSort extends Sort implements AdminSortContract
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
