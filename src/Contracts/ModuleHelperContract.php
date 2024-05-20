<?php

namespace Zbiller\Crudhub\Contracts;

interface ModuleHelperContract
{
    public function getModules();
    public function getInstalledModules();
    public function hasModuleInstalled(string $module): bool;
}
