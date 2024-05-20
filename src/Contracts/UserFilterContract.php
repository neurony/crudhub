<?php

namespace Zbiller\Crudhub\Contracts;

interface UserFilterContract
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
