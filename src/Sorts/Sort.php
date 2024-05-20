<?php

namespace Zbiller\Crudhub\Sorts;

abstract class Sort
{
    /**
     * @const
     */
    const DEFAULT_SORT_FIELD = 'sort_by';

    /**
     * @const
     */
    const DEFAULT_DIRECTION_FIELD = 'sort_dir';

    /**
     * @const
     */
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';
    const DIRECTION_RANDOM = 'random';

    /**
     * @var string[]
     */
    public static array $directions = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC,
        self::DIRECTION_RANDOM,
    ];

    /**
     * @return string
     */
    abstract public function field(): string;

    /**
     * @return string
     */
    abstract public function direction(): string;

    /**
     * @return string[]
     */
    public function defaults(): array
    {
        return [];
    }
}
