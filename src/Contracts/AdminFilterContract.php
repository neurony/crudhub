<?php

namespace Zbiller\Crudhub\Contracts;

interface AdminFilterContract
{
    /**
     * @return string
     */
    public function morph(): string;

    /**
     * @return array
     */
    public function filters(): array;
}
