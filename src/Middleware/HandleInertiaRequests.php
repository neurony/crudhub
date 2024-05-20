<?php

namespace Zbiller\Crudhub\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;
use Zbiller\Crudhub\Facades\Module;
use Zbiller\Crudhub\Resources\Resource;

class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'crudhub';

    /**
     * @see https://inertiajs.com/asset-versioning
     * @param Request $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @see https://inertiajs.com/shared-data
     * @param Request $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(
            parent::share($request),
            $this->getBaseSharedData($request),
            $this->getLangSharedData($request),
        );
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getBaseSharedData(Request $request): array
    {
        return [
            'prefix' => config('crudhub.admin.prefix', 'admin'),
            'auth' => Auth::user() ? Resource::make('admin_resource', Auth::user()) : null,
            'query' => $request->query(),
            'menu' => config('crudhub.menu.sidebar', []),
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error', $request->session()->has('errors') ? 'Validation errors' : null),
            ],
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getLangSharedData(Request $request): array
    {
        if (!Module::hasModuleInstalled('lang')) {
            return [];
        }

        return [
            'locales' => [
                'current' => app()->getLocale(),
                'default' => \Zbiller\CrudhubLang\Singletons\LocaleSingleton::getDefaultLocale(),
                'active' => \Zbiller\CrudhubLang\Singletons\LocaleSingleton::getActiveLocales(),
            ],
        ];
    }
}
