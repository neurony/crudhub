<?php

namespace Zbiller\Crudhub\Crud;

use Illuminate\Support\Str;

/**
 * The class that builds the necessary data to be used by the CrudGenerator
 */
class CrudData
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
     * @var string
     */
    public string $modelName;

    /**
     * @var string
     */
    public string $modelNamespace = 'App\Models';

    /**
     * @var string
     */
    public string $modelFilePath;

    /**
     * @var array
     */
    public array $modelFillable = [];

    /**
     * @var array
     */
    public array $modelCasts = [];

    /**
     * @var bool
     */
    public bool $modelUsesSoftDelete = false;

    /**
     * @var array
     */
    public array $modelRelations = [];

    /**
     * @var array
     */
    public array $modelTranslatable = [];

    /**
     * @var string
     */
    public string $resourceName;

    /**
     * @var string
     */
    public string $resourceFilePath;

    /**
     * @var string
     */
    public string $resourceNamespace = 'App\Http\Resources';

    /**
     * @var string
     */
    public string $formRequestName;

    /**
     * @var string
     */
    public string $formRequestFilePath;

    /**
     * @var string
     */
    public string $formRequestNamespace = 'App\Http\Requests';

    /**
     * @var array
     */
    public array $validationRules = [];

    /**
     * @var array
     */
    public array $translatableValidationRules = [];

    /**
     * @var array
     */
    public array $formFields = [];

    /**
     * @var array
     */
    public array $requestMerges = [];

    /**
     * @var string
     */
    public string $filterName;

    /**
     * @var string
     */
    public string $filterFilePath;

    /**
     * @var string
     */
    public string $filterNamespace = 'App\Http\Filters';

    /**
     * @var array
     */
    public array $filteringFields = [];

    /**
     * @var array
     */
    public array $filteringRules = [];

    /**
     * @var string
     */
    public string $sortName;

    /**
     * @var string
     */
    public string $sortFilePath;

    /**
     * @var string
     */
    public string $sortNamespace = 'App\Http\Sorts';

    /**
     * @var string
     */
    public string $controllerName;

    /**
     * @var string
     */
    public string $controllerFilePath;

    /**
     * @var string
     */
    public string $controllerNamespace = 'App\Http\Controllers\Admin';

    /**
     * @var array
     */
    public array $optionVariables = [
        'booleans' => [],
        'relations' => [],
    ];

    /**
     * @var array
     */
    public array $routeNames = [
        'index' => null,
        'create' => null,
        'store' => null,
        'edit' => null,
        'update' => null,
        'partial_update' => null,
        'destroy' => null,
        'force_destroy' => null,
        'bulk_destroy' => null,
        'restore' => null,
        'reorder' => null,
    ];

    /**
     * @var array
     */
    public array $adminPermissions = [
        'list' => null,
        'add' => null,
        'edit' => null,
        'delete' => null,
    ];

    /**
     * @var string
     */
    public string $permissionAdd;

    /**
     * @var string
     */
    public string $permissionEdit;

    /**
     * @var string
     */
    public string $permissionDelete;

    /**
     * @var string
     */
    public string $indexViewFilePath;

    /**
     * @var string
     */
    public string $createViewFilePath;

    /**
     * @var string
     */
    public string $editViewFilePath;

    /**
     * @var string
     */
    public string $filterViewFilePath;

    /**
     * @var string
     */
    public string $formViewFilePath;

    /**
     * @var string
     */
    public string $permissionsMigrationName;

    /**
     * @var string
     */
    public string $permissionsMigrationFilePath;

    /**
     * @var string
     */
    public string $menuConfigPath;

    /**
     * @param CrudSchema $schema
     * @param CrudOptions $options
     */
    public function __construct(CrudSchema $schema, CrudOptions $options)
    {
        $this->schema = $schema;
        $this->options = $options;
    }

    /**
     * @return void
     */
    public function initData()
    {
        $this->setModelName();
        $this->setModelFilePath();
        $this->setModelFillable();
        $this->setModelCasts();
        $this->setModelSoftDelete();
        $this->setModelRelations();
        $this->setModelTranslatable();

        $this->setResourceName();
        $this->setResourceFilePath();

        $this->setFormRequestName();
        $this->setFormRequestFilePath();
        $this->setValidationRules();
        $this->setTranslatableValidationRules();
        $this->setFormFields();
        $this->setRequestMerges();

        $this->setFilterName();
        $this->setFilterFilePath();
        $this->setFilteringFields();
        $this->setFilteringRules();

        $this->setSortName();
        $this->setSortFilePath();

        $this->setControllerName();
        $this->setControllerFilePath();
        $this->setOptionVariables();

        $this->setRouteNames();
        $this->setAdminPermissions();

        $this->setIndexViewFilePath();
        $this->setCreateViewFilePath();
        $this->setEditViewFilePath();
        $this->setFilterViewFilePath();
        $this->setFormViewFilePath();

        $this->setPermissionsMigrationName();
        $this->setPermissionsMigrationFilePath();

        $this->setMenuConfigPath();
    }

    /**
     * @return array
     */
    public function buildModelData(): array
    {
        return [
            'tableName' => $this->schema->tableName,
            'classNamespace' => $this->modelNamespace,
            'className' => $this->modelName,
            'fieldsToFill' => $this->modelFillable,
            'fieldsToCast' => $this->modelCasts,
            'withTranslatable' => $this->options->withTranslatable,
            'fieldsToTranslate' => $this->modelTranslatable,
            'usesSoftDelete' => $this->modelUsesSoftDelete,
            'definedRelations' => $this->modelRelations,
            'importRelations' => array_combine(array_column($this->modelRelations, 'relation_namespace'), $this->modelRelations),
            'mediaCollections' => $this->options->mediaCollections->toArray(),
            'withReorderable' => $this->options->withReorderable,
            'reorderableColumn' => $this->options->reorderableColumn,
        ];
    }

    /**
     * @return array
     */
    public function buildResourceData(): array
    {
        return [
            'classNamespace' => $this->resourceNamespace,
            'className' => $this->resourceName,
            'definedRelations' => $this->modelRelations,
            'mediaCollections' => $this->options->mediaCollections->toArray(),
        ];
    }

    /**
     * @return array
     */
    public function buildFormRequestData(): array
    {
        return [
            'classNamespace' => $this->formRequestNamespace,
            'className' => $this->formRequestName,
            'routeBinding' => lcfirst($this->modelName),
            'validationRules' => $this->validationRules,
            'translatableValidationRules' => $this->translatableValidationRules,
            'requestMerges' => $this->requestMerges,
            'withTranslatable' => $this->options->withTranslatable,
            'translatableFields' => $this->options->translatableFields,
        ];
    }

    /**
     * @return array
     */
    public function buildFilterData(): array
    {
        return [
            'classNamespace' => $this->filterNamespace,
            'className' => $this->filterName,
            'filteringRules' => $this->filteringRules,
        ];
    }

    /**
     * @return array
     */
    public function buildSortData(): array
    {
        return [
            'classNamespace' => $this->sortNamespace,
            'className' => $this->sortName,
        ];
    }

    /**
     * @return array
     */
    public function buildControllerData(): array
    {
        return [
            'classNamespace' => $this->controllerNamespace,
            'className' => $this->controllerName,
            'baseControllerFqn' => 'App\Http\Controllers\Controller',
            'baseControllerName' => 'Controller',
            'filterFqn' => $this->filterNamespace . '\\' . $this->filterName,
            'filterName' => $this->filterName,
            'resourceFqn' => $this->resourceNamespace . '\\' . $this->resourceName,
            'resourceName' => $this->resourceName,
            'formRequestFqn' => $this->formRequestNamespace . '\\' . $this->formRequestName,
            'formRequestName' => $this->formRequestName,
            'sortFqn' => $this->sortNamespace . '\\' . $this->sortName,
            'sortName' => $this->sortName,
            'modelFqn' => $this->modelNamespace . '\\' . $this->modelName,
            'modelName' => $this->modelName,
            'modelVariable' => "$" . lcfirst($this->modelName),
            'modelRelations' => $this->modelRelations,
            'optionVariables' => $this->optionVariables,
            'routeNames' => $this->routeNames,
            'indexView' => Str::plural($this->modelName) . '/Index',
            'createView' => Str::plural($this->modelName) . '/Create',
            'editView' => Str::plural($this->modelName) . '/Edit',
            'formFields' => $this->options->formFields,
            'withSoftDelete' => $this->modelUsesSoftDelete,
            'withReorderable' => $this->options->withReorderable,
        ];
    }

    /**
     * @return array
     */
    public function buildRoutesData(): array
    {
        return [
            'controllerFqn' => '\\' . $this->controllerNamespace . '\\' . $this->controllerName . '::class',
            'routeBinding' => '{' . lcfirst($this->modelName) . '}',
            'adminPermissions' => $this->adminPermissions,
            'routeNames' => $this->routeNames,
            'urlPrefix' => Str::lower(Str::replace(['_', '.'], '-', $this->schema->tableName)),
            'withSoftDelete' => $this->modelUsesSoftDelete,
            'withReorderable' => $this->options->withReorderable,
        ];
    }

    /**
     * @return array
     */
    public function buildIndexViewData(): array
    {
        return [
            'titleName' => Str::of($this->schema->tableName)->replace('_', ' ')->title()->toString(),
            'subtitleName' => Str::of($this->schema->tableName)->replace('_', ' ')->lower()->toString(),
            'routeNames' => $this->routeNames,
            'tableFields' => $this->options->tableFields,
            'withSoftDelete' => $this->modelUsesSoftDelete,
            'withTranslatable' => $this->options->withTranslatable,
            'withReorderable' => $this->options->withReorderable,
        ];
    }

    /**
     * @return array
     */
    public function buildCreateViewData(): array
    {
        return [
            'titleName' => Str::of($this->schema->tableName)->replace('_', ' ')->lower()->singular()->toString(),
            'formFields' => $this->formFields,
            'routeNames' => $this->routeNames,
            'mainField' => $this->schema->validTableColumns->whereIn('type', ['string', 'varchar', 'json', 'text'])->first() ?? null,
        ];
    }

    /**
     * @return array
     */
    public function buildEditViewData(): array
    {
        $updatedField = null;

        if ($column = $this->schema->tableColumns->where('name', 'updated_at')->first()) {
            $updatedField = $column['name'];
        } elseif ($column = $this->schema->tableColumns->where('name', 'created_at')->first()) {
            $updatedField = $column['name'];
        }

        return [
            'titleName' => Str::of($this->schema->tableName)->replace('_', ' ')->lower()->singular()->toString(),
            'formFields' => $this->formFields,
            'routeNames' => $this->routeNames,
            'updatedField' => $updatedField,
        ];
    }

    /**
     * @return array
     */
    public function buildFilterViewData(): array
    {
        return [
            'routeNames' => $this->routeNames,
            'filteringFields' => $this->filteringFields,
            'withSoftDelete' => $this->modelUsesSoftDelete,
        ];
    }

    /**
     * @return array
     */
    public function buildFormViewData(): array
    {
        return [
            'formFields' => $this->formFields,
            'mediaCollections' => $this->options->mediaCollections,
            'withTranslatable' => $this->options->withTranslatable,
        ];
    }

    /**
     * @return array
     */
    public function buildPermissionsMigrationData(): array
    {
        return [
            'adminPermissions' => $this->adminPermissions,
        ];
    }

    /**
     * @return array
     */
    public function buildMenuButtonData(): array
    {
        return [
            'menuName' => Str::of($this->schema->tableName)->replace('_', ' ')->title()->toString(),
            'menuRoute' => $this->routeNames['index'],
        ];
    }

    /**
     * @return void
     */
    protected function setModelName(): void
    {
        $this->modelName = Str::studly(Str::singular($this->schema->tableName));
    }

    /**
     * @return void
     */
    protected function setModelFilePath(): void
    {
        $this->modelFilePath = app_path("Models/{$this->modelName}.php");
    }

    /**
     * @return void
     */
    protected function setModelFillable(): void
    {
        $this->modelFillable = $this->schema->validTableColumns->map(function ($column) {
            return $column['name'];
        })->toArray() ?? [];
    }

    /**
     * @return void
     */
    protected function setModelCasts(): void
    {
        $this->modelCasts = $this->schema->validTableColumns->mapWithKeys(function ($column) {
            return match (strtolower($column['type'])) {
                'date' => [$column['name'] => 'date'],
                'datetime', 'timestamp' => [$column['name'] => 'datetime'],
                'boolean', 'tinyint' => [$column['name'] => 'boolean'],
                'integer', 'int' => [$column['name'] => 'integer'],
                'float', 'double' => [$column['name'] => 'float'],
                default => ['' => ''],
            };
        })->filter()->toArray() ?? [];
    }

    /**
     * @return void
     */
    protected function setModelSoftDelete(): void
    {
        $this->modelUsesSoftDelete = $this->schema->tableColumns->contains('name', 'deleted_at');
    }

    /**
     * @return void
     */
    protected function setModelRelations(): void
    {
        if (!$this->options->modelRelations || empty($this->options->modelRelations)) {
            return;
        }

        $this->options->modelRelations->each(function ($modelRelation, $index) {
            $relation = $modelRelation;

            switch ($modelRelation['type']) {
                case 'belongsTo':
                    $relation['relation_namespace'] = 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo';
                    $relation['relation_class'] = 'BelongsTo';
                    break;
                case 'belongsToMany':
                    $relation['relation_namespace'] = 'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany';
                    $relation['relation_class'] = 'BelongsToMany';
                    break;
            }

            $this->modelRelations[] = $relation;
        });
    }

    /**
     * @return void
     */
    protected function setModelTranslatable(): void
    {
        $this->modelTranslatable = $this->options->translatableFields->map(function ($field) {
            return $field['name'];
        })->toArray() ?? [];
    }

    /**
     * @return void
     */
    protected function setResourceName(): void
    {
        $this->resourceName = Str::studly(Str::singular($this->schema->tableName)) . 'Resource';
    }

    /**
     * @return void
     */
    protected function setResourceFilePath(): void
    {
        $this->resourceFilePath = app_path("Http/Resources/{$this->resourceName}.php");
    }

    /**
     * @return void
     */
    protected function setFormRequestName(): void
    {
        $this->formRequestName = Str::studly(Str::singular($this->schema->tableName)) . 'Request';
    }

    /**
     * @return void
     */
    protected function setFormRequestFilePath(): void
    {
        $this->formRequestFilePath = app_path("Http/Requests/{$this->formRequestName}.php");
    }

    /**
     * @return void
     */
    public function setValidationRules(): void
    {
        $this->options->formFields->each(function ($field) {
            if ((bool)($field['translatable'] ?? false) === true) {
                return;
            }

            $column = $this->schema->validTableColumns->where('name', $field['name'] ?? null)->first();

            if (!$column) {
                return;
            }

            if ($column['required'] === true) {
                $this->validationRules[$field['name']]['rules'][] = 'required';
            }

            if ($column['default'] !== null) {
                $this->validationRules[$field['name']]['rules'][] = 'sometimes';
            }

            if ($column['nullable'] === true) {
                $this->validationRules[$field['name']]['rules'][] = 'nullable';
            }

            if (Str::contains(strtolower($column['name']), 'mail')) {
                $this->validationRules[$field['name']]['rules'][] = 'email';
            }

            switch (strtolower($column['type'])) {
                case 'string':
                case 'varchar':
                case 'text':
                    $this->validationRules[$field['name']]['rules'][] = 'string';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'double':
                    $this->validationRules[$field['name']]['rules'][] = 'numeric';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $this->validationRules[$field['name']]['rules'][] = 'date';
                    break;
                case 'boolean':
                case 'tinyint':
                    $this->validationRules[$field['name']]['rules'][] = 'boolean';
                    break;
            }

            $this->schema->tableIndexes->filter(function ($index) use ($field, $column) {
                return Str::containsAll($index['name'], [$column['name'], 'unique']);
            })->each(function ($index) use ($field, $column) {
                $indexColumns = $index['columns'] ?? [];

                if (!in_array($field['name'], $indexColumns)) {
                    return;
                }

                $mainColumn = $field['name'];
                $secondaryColumns = array_values(array_diff($indexColumns, [$mainColumn]));

                if (!in_array($mainColumn, array_keys($this->validationRules))) {
                    return;
                }

                $this->validationRules[$field['name']]['rules'][] = 'unique';
                $this->validationRules[$field['name']]['unique_table'] = $this->schema->tableName;
                $this->validationRules[$field['name']]['unique_column'] = $mainColumn;
                $this->validationRules[$field['name']]['unique_related_columns'] = $secondaryColumns;
            });

            $this->schema->tableForeignKeys->filter(function ($foreign) use ($field, $column) {
                return Str::containsAll($foreign['name'], [$column['name'], 'foreign']);
            })->each(function ($foreign) use ($field, $column) {
                foreach ($foreign['columns'] as $column) {
                    if (!in_array($column, array_keys($this->validationRules))) {
                        continue;
                    }

                    $this->validationRules[$field['name']]['rules'][] = 'exists';
                    $this->validationRules[$field['name']]['exists_table'] = $foreign['foreign_table'];
                    $this->validationRules[$field['name']]['exists_column'] = $foreign['foreign_columns'][0] ?? 'id';
                }
            });
        });
    }

    /**
     * @return void
     */
    public function setTranslatableValidationRules(): void
    {
        $this->options->formFields->each(function ($field) {
            if ((bool)($field['translatable'] ?? false) === false) {
                return;
            }

            $column = $this->schema->validTableColumns->where('name', $field['name'] ?? null)->first();

            if (!$column) {
                return;
            }

            if ($column['required'] === true) {
                $this->translatableValidationRules[$field['name']]['rules'][] = 'required';
            }

            if ($column['default'] !== null) {
                $this->translatableValidationRules[$field['name']]['rules'][] = 'sometimes';
            }

            if ($column['nullable'] === true) {
                $this->translatableValidationRules[$field['name']]['rules'][] = 'nullable';
            }

            if (Str::contains(strtolower($column['name']), 'mail')) {
                $this->translatableValidationRules[$field['name']]['rules'][] = 'email';
            }

            switch (strtolower($column['type'])) {
                case 'string':
                case 'varchar':
                case 'text':
                    $this->translatableValidationRules[$field['name']]['rules'][] = 'string';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'double':
                    $this->translatableValidationRules[$field['name']]['rules'][] = 'numeric';
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $this->translatableValidationRules[$field['name']]['rules'][] = 'date';
                    break;
                case 'boolean':
                case 'tinyint':
                    $this->translatableValidationRules[$field['name']]['rules'][] = 'boolean';
                    break;
            }
        });
    }

    /**
     * @return void
     */
    public function setFormFields(): void
    {
        $this->options->formFields->each(function ($field) {
            $column = $this->schema->validTableColumns->where('name', $field['name'] ?? null)->first();

            if (!$column) {
                return;
            }

            $this->formFields[$field['name']]['label'] = Str::of($field['name'])->snake()->replace(['_', 'id'], ' ')->title()->toString();
            $this->formFields[$field['name']]['translatable'] = (bool)($field['translatable'] ?? false);

            switch (strtolower($column['type'])) {
                case 'string':
                case 'varchar':
                case 'json':
                    $this->formFields[$field['name']]['input_type'] = 'text';
                    $this->formFields[$field['name']]['data_type'] = 'string';
                    break;
                case 'text':
                case 'longtext':
                    $this->formFields[$field['name']]['input_type'] = 'textarea';
                    $this->formFields[$field['name']]['data_type'] = 'text';
                    break;
                case 'date':
                    $this->formFields[$field['name']]['input_type'] = 'date';
                    $this->formFields[$field['name']]['data_type'] = 'date';
                    break;
                case 'datetime':
                case 'timestamp':
                    $this->formFields[$field['name']]['input_type'] = 'datetime';
                    $this->formFields[$field['name']]['data_type'] = 'datetime';
                    break;
                case 'boolean':
                case 'tinyint':
                case 'enum':
                    $this->formFields[$field['name']]['input_type'] = 'select';
                    $this->formFields[$field['name']]['data_type'] = 'boolean';
                    break;
                case 'int':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'double':
                    $this->formFields[$field['name']]['input_type'] = 'number';
                    $this->formFields[$field['name']]['data_type'] = 'number';
                    break;
                default:
                    $this->formFields[$field['name']]['input_type'] = 'text';
                    $this->formFields[$field['name']]['data_type'] = $column['type'];
            }

            if ((bool)($field['translatable'] ?? false) === true) {
                $this->formFields[$field['name']]['input_type'] = 'text';
            }

            foreach ($this->modelRelations as $relation) {
                if ($relation['type'] != 'belongsTo') {
                    continue;
                }

                if ($relation['foreign_key'] !== $column['name']) {
                    continue;
                }

                $this->formFields[$field['name']]['label'] = Str::of($relation['name'])->snake()->replace(['_', 'id'], ' ')->title()->toString();
                $this->formFields[$field['name']]['relation_name'] = $relation['name'];
                $this->formFields[$field['name']]['relation_type'] = $relation['type'];
                $this->formFields[$field['name']]['relation_primary_key'] = $relation['primary_key'];
                $this->formFields[$field['name']]['relation_foreign_key'] = $relation['foreign_key'];
                $this->formFields[$field['name']]['input_type'] = 'select';
                $this->formFields[$field['name']]['input_options'] = Str::of($relation['name'])->snake()->plural()->toString();
            }
        });

        foreach ($this->modelRelations as $relation) {
            if ($relation['type'] != 'belongsToMany') {
                continue;
            }

            $this->formFields[$relation['name']]['label'] = Str::of($relation['name'])->snake()->replace(['_', 'id'], ' ')->title()->toString();
            $this->formFields[$relation['name']]['data_type'] = 'array';
            $this->formFields[$relation['name']]['relation_name'] = $relation['name'];
            $this->formFields[$relation['name']]['relation_type'] = $relation['type'];
            $this->formFields[$relation['name']]['relation_primary_key'] = $relation['primary_key'];
            $this->formFields[$relation['name']]['relation_foreign_key'] = $relation['foreign_key'];
            $this->formFields[$relation['name']]['input_type'] = 'multiselect';
            $this->formFields[$relation['name']]['input_options'] = Str::of($relation['name'])->snake()->plural()->toString();
        }

        $this->options->mediaCollections->each(function ($collection) {
            $this->formFields[$collection['name']]['label'] = Str::of($collection['name'])->snake()->replace(['_', 'id'], ' ')->title()->toString();
            $this->formFields[$collection['name']]['data_type'] = $collection['is_image'] ? 'image' : 'file';
            $this->formFields[$collection['name']]['input_type'] = 'media';
            $this->formFields[$collection['name']]['single_media'] = $collection['is_single'];
            $this->formFields[$collection['name']]['image_media'] = $collection['is_image'];
        });
    }

    /**
     * @return void
     */
    protected function setRequestMerges(): void
    {
        $fields = array_keys(array_filter($this->validationRules, function($item) {
            return in_array("boolean", $item['rules']);
        }));

        foreach ($fields as $field) {
            $this->requestMerges[] = [
                'type' => 'boolean',
                'field' => $field,
                'name' => Str::studly($field),
            ];
        }
    }

    /**
     * @return void
     */
    protected function setFilterName(): void
    {
        $this->filterName = Str::studly(Str::singular($this->schema->tableName)) . 'Filter';
    }

    /**
     * @return void
     */
    protected function setFilterFilePath(): void
    {
        $this->filterFilePath = app_path("Http/Filters/{$this->filterName}.php");
    }

    /**
     * @return void
     */
    public function setFilteringRules(): void
    {
        $this->filteringRules['keyword']['label'] = 'Keyword';
        $this->filteringRules['keyword']['operator'] = 'Filter::OPERATOR_LIKE';
        $this->filteringRules['keyword']['operator_symbol'] = 'like';
        $this->filteringRules['keyword']['condition'] = 'Filter::CONDITION_OR';
        $this->filteringRules['keyword']['condition_symbol'] = 'or';
        $this->filteringRules['keyword']['columns'] = [];

        $this->schema->validTableColumns->filter(function ($column) {
            if (in_array(strtolower($column['type']), ['string', 'varchar'])) {
                return true;
            }

            if ((bool)($column['translatable'] ?? false) === true) {
                return true;
            }

            return false;
        })->each(function ($column) {
            $this->filteringRules['keyword']['columns'][] = $column['name'];
        });

        $this->options->filterFields->each(function ($column) {
            $snakeCase = Str::snake($column['name']);

            if (in_array(strtolower($column['type']), ['int', 'integer', 'bigint'])) {
                $foreignKey = $this->schema->tableForeignKeys->filter(function ($foreign) use ($column) {
                    return Str::containsAll($foreign['name'], [$column['name'], 'foreign']);
                })->first();

                if (!empty($foreign['name'])) {
                    foreach ($foreignKey['columns'] as $localColumn) {
                        if ($column['name'] != $localColumn) {
                            continue;
                        }

                        $this->filteringRules[$snakeCase] = [
                            'label' => Str::title(Str::replace('_', ' ', Str::before($column['name'], '_id'))),
                            'operator' => 'Filter::OPERATOR_IN',
                            'operator_symbol' => '=',
                            'condition' => 'Filter::CONDITION_OR',
                            'condition_symbol' => 'or',
                            'columns' => [$column['name']],
                        ];

                        break;
                    }

                    return;
                }
            }

            switch (strtolower($column['type'])) {
                case 'int':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'double':
                    $this->filteringRules["min_$snakeCase"] = [
                        'label' => 'Min ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'operator' => 'Filter::OPERATOR_GREATER_OR_EQUAL',
                        'operator_symbol' => '>=',
                        'condition' => 'Filter::CONDITION_OR',
                        'condition_symbol' => 'or',
                        'columns' => [$column['name']],
                    ];

                    $this->filteringRules["max_$snakeCase"] = [
                        'label' => 'Max ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'operator' => 'Filter::OPERATOR_SMALLER_OR_EQUAL',
                        'operator_symbol' => '<=',
                        'condition' => 'Filter::CONDITION_OR',
                        'condition_symbol' => 'or',
                        'columns' => [$column['name']],
                    ];

                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $this->filteringRules["min_$snakeCase"] = [
                        'label' => 'Min ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'operator' => 'Filter::OPERATOR_DATE_GREATER_OR_EQUAL',
                        'operator_symbol' => 'date >=',
                        'condition' => 'Filter::CONDITION_OR',
                        'condition_symbol' => 'or',
                        'columns' => [$column['name']],
                    ];

                    $this->filteringRules["max_$snakeCase"] = [
                        'label' => 'Max ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'operator' => 'Filter::OPERATOR_DATE_SMALLER_OR_EQUAL',
                        'operator_symbol' => 'date <=',
                        'condition' => 'Filter::CONDITION_OR',
                        'condition_symbol' => 'or',
                        'columns' => [$column['name']],
                    ];

                    break;
                case 'boolean':
                case 'tinyint':
                    $this->filteringRules[$snakeCase] = [
                        'label' => Str::title(Str::replace('_', ' ', $column['name'])),
                        'operator' => 'Filter::OPERATOR_EQUAL',
                        'operator_symbol' => '=',
                        'condition' => 'Filter::CONDITION_OR',
                        'condition_symbol' => 'or',
                        'columns' => [$column['name']],
                    ];

                    break;
            }
        });

        $date = null;

        if ($this->schema->tableColumns->where('name', 'created_at')->first()) {
            $date = 'created_at';
        } elseif ($this->schema->tableColumns->where('name', 'updated_at')->first()) {
            $date = 'updated_at';
        }

        if ($date) {
            $this->filteringRules['start_date'] = [
                'label' => 'Start date',
                'operator' => 'Filter::OPERATOR_DATE_GREATER_OR_EQUAL',
                'operator_symbol' => 'date >=',
                'condition' => 'Filter::CONDITION_OR',
                'condition_symbol' => 'or',
                'columns' => [$date],
            ];

            $this->filteringRules['end_date'] = [
                'label' => 'End date',
                'operator' => 'Filter::OPERATOR_DATE_SMALLER_OR_EQUAL',
                'operator_symbol' => 'date <=',
                'condition' => 'Filter::CONDITION_OR',
                'condition_symbol' => 'or',
                'columns' => [$date],
            ];
        }
    }

    /**
     * @return void
     */
    public function setFilteringFields(): void
    {
        $this->filteringFields['keyword']['label'] = 'Keyword';
        $this->filteringFields['keyword']['type'] = 'text';
        $this->filteringFields['keyword']['user_provided'] = false;

        $this->options->filterFields->each(function ($column) {
            $snakeName = Str::snake($column['name']);

            if (in_array(strtolower($column['type']), ['int', 'integer', 'bigint'])) {
                $foreignKey = $this->schema->tableForeignKeys->filter(function ($foreign) use ($column) {
                    return Str::containsAll($foreign['name'], [$column['name'], 'foreign']);
                })->first();

                if (!empty($foreignKey['name'])) {
                    foreach ($foreignKey['columns'] as $localColumn) {
                        if ($column['name'] != $localColumn) {
                            continue;
                        }

                        $this->filteringFields[$snakeName] = [
                            'label' => Str::title(Str::replace('_', ' ', Str::before($column['name'], '_id'))),
                            'type' => 'select',
                            'user_provided' => true,
                        ];

                        if ($this->options->modelRelations && count($this->options->modelRelations)) {
                            $relation = $this->options->modelRelations->filter(function ($relation) use ($column) {
                                return $relation['foreign_key'] == $column['name'];
                            })->first();

                            if (!empty($relation['name'])) {
                                $this->filteringFields[$snakeName]['select_type'] = 'multiple';
                                $this->filteringFields[$snakeName]['select_options'] = Str::of($relation['name'])->snake()->plural()->toString();
                            }
                        }

                        break;
                    }

                    return;
                }
            }

            switch (strtolower($column['type'])) {
                case 'int':
                case 'integer':
                case 'bigint':
                case 'float':
                case 'double':
                    $this->filteringFields["min_$snakeName"] = [
                        'label' => 'Min ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'type' => 'number',
                        'user_provided' => true,
                    ];

                    $this->filteringFields["max_$snakeName"] = [
                        'label' => 'Max ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'type' => 'number',
                        'user_provided' => true,
                    ];

                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $this->filteringFields["min_$snakeName"] = [
                        'label' => 'Min ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'type' => 'date',
                        'user_provided' => true,
                    ];

                    $this->filteringFields["max_$snakeName"] = [
                        'label' => 'Max ' . Str::lower(Str::replace('_', ' ', $column['name'])),
                        'type' => 'date',
                        'user_provided' => true,
                    ];

                    break;
                case 'boolean':
                case 'tinyint':
                    $this->filteringFields[$snakeName] = [
                        'label' => Str::title(Str::replace('_', ' ', $column['name'])),
                        'type' => 'boolean',
                        'user_provided' => true,
                    ];

                    break;
            }
        });

        $date = null;

        if ($this->schema->tableColumns->where('name', 'created_at')->first()) {
            $date = 'created_at';
        } elseif ($this->schema->tableColumns->where('name', 'updated_at')->first()) {
            $date = 'updated_at';
        }

        if ($date) {
            $this->filteringFields['start_date'] = [
                'label' => 'Start date',
                'type' => 'date',
                'user_provided' => true,
            ];

            $this->filteringFields['end_date'] = [
                'label' => 'End date',
                'type' => 'date',
                'user_provided' => true,
            ];
        }
    }

    /**
     * @return void
     */
    protected function setSortName(): void
    {
        $this->sortName = Str::studly(Str::singular($this->schema->tableName)) . 'Sort';
    }

    /**
     * @return void
     */
    protected function setSortFilePath(): void
    {
        $this->sortFilePath = app_path("Http/Sorts/{$this->sortName}.php");
    }

    /**
     * @return void
     */
    protected function setControllerName(): void
    {
        $this->controllerName = Str::studly(Str::singular($this->schema->tableName)) . 'Controller';
    }

    /**
     * @return void
     */
    protected function setControllerFilePath(): void
    {
        $this->controllerFilePath = app_path("Http/Controllers/Admin/{$this->controllerName}.php");
    }

    /**
     * @return void
     */
    protected function setOptionVariables(): void
    {
        if (!empty($this->options->modelRelations)) {
            $this->optionVariables['relations'] = $this->options->modelRelations->map(function ($relation) {
                $relation['field_name'] = Str::of($relation['name'])->snake()->plural()->toString();
                $relation['method_name'] = 'get' . Str::studly(Str::singular($relation['name'])) . 'Options';

                return $relation;
            })->values()->all();
        }
    }

    /**
     * @return void
     */
    protected function setRouteNames(): void
    {
        $prefix = trim(Str::lower(Str::replace(['-', '.'], '_', $this->schema->tableName)));

        $this->routeNames = [
            'index' => "admin.$prefix.index",
            'create' => "admin.$prefix.create",
            'store' => "admin.$prefix.store",
            'edit' => "admin.$prefix.edit",
            'update' => "admin.$prefix.update",
            'partial_update' => "admin.$prefix.partial_update",
            'destroy' => "admin.$prefix.destroy",
            'force_destroy' => "admin.$prefix.force_destroy",
            'bulk_destroy' => "admin.$prefix.bulk_destroy",
            'restore' => "admin.$prefix.restore",
            'reorder' => "admin.$prefix.reorder",
        ];
    }

    /**
     * @return void
     */
    protected function setAdminPermissions(): void
    {
        $prefix = trim(Str::lower(Str::replace('_', '-', $this->schema->tableName)));

        $this->adminPermissions = [
            'list' => "$prefix-list",
            'add' => "$prefix-add",
            'edit' => "$prefix-edit",
            'delete' => "$prefix-delete",
        ];
    }

    /**
     * @return void
     */
    protected function setIndexViewFilePath(): void
    {
        $this->indexViewFilePath = resource_path("js/crudhub/Pages/" . Str::plural($this->modelName) . "/Index.vue");
    }

    /**
     * @return void
     */
    protected function setCreateViewFilePath(): void
    {
        $this->createViewFilePath = resource_path("js/crudhub/Pages/" . Str::plural($this->modelName) . "/Create.vue");
    }

    /**
     * @return void
     */
    protected function setEditViewFilePath(): void
    {
        $this->editViewFilePath = resource_path("js/crudhub/Pages/" . Str::plural($this->modelName) . "/Edit.vue");
    }

    /**
     * @return void
     */
    protected function setFilterViewFilePath(): void
    {
        $this->filterViewFilePath = resource_path("js/crudhub/Pages/" . Str::plural($this->modelName) . "/Filter.vue");
    }

    /**
     * @return void
     */
    protected function setFormViewFilePath(): void
    {
        $this->formViewFilePath = resource_path("js/crudhub/Pages/" . Str::plural($this->modelName) . "/Form.vue");
    }

    /**
     * @return void
     */
    protected function setPermissionsMigrationName(): void
    {
        $this->permissionsMigrationName = "create_{$this->schema->tableName}_permissions";
    }

    /**
     * @return void
     */
    protected function setPermissionsMigrationFilePath(): void
    {
        $timestamp = date('Y_m_d_His', time());

        $this->permissionsMigrationFilePath = database_path("migrations/{$timestamp}_{$this->permissionsMigrationName}.php");
    }

    /**
     * @return void
     */
    protected function setMenuConfigPath(): void
    {
        $this->menuConfigPath = config_path('crudhub/menu.php');
    }
}
