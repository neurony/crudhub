<?php

namespace Zbiller\Crudhub\Commands;

use Database\Seeders\Crudhub\AdminSeeder;
use Database\Seeders\Crudhub\PermissionSeeder;
use Database\Seeders\Crudhub\RoleSeeder;
use Exception;
use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class InstallCommand extends Command
{
    use InteractsWithIO;

    /**
     * @var string
     */
    protected $signature = 'crudhub:install         {--overwrite        : Overwrite files}
                                                    {--no-migrate       : Do not run migrations}
                                                    {--no-seed          : Do not run seeders}';

    /**
     * @var string
     */
    protected $description = 'Install Crudhub';

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var array|string[]
     */
    protected array $flags = [
        'success' => '<fg=green;options=bold>SUCCESS</>',
        'warning' => '<fg=yellow;options=bold>WARNING</>',
        'error' => '<fg=red;options=bold>ERROR</>',
        'info' => '<fg=blue;options=bold>INFO</>',
        'skipped' => '<fg=yellow;options=bold>SKIPPED</>',
    ];

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * @return int
     */
    public function handle()
    {
        if (!$this->healthyDatabaseConnection()) {
            $this->components->error('In order to install Crudhub, please check and configure the database connection');

            return Command::FAILURE;
        }

        $this->publishFiles();
        $this->overwriteFiles();
        $this->updateNodePackages();
        $this->registerRoutes();
        $this->migrateDatabase();
        $this->seedDatabase();
        $this->modifyUserModel();
        $this->optimizeClear();

        return Command::SUCCESS;
    }

    /**
     * @return bool
     */
    public function healthyDatabaseConnection(): bool
    {
        try {
            $this->components->task('Checking the database connection', function () {
                DB::connection()->getPdo();
            });

            return true;
        } catch (\Exception $e) {
            $this->components->error('Could not connect to the database.  Please check your configuration.');
            $this->components->error($e->getMessage());

            return false;
        }
    }

    /**
     * @return void
     */
    protected function publishFiles(): void
    {
        $this->components->info('Publishing third-party files.');

        $this->components->task('Publishing "spatie/laravel-medialibrary" vendor files', function () {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
                '--tag' => 'medialibrary-migrations',
            ]);

            $this->callSilent('vendor:publish', [
                '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
                '--tag' => 'medialibrary-config',
            ]);
        });

        $this->components->task('Publishing "spatie/laravel-permission" vendor files', function () {
            $this->callSilent('vendor:publish', [
                '--provider' => 'Spatie\Permission\PermissionServiceProvider',
            ]);
        });

        $this->components->info('Publishing necessary files.');

        $this->components->task('Publishing config files inside the "config/crudhub/" directory', function () {
            $this->callSilent('vendor:publish', ['--tag' => 'crudhub-config']);
        });

        $this->components->task('Publishing email views inside the "resources/views/vendor/crudhub/" directory', function () {
            $this->callSilent('vendor:publish', ['--tag' => 'crudhub-emails']);
        });

        $this->components->task('Publishing migration files inside the "database/migrations/" directory', function () {
            $this->callSilent('vendor:publish', ['--tag' => 'crudhub-migrations']);
        });

        $this->components->task('Publishing the seeders the "database/seeders/" directory', function () {
            $this->callSilent('vendor:publish', ['--tag' => 'crudhub-seeders']);
        });

        $this->components->task('Publishing root template at "resources/views/crudhub.blade.php"', function () {
            $this->copyIfNotExistOrOverwrite(__DIR__ . '/../../stubs/resources/views/crudhub.blade.php', resource_path('views/crudhub.blade.php'));
        });

        $this->components->task('Publishing Vite config at "crudhub.vite.config.js"', function () {
            $this->copyIfNotExistOrOverwrite(__DIR__ . '/../../stubs/crudhub.vite.config.js', base_path('crudhub.vite.config.js'));
        });

        $this->components->task('Publishing TaiwindCSS config at "crudhub.tailwind.config.js"', function () {
            $this->copyIfNotExistOrOverwrite(__DIR__ . '/../../stubs/crudhub.tailwind.config.js', base_path('crudhub.tailwind.config.js'));
        });

        $this->components->task('Publishing JS resources inside the "resources/js/crudhub" directory', function () {
            $this->files->ensureDirectoryExists(resource_path('js/crudhub'));

            if ($this->option('overwrite')) {
                $this->files->copyDirectory(__DIR__ . '/../../stubs/resources/js', resource_path('js/crudhub'));
            } else {
                $this->copyIfNotExistFile(__DIR__ . '/../../stubs/resources/js', resource_path('js/crudhub'));
            }
        });

        $this->components->task('Publishing CSS resources inside the "resources/css/crudhub" directory', function () {
            $this->files->ensureDirectoryExists(resource_path('css/crudhub'));

            if ($this->option('overwrite')) {
                $this->files->copyDirectory(__DIR__ . '/../../stubs/resources/css', resource_path('css/crudhub'));
            } else {
                $this->copyIfNotExistFile(__DIR__ . '/../../stubs/resources/css', resource_path('css/crudhub'));
            }
        });

        $this->components->task('Publishing the "jsconfig.json" inside the root directory', function () {
            if (file_exists(base_path('jsconfig.json')) || $this->option('overwrite')) {
                $ask = $this->components->choice(
                    question: 'It looks like you already have a JS config in this project. Should I overwrite it?',
                    choices: ['yes', 'no'],
                    default: 'no',
                );

                if ($ask === "yes") {
                    $this->files->copy(__DIR__ . '/../../stubs/jsconfig.json', base_path('jsconfig.json'));
                } else {
                    $this->components->twoColumnDetail('Okay, JS config was not replaced. You should update it manually based on the documentation.', $this->flags['skipped']);
                }
            } else {
                $this->files->copy(__DIR__ . '/../../stubs/jsconfig.json', base_path('jsconfig.json'));
            }
        });

        $this->components->task('Publishing the "tsconfig.json" inside the root directory', function () {
            if (file_exists(base_path('tsconfig.json')) || $this->option('overwrite')) {
                $ask = $this->components->choice(
                    question: 'It looks like you already have a TS config in this project. Should I overwrite it?',
                    choices: ['yes', 'no'],
                    default: 'no',
                );

                if ($ask === "yes") {
                    $this->files->copy(__DIR__ . '/../../stubs/tsconfig.json', base_path('tsconfig.json'));
                } else {
                    $this->components->twoColumnDetail('Okay, TS config was not replaced. You should update it manually based on the documentation.', $this->flags['skipped']);
                }
            } else {
                $this->files->copy(__DIR__ . '/../../stubs/tsconfig.json', base_path('tsconfig.json'));
            }
        });
    }

    /**
     * @return void
     */
    protected function overwriteFiles(): void
    {
        $this->components->info('Overwriting necessary files.');

        $this->components->task('Overwriting the "app/Providers/AppServiceProvider.php" file', function () {
            $ask = $this->components->choice(
                question: 'Crudhub wants to overwrite the "AppServiceProvider.php" file. Should I proceed? (note that this will remove all your changes made to that file)',
                choices: ['yes', 'no'],
                default: 'no',
            );

            if ($ask === "yes") {
                $path = base_path('app/Providers/AppServiceProvider.php');
                $stub = __DIR__ . '/../../stubs/app/Providers/AppServiceProvider.php';

                $this->files->put($path, $this->files->get($stub));
            } else {
                $this->components->twoColumnDetail('Okay, "AppServiceProvider.php" was not replaced. You should update it manually based on the documentation.', $this->flags['skipped']);
            }
        });
    }

    /**
     * @return void
     */
    public function migrateDatabase(): void
    {
        if ($this->option('no-migrate')) {
            return;
        }

        $this->call(MigrateCommand::class);
    }

    /**
     * @return void
     */
    public function seedDatabase(): void
    {
        if ($this->option('no-seed')) {
            return;
        }

        $this->components->info('Seeding database.');

        $this->components->task('Seeding the default roles & permissions', function () {
            $this->callSilent(SeedCommand::class, [
                'class' => PermissionSeeder::class
            ]);

            $this->callSilent(SeedCommand::class, [
                'class' => RoleSeeder::class
            ]);
        });

        $this->components->task('Seeding the default admin user', function () {
            $this->callSilent(SeedCommand::class, [
                'class' => AdminSeeder::class
            ]);
        });
    }

    /**
     * @return void
     */
    protected function modifyUserModel(): void
    {
        $this->components->info('Modifying the User model.');

        try {
            $userFile = $this->laravel['path'] . '/Models/User.php';
            $userContent = $this->files->get($userFile);
        } catch (FileNotFoundException $e) {
            $this->components->twoColumnDetail('The "app/Models/User.php" does not exist', $this->flags['error']);
            $this->components->twoColumnDetail('Please manually extend your User model with "\Zbiller\Crudhub\Models\User"', $this->flags['info']);

            return;
        }

        try {
            $this->components->task('Extend the "User" model', function () use ($userFile, &$userContent) {
                if (!Str::contains($userContent, "extends Authenticatable")) {
                    throw new Exception;
                }

                $userContent = str_replace("extends Authenticatable", "extends \Zbiller\Crudhub\Models\User", $userContent);

                $this->files->put($userFile, $userContent);
            });
        } catch (Exception $e) {
            $this->components->twoColumnDetail('The "app/Models/User.php" does not extend "Authenticatable"', $this->flags['error']);
            $this->components->twoColumnDetail('Please manually extend your User model with "\Zbiller\Crudhub\Models\User"', $this->flags['info']);
        }

        try {
            $this->components->task('Update the "fillable" property', function () use ($userFile, &$userContent) {
                if (Str::contains($userContent, "'active',")) {
                    throw new Exception('The "active" column is already defined in "fillable" property');
                }

                $userContent = str_replace("protected \$fillable = [", "protected \$fillable = [\n        'active',", $userContent);

                $this->files->put($userFile, $userContent);
            });
        } catch (Exception $e) {
            $this->components->twoColumnDetail($e->getMessage(), $this->flags['skipped']);
        }

        try {
            $this->components->task('Update the "casts" property', function () use ($userFile, &$userContent) {
                if (Str::contains($userContent, "'active' => 'boolean'")) {
                    throw new Exception('The "active" column is already defined in "casts" property');
                }

                $userContent = str_replace("protected \$casts = [", "protected \$casts = [\n        'active' => 'boolean',", $userContent);

                $this->files->put($userFile, $userContent);
            });
        } catch (Exception $e) {
            $this->components->twoColumnDetail($e->getMessage(), $this->flags['skipped']);
        }
    }

    /**
     * @return void
     */
    public function optimizeClear(): void
    {
        $this->call(OptimizeClearCommand::class);
    }

    /**
     * @return void
     */
    public function updateNodePackages(): void
    {
        $this->components->info('Updating Node packages.');

        $this->editPackageJson(function ($packages) {
            return [
                "@tailwindcss/forms" => "^0.5.4",
                "@vitejs/plugin-vue" => "^4.2.3",
                "@vue/compiler-sfc" => "^3.3.4",
                "laravel-vite-plugin" => "^0.7.5",
                "tailwindcss" => "^3.3.3",
                "vite" => "^4.0.0",
                "@headlessui/vue" => "^1.7.15",
                "@heroicons/vue" => "^2.0.18",
                "@inertiajs/vue3" => "^1.0.9",
                "@vueform/multiselect" => "^2.6.2",
                "axios" => "^1.1.2",
                "flatpickr" => "^4.6.13",
                "lodash" => "^4.17.21",
                "moment" => "^2.29.4",
                "sweetalert2" => "^11.7.22",
                "vue" => "^3.2.36",
                "vuedraggable" => "^4.1.0",
                "sass" => "^1.64.1",
            ] + $packages;
        });
    }

    /**
     * @return void
     */
    public function registerRoutes(): void
    {
        $this->components->info('Registering routes.');

        try {
            $routes = $this->files->get(base_path('routes/web.php'));

            $this->components->task('Registering the "crudhub" route macro', function () use ($routes) {
                if (!Str::contains($routes, 'Route::crudhub()')) {
                    $this->files->append(base_path('routes/web.php'), "\nRoute::crudhub();\n");
                }
            });
        } catch (Throwable $e) {
            $this->components->twoColumnDetail('Unable to register routes', $this->flags['error']);
            $this->components->twoColumnDetail('Please manually append this to your "routes/web.php": Route::crudhub()', $this->flags['info']);
        }
    }

    /**
     * @param callable $callback
     * @param bool $dev
     * @return void
     */
    protected function editPackageJson(callable $callback, bool $dev = true)
    {
        if (! $this->files->exists(base_path('package.json'))) {
            $this->components->twoColumnDetail('File package.json not found, was not able to install additional packages', $this->flags['error']);

            return;
        }

        $this->components->task('Updating the "package.json" configuration and dependencies', function () use ($callback, $dev) {
            $file = base_path('package.json');
            $key = $dev ? 'devDependencies' : 'dependencies';
            $packages = json_decode($this->files->get($file), true);

            $packages['scripts'] = $packages['scripts'] + [
                "crudhub:dev" => "vite --config crudhub.vite.config.js",
                "crudhub:build" => "vite build --config crudhub.vite.config.js",
            ];

            $packages[$key] = $callback(array_key_exists($key, $packages) ? $packages[$key] : [], $key);

            ksort($packages[$key]);

            $this->files->put($file, json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL);
        });
    }

    /**
     * @param string $path
     * @param string $destination
     * @return void
     */
    private function copyIfNotExistFile(string $path, string $destination): void
    {
        $items = new FilesystemIterator($path);

        collect($items)->each(function ($item) use ($path, $destination) {
            $baseName = $item->getBaseName();
            $destinationPath = "$destination/$baseName";
            $filePath = "$path/$baseName";

            if ($item->isDir()) {
                if (! $this->files->exists($destinationPath)) {
                    $this->files->ensureDirectoryExists($destinationPath);
                }

                $this->copyIfNotExistFile($filePath, $destinationPath);
            }

            if (! file_exists($destinationPath)) {
                $this->files->copy($filePath, $destinationPath);

                return true;
            }
        });
    }

    /**
     * @param string $path
     * @param string $destination
     * @return void
     */
    private function copyIfNotExistOrOverwrite(string $path, string $destination): void
    {
        if (!$this->files->exists($destination) || $this->option('overwrite')) {
            $this->files->copy($path, $destination);
        }
    }
}
