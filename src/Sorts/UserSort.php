<?php

namespace Zbiller\Crudhub\Sorts;

use Zbiller\Crudhub\Contracts\UserSortContract;

class UserSort extends Sort implements UserSortContract
{
    /**
     * @return string
     */
    public function field(): string
    {
        return 'sort_by';
    }

    /**
     * @return string
     */
    public function direction(): string
    {
        return 'sort_dir';
    }
}
