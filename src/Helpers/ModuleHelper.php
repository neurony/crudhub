<?php

namespace Zbiller\Crudhub\Helpers;

use Zbiller\Crudhub\Contracts\ModuleHelperContract;

class ModuleHelper implements ModuleHelperContract
{
    /**
     * @var array|string[]
     */
    protected array $modules = [
        'lang' => '\Zbiller\CrudhubLang\CrudhubLangServiceProvider',
        'cms' => '\Zbiller\CrudhubCms\CrudhubCmsServiceProvider',
    ];

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return array
     */
    public function getInstalledModules(): array
    {
        $installed = [];

        foreach ($this->modules as $module => $provider) {
            if (!$this->hasModuleInstalled($module)) {
                continue;
            }

            $installed[$module] = $provider;
        }

        return $installed;
    }

    /**
     * @param string $module
     * @return bool
     */
    public function hasModuleInstalled(string $module): bool
    {
        $provider = $this->modules[$module] ?? null;

        return $provider && class_exists($provider);
    }
}
