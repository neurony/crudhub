<?php

namespace Zbiller\Crudhub\Contracts;

interface AdminSortContract
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
