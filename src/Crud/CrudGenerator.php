<?php

namespace Zbiller\Crudhub\Crud;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Zbiller\Crudhub\Exceptions\GenerateCrudException;

/**
 * The class that generates the CRUDs based on the provided CrudData
 */
class CrudGenerator
{
    /**
     * @var CrudSchema
     */
    protected CrudSchema $schema;

    /**
     * @var CrudOptions
     */
    protected CrudOptions $options;

    /**
     * @var CrudData
     */
    protected CrudData $data;

    /**
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * @param CrudSchema $schema
     * @param CrudOptions $options
     * @throws GenerateCrudException
     */
    public function __construct(CrudSchema $schema, CrudOptions $options)
    {
        $this->schema = $schema;
        $this->options = $options;
        $this->data = new CrudData($schema, $options);
        $this->files = App::make(Filesystem::class);

        try {
            $this->data->initData();
        } catch (Exception $e) {
            throw new GenerateCrudException($e->getMessage());
        }
    }

    /**
     * @throws GenerateCrudException
     * @return array
     */
    public function generate(): array
    {
        $this->checkModelClassExists();
        $this->checkResourceClassExists();
        $this->checkFormRequestClassExists();
        $this->checkFilterClassExists();
        $this->checkSortClassExists();
        $this->checkControllerClassExists();
        $this->checkRoutesExist();
        $this->checkIndexViewExists();
        $this->checkCreateViewExists();
        $this->checkEditViewExists();
        $this->checkFilterViewExists();
        $this->checkFormViewExists();
        $this->checkPermissionsMigrationExists();
        $this->checkMenuConfigExists();

        $this->generateModelClass();
        $this->generateResourceClass();
        $this->generateFormRequestClass();
        $this->generateFilterClass();
        $this->generateSortClass();
        $this->generateControllerClass();
        $this->generateRoutes();
        $this->generateIndexView();
        $this->generateFilterView();
        $this->generateCreateView();
        $this->generateEditView();
        $this->generateFormView();
        $this->generatePermissionsMigration();
        $this->generateMenuButton();

        return [
            'success' => true,
            'classes' => [
                'model_class' => $this->data->modelFilePath,
                'controller_class' => $this->data->controllerFilePath,
                'form_request_class' => $this->data->formRequestFilePath,
                'resource_class' => $this->data->resourceFilePath,
                'filter_class' => $this->data->filterFilePath,
                'sort_class' => $this->data->sortFilePath,
            ],
            'views' => [
                'index_view' => $this->data->indexViewFilePath,
                'create_view' => $this->data->createViewFilePath,
                'edit_view' => $this->data->editViewFilePath,
                'filter_view' => $this->data->filterViewFilePath,
                'form_view' => $this->data->formViewFilePath,
            ],
            'routes' => [
                'routes_file' => base_path('routes/web.php'),
                'route_names' => $this->data->routeNames,
            ],
            'migrations' => [
                'permissions_migration' => $this->data->permissionsMigrationFilePath,
            ],
        ];
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkModelClassExists()
    {
        if ($this->files->exists($this->data->modelFilePath)) {
            throw new GenerateCrudException("File for model '{$this->data->modelFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateModelClass(): void
    {
        $path = $this->data->modelFilePath;
        $content = View::make('crudhub::crud.model_class', $this->data->buildModelData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkResourceClassExists()
    {
        if ($this->files->exists($this->data->resourceFilePath)) {
            throw new GenerateCrudException("File for resource '{$this->data->resourceFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateResourceClass(): void
    {
        $path = $this->data->resourceFilePath;
        $content = View::make('crudhub::crud.resource_class', $this->data->buildResourceData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkFormRequestClassExists()
    {
        if ($this->files->exists($this->data->formRequestFilePath)) {
            throw new GenerateCrudException("File for form request '{$this->data->formRequestFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateFormRequestClass(): void
    {
        $path = $this->data->formRequestFilePath;
        $content = View::make('crudhub::crud.form_request_class', $this->data->buildFormRequestData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkFilterClassExists()
    {
        if ($this->files->exists($this->data->filterFilePath)) {
            throw new GenerateCrudException("File for filter '{$this->data->filterFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateFilterClass(): void
    {
        $path = $this->data->filterFilePath;
        $content = View::make('crudhub::crud.filter_class', $this->data->buildFilterData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkSortClassExists()
    {
        if ($this->files->exists($this->data->sortFilePath)) {
            throw new GenerateCrudException("File for sort '{$this->data->sortFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateSortClass(): void
    {
        $path = $this->data->sortFilePath;
        $content = View::make('crudhub::crud.sort_class', $this->data->buildSortData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkControllerClassExists()
    {
        if ($this->files->exists($this->data->controllerFilePath)) {
            throw new GenerateCrudException("File for controller '{$this->data->controllerFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateControllerClass(): void
    {
        $path = $this->data->controllerFilePath;
        $content = View::make('crudhub::crud.controller_class', $this->data->buildControllerData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkRoutesExist()
    {
        try {
            $content = $this->files->get(base_path('routes/web.php'));

            if (Str::contains($content, $this->data->routeNames)) {
                throw new GenerateCrudException("Routes for '{$this->data->modelName}' are already registered");
            }
        } catch (FileNotFoundException $e) {
            throw new GenerateCrudException("Could not find the 'routes/web.php' file");
        }
    }

    /**
     * @return void
     */
    protected function generateRoutes(): void
    {
        $path = base_path('routes/web.php');
        $content = View::make('crudhub::crud.routes_list', $this->data->buildRoutesData())->render();

        $this->files->append($path, "\n$content");
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkIndexViewExists()
    {
        if ($this->files->exists($this->data->indexViewFilePath)) {
            throw new GenerateCrudException("File for index view '{$this->data->indexViewFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateIndexView(): void
    {
        $path = $this->data->indexViewFilePath;
        $content = View::make('crudhub::crud.index_view', $this->data->buildIndexViewData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkCreateViewExists()
    {
        if ($this->files->exists($this->data->createViewFilePath)) {
            throw new GenerateCrudException("File for create view '{$this->data->createViewFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateCreateView(): void
    {
        $path = $this->data->createViewFilePath;
        $content = View::make('crudhub::crud.create_view', $this->data->buildCreateViewData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkEditViewExists()
    {
        if ($this->files->exists($this->data->editViewFilePath)) {
            throw new GenerateCrudException("File for edit view '{$this->data->editViewFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateEditView(): void
    {
        $path = $this->data->editViewFilePath;
        $content = View::make('crudhub::crud.edit_view', $this->data->buildEditViewData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkFilterViewExists()
    {
        if ($this->files->exists($this->data->filterViewFilePath)) {
            throw new GenerateCrudException("File for filter view '{$this->data->filterViewFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateFilterView(): void
    {
        $path = $this->data->filterViewFilePath;
        $content = View::make('crudhub::crud.filter_view', $this->data->buildFilterViewData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkFormViewExists()
    {
        if ($this->files->exists($this->data->formViewFilePath)) {
            throw new GenerateCrudException("File for form view '{$this->data->formViewFilePath}' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generateFormView(): void
    {
        $path = $this->data->formViewFilePath;
        $content = View::make('crudhub::crud.form_view', $this->data->buildFormViewData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     */
    protected function checkPermissionsMigrationExists()
    {
        $migrations = $this->files->glob(database_path("migrations/*_{$this->data->permissionsMigrationName}.php"));

        if (!empty($migrations)) {
            throw new GenerateCrudException("Migration for '[timestamp]_{$this->data->permissionsMigrationName}.php' already exists");
        }
    }

    /**
     * @return void
     */
    protected function generatePermissionsMigration(): void
    {
        $path = $this->data->permissionsMigrationFilePath;
        $content = View::make('crudhub::crud.permissions_migration', $this->data->buildPermissionsMigrationData())->render();

        $this->writeFileInDirectory(dirname($path), $path, $content);
    }

    /**
     * @throws GenerateCrudException
     * @return void
     */
    protected function checkMenuConfigExists(): void
    {
        if (!$this->files->exists($this->data->menuConfigPath)) {
            throw new GenerateCrudException("File for storing the menu '{$this->data->menuConfigPath}' doesn't exist");
        }
    }

    /**
     * @return void
     */
    protected function generateMenuButton(): void
    {
        try {
            $filePath = $this->data->menuConfigPath;
            $fileContent = $this->files->get($filePath);

            if (Str::contains($fileContent, "'heading' => 'Custom CRUDs'")) {
                $menuContent = View::make('crudhub::crud.menu_button', $this->data->buildMenuButtonData())->render();
                $fileContent = str_replace("            'heading' => 'Custom CRUDs',\n            'items' => [", "            {$menuContent}", $fileContent);

                $this->files->put($filePath, $fileContent);
            } elseif (Str::contains($fileContent, "'menu' => [")) {
                $menuContent = View::make('crudhub::crud.menu_button', $this->data->buildMenuButtonData())->render();
                $fileContent = str_replace("    'menu' => [", "    'menu' => [\n        [\n            {$menuContent}            ],\n        ],", $fileContent);

                $this->files->put($filePath, $fileContent);
            } else {
                throw new GenerateCrudException("Could not write contents for the menu button inside {$filePath}. Please do it manually.");
            }
        } catch (FileNotFoundException $e) {
            throw new GenerateCrudException("File for storing the menu '{$this->data->menuConfigPath}' doesn't exist");
        }
    }

    /**
     * @param string $directory
     * @param string $file
     * @param string $content
     * @return void
     */
    protected function writeFileInDirectory(string $directory, string $file, string $content = ''): void
    {
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($file, $content);
    }
}
