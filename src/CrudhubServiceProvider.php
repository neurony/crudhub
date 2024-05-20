<?php

namespace Zbiller\Crudhub;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Zbiller\Crudhub\Commands\InstallCommand;
use Zbiller\Crudhub\Commands\MakeCrudCommand;
use Zbiller\Crudhub\Contracts\AdminFilterContract;
use Zbiller\Crudhub\Contracts\AdminModelContract;
use Zbiller\Crudhub\Contracts\AdminSortContract;
use Zbiller\Crudhub\Contracts\FlashHelperContract;
use Zbiller\Crudhub\Contracts\MediaModelContract;
use Zbiller\Crudhub\Contracts\MediaUnassignedModelContract;
use Zbiller\Crudhub\Contracts\ModuleHelperContract;
use Zbiller\Crudhub\Contracts\UserFilterContract;
use Zbiller\Crudhub\Contracts\UserModelContract;
use Zbiller\Crudhub\Contracts\UserSortContract;
use Zbiller\Crudhub\Events\TwoFactorCodeGenerated;
use Zbiller\Crudhub\Filters\AdminFilter;
use Zbiller\Crudhub\Filters\UserFilter;
use Zbiller\Crudhub\Helpers\FlashHelper;
use Zbiller\Crudhub\Helpers\ModuleHelper;
use Zbiller\Crudhub\Listeners\SendTwoFactorCodeToUser;
use Zbiller\Crudhub\Middleware\HandleInertiaRequests;
use Zbiller\Crudhub\Models\Admin;
use Zbiller\Crudhub\Models\Media;
use Zbiller\Crudhub\Models\MediaUnassigned;
use Zbiller\Crudhub\Sorts\AdminSort;
use Zbiller\Crudhub\Sorts\UserSort;

class CrudhubServiceProvider extends BaseServiceProvider
{
    /**
     * @var ConfigRepository
     */
    protected ConfigRepository $config;

    /**
     * @var Router
     */
    protected Router $router;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->config = $this->app->config;
    }

    /**
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->router = $router;

        Schema::defaultStringLength(125);

        $this->publishConfigs();
        $this->publishEmails();
        $this->publishMigrations();
        $this->publishSeeders();
        $this->overwriteConfigs();
        $this->registerCommands();
        $this->registerMiddlewares();
        $this->registerRouteBindings();
        $this->registerRoutes();
        $this->registerRedirectMacros();
        $this->registerEventListeners();
        $this->loadViews();
        $this->defineSuperAdmin();
    }

    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->mergeConfigs();
        $this->registerModelBindings();
        $this->registerFilterBindings();
        $this->registerSortBindings();
        $this->registerHelperBindings();
    }

    /**
     * @return void
     */
    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('crudhub/admin.php'),
            __DIR__ . '/../config/bindings.php' => config_path('crudhub/bindings.php'),
            __DIR__ . '/../config/flash.php' => config_path('crudhub/flash.php'),
            __DIR__ . '/../config/login.php' => config_path('crudhub/login.php'),
            __DIR__ . '/../config/media.php' => config_path('crudhub/media.php'),
            __DIR__ . '/../config/menu.php' => config_path('crudhub/menu.php'),
        ], 'crudhub-config');
    }

    /**
     * @return void
     */
    protected function publishEmails()
    {
        $this->publishes([
            __DIR__ . '/../resources/views/emails' => resource_path('views/vendor/crudhub/emails'),
        ], 'crudhub-emails');
    }

    /**
     * @return void
     */
    protected function publishMigrations()
    {
        if (empty(File::glob(database_path('migrations/*_create_crudhub_tables.php')))) {
            $timestamp = date('Y_m_d_His', time() + 60);

            $this->publishes([
                __DIR__ . '/../database/migrations/create_crudhub_tables.php' => database_path() . "/migrations/{$timestamp}_create_crudhub_tables.php",
            ], 'crudhub-migrations');
        }
    }

    /**
     * @return void
     */
    protected function publishSeeders()
    {
        $this->publishes([
            __DIR__ . '/../database/seeders' => database_path('seeders/Crudhub'),
        ], 'crudhub-seeders');
    }

    /**
     * @return void
     */
    protected function overwriteConfigs()
    {
        $config = $this->config['crudhub'];

        $this->config->set([
            'media-library.disk_name' => $config['media']['disk_name'] ?? 'public',
            'media-library.media_model' => $config['media']['media_model'] ?? $config['bindings']['models']['media_model'],
        ]);
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeCrudCommand::class,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function registerMiddlewares()
    {
        $middleware = $this->config['crudhub.bindings.middleware'];

        $this->router->aliasMiddleware('crudhub.inertia.handle_requests', $middleware['inertia_handle_requests_middleware'] ?? HandleInertiaRequests::class);

        $this->router->aliasMiddleware('role', RoleMiddleware::class);
        $this->router->aliasMiddleware('permission', PermissionMiddleware::class);
        $this->router->aliasMiddleware('role_or_permission', RoleOrPermissionMiddleware::class);
    }

    /**
     * @return void
     */
    protected function registerRouteBindings()
    {
        Route::model('user', UserModelContract::class);
        Route::model('admin', AdminModelContract::class);
        Route::model('media', MediaModelContract::class);
        Route::model('mediaUnassigned', MediaUnassignedModelContract::class);
    }

    /**
     * @return void
     */
    protected function registerRedirectMacros()
    {
        Redirect::macro('saved', function (Request $request, ?string $indexRoute = null, ?string $editRoute = null) {
            if ($request->get('save_stay', false) === true || $request->get('save_continue', false) === true) {
                return $editRoute ? Redirect::to($editRoute) : Redirect::back();
            }

            return $indexRoute ? Redirect::to($indexRoute) : Redirect::back();
        });

        Redirect::macro('deleted', function (?string $indexRoute = null, bool $withQueryString = true) {
            $previousUrl = URL::previous();
            $previousPath = parse_url($previousUrl)['path'] ?? null;
            $previousQueryString = parse_url($previousUrl)['query'] ?? null;

            return Redirect::to(
                ($indexRoute ?? $previousPath) . ($withQueryString && $previousQueryString ? '?' . $previousQueryString : '')
            );
        });

        Redirect::macro('previous', function () {
            $previousUrl = URL::previous();
            $previousPath = parse_url($previousUrl)['path'] ?? null;
            $previousQueryString = parse_url($previousUrl)['query'] ?? null;

            return Redirect::to(
                $previousPath . ($previousQueryString ? '?' . $previousQueryString : '')
            );
        });
    }

    /**
     * @return void
     */
    protected function registerEventListeners(): void
    {
        Event::listen(TwoFactorCodeGenerated::class, [
            SendTwoFactorCodeToUser::class, 'handle'
        ]);
    }

    /**
     * @return void
     */
    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'crudhub');
    }

    /**
     * @return void
     */
    protected function defineSuperAdmin(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Root') ? true : null;
        });
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        Route::macro('crudhub', function () {
            require __DIR__ . '/../routes/routes.php';
        });
    }

    /**
     * @return void
     * @throws BindingResolutionException
     */
    protected function mergeConfigs()
    {
        $this->mergeConfigKeysFrom(__DIR__ . '/../config/auth.php', 'auth');
        $this->mergeConfigKeysFrom(__DIR__ . '/../config/filesystems.php', 'filesystems');

        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'crudhub.admin');
        $this->mergeConfigFrom(__DIR__ . '/../config/bindings.php', 'crudhub.bindings');
        $this->mergeConfigFrom(__DIR__ . '/../config/flash.php', 'crudhub.flash');
        $this->mergeConfigFrom(__DIR__ . '/../config/login.php', 'crudhub.login');
        $this->mergeConfigFrom(__DIR__ . '/../config/media.php', 'crudhub.media');
        $this->mergeConfigFrom(__DIR__ . '/../config/menu.php', 'crudhub.menu');
    }

    /**
     * @return void
     */
    protected function registerModelBindings()
    {
        $binding = $this->config['crudhub.bindings.models'];

        $this->app->bind(UserModelContract::class, $binding['user_model'] ?? \App\Models\User::class);
        $this->app->bind(AdminModelContract::class, $binding['admin_model'] ?? Admin::class);
        $this->app->bind(MediaModelContract::class, $binding['media_model'] ?? Media::class);
        $this->app->bind(MediaUnassignedModelContract::class, $binding['media_unassigned_model'] ?? MediaUnassigned::class);
    }

    /**
     * @return void
     */
    protected function registerFilterBindings()
    {
        $binding = $this->config['crudhub.bindings.filters'];

        $this->app->singleton(AdminFilterContract::class, $binding['admin_filter'] ?? AdminFilter::class);
        $this->app->singleton(UserFilterContract::class, $binding['user_filter'] ?? UserFilter::class);
    }

    /**
     * @return void
     */
    protected function registerSortBindings()
    {
        $binding = $this->config['crudhub.bindings.sorts'];

        $this->app->singleton(AdminSortContract::class, $binding['admin_sort'] ?? AdminSort::class);
        $this->app->singleton(UserSortContract::class, $binding['user_sort'] ?? UserSort::class);
    }

    /**
     * @return void
     */
    protected function registerHelperBindings()
    {
        $binding = $this->config['crudhub.bindings.helpers'];

        $this->app->singleton(ModuleHelperContract::class, ModuleHelper::class);

        $this->app->singleton(FlashHelperContract::class, function ($app) use ($binding) {
            $implementation = $binding['flash_helper'] ?? FlashHelper::class;

            return new $implementation($app['request']);
        });
    }

    /**
     * @param string $path
     * @param string $key
     * @return void
     * @throws BindingResolutionException
     */
    protected function mergeConfigKeysFrom($path, $key): void
    {
        if (!($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, $this->mergeConfigKeys(require $path, $config->get($key, [])));
        }
    }

    /**
     * @param array $original
     * @param array $merging
     * @return array
     */
    protected function mergeConfigKeys(array $original, array $merging): array
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfigKeys($value, $merging[$key]);
        }

        return $array;
    }
}
