<?php

namespace Zbiller\Crudhub\Contracts;

interface MediaModelContract
{
    /**
     * @return string
     */
    public function getFileType(): string;

    /**
     * @return string
     */
    public function getOrderColumnName(): string;
}
