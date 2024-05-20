<?php

namespace Zbiller\Crudhub\Facades;

use Illuminate\Support\Facades\Facade;
use Zbiller\Crudhub\Contracts\FlashHelperContract;

/**
 * @method static void success(string $message = 'Operation successful')
 * @method static void error(string $message = 'Operation failed', ?\Throwable $exception = null)
 *
 * @see \Zbiller\Crudhub\Helpers\FlashHelper
 */
class Flash extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return FlashHelperContract::class;
    }
}
