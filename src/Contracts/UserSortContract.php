<?php

namespace Zbiller\Crudhub\Contracts;

interface UserSortContract
{
    /**
     * @return string
     */
    public function field(): string;

    /**
     * @return string
     */
    public function direction(): string;
}
