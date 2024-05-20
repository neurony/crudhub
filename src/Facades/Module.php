<?php

namespace Zbiller\Crudhub\Facades;

use Illuminate\Support\Facades\Facade;
use Zbiller\Crudhub\Contracts\ModuleHelperContract;

/**
 * @method static array getModules()
 * @method static array getInstalledModules()
 * @method static bool hasModuleInstalled(string $module)
 *
 * @see \Zbiller\Crudhub\Helpers\FlashHelper
 */
class Module extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return ModuleHelperContract::class;
    }
}
