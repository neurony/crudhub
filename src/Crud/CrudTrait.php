<?php

namespace Zbiller\Crudhub\Crud;

use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Zbiller\Crudhub\Exceptions\MakeCrudException;

/**
 * The class that encapsulates the interaction with the user for building a CRUD
 */
trait CrudTrait
{
    /**
     * @param string|null $tableName
     * @return void
     * @throws MakeCrudException
     */
    protected function setTableAndColumns(?string $tableName = null): void
    {
        if ($tableName) {
            $this->schema->tableName = $tableName;
        } else {
            $this->schema->tableName = $this->chooseTableName();
        }

        if (!$this->schema->checkTableExists($this->schema->tableName)) {
            throw new MakeCrudException("Selected table '{$this->schema->tableName}' does not exist");
        }

        if (in_array($this->schema->tableName, $this->schema->ignoredTables)) {
            throw new MakeCrudException("Can't create a CRUD for the '{$this->schema->tableName}' table because it's ignored");
        }

        $this->schema->tableColumns = $this->schema->getTableColumns();
        $this->schema->validTableColumns = $this->schema->getValidTableColumns();
        $this->schema->tableIndexes = $this->schema->getTableIndexes();
        $this->schema->tableForeignKeys = $this->schema->getTableForeignKeys();
    }

    /**
     * @return string
     * @throws MakeCrudException
     */
    protected function chooseTableName(): string
    {
        $tables = $this->schema->getTables();

        if (empty($tables)) {
            throw new MakeCrudException("You don't have any tables from which to can generate a CRUD.");
        }

        return $this->components->choice("Please choose the table you would like to generate CRUD for", $tables);
    }

    /**
     * @return void
     */
    protected function askForTableFields(): void
    {
        $availableChoices = $this->schema->tableColumns->filter(function ($tableColumn) {
            if ($tableColumn['hidden'] == true || $tableColumn['name'] == 'id') {
                return false;
            }

            if (in_array(strtolower($tableColumn['type']), ['text', 'longtext', 'tinytext', 'mediumtext'])) {
                return false;
            }

            return true;
        })->map->name->toArray();

        $selectedFields = $this->components->choice(
            question: "Which columns should be visible on the listing page's table?",
            choices: $availableChoices,
            default: implode(",", $availableChoices),
            multiple: true,
        );

        $selectedFields = array_intersect_key(
            $availableChoices,
            array_flip($selectedFields)
        );

        if (array_search("password", $selectedFields) !== false) {
            $selectedFields = array_merge(array_diff($selectedFields, ["password"]));
        }

        if (!$this->schema->checkSelectedColumnsExist($selectedFields)) {
            $this->components->error("Some provided fields couldn't be mapped to the table's columns");

            $this->askForTableFields();
        }

        $this->options->tableFields = $this->mapFieldsData($selectedFields);
    }

    /**
     * @return void
     */
    protected function askForFilterFields()
    {
        $availableChoices = $this->schema->tableColumns->filter(function ($tableColumn) {
            if ($tableColumn['hidden'] == true || $tableColumn['name'] == 'id') {
                return false;
            }

            if (in_array(strtolower($tableColumn['type']), ['string', 'varchar', 'text', 'json', 'longtext', 'text', 'tinytext', 'mediumtext'])) {
                return false;
            }

            return true;
        })->map->name->toArray();

        $selectedFields = $this->components->choice(
            question: "Which columns should be available as filters on the listing page?",
            choices: $availableChoices,
            default: implode(",", $availableChoices),
            multiple: true,
        );

        $selectedFields = array_intersect_key(
            $availableChoices,
            array_flip($selectedFields)
        );

        if (array_search("password", $selectedFields) !== false) {
            $selectedFields = array_merge(array_diff($selectedFields, ["password"]));
        }

        if (!$this->schema->checkSelectedColumnsExist($selectedFields)) {
            $this->components->error("Some provided fields couldn't be mapped to the table's columns");

            $this->askForFilterFields();
        }

        $this->options->filterFields = $this->mapFieldsData($selectedFields);
    }

    /**
     * @return void
     */
    protected function askForFormFields()
    {
        $availableChoices = $this->schema->tableColumns->filter(function ($tableColumn) {
            return !($tableColumn['hidden'] == true || $tableColumn['name'] == 'id');
        })->map->name->toArray();

        $selectedFields = $this->components->choice(
            question: "Which columns should be available on the form page?",
            choices: $availableChoices,
            default: implode(",", $availableChoices),
            multiple: true,
        );

        $selectedFields = array_intersect_key(
            $availableChoices,
            array_flip($selectedFields)
        );

        if (!$this->schema->checkSelectedColumnsExist($selectedFields)) {
            $this->components->error("Some provided fields couldn't be mapped to the table's columns");

            $this->askForFormFields();
        }

        $this->options->formFields = $this->mapFieldsData($selectedFields);
    }

    /**
     * @return void
     */
    protected function askForTranslatableFields()
    {
        if (!class_exists('\Zbiller\CrudhubLang\CrudhubLangServiceProvider')) {
            return;
        }

        $availableChoices = $this->schema->tableColumns->filter(function ($tableColumn) {
            return in_array(strtolower($tableColumn['type'] ?? ''), [
                'text', 'longtext', 'mediumtext', 'json',
            ]);
        })->map->name->toArray();

        $selectedFields = $this->components->choice(
            question: "Which columns should be translatable?",
            choices: ['none', ...$availableChoices],
            default: 'none',
            multiple: true,
        );

        if (in_array('none', $selectedFields) && count($selectedFields) == 1) {
            $this->options->withTranslatable = false;
            $this->options->translatableFields = Collection::make([]);
        } else {
            if (($key = array_search('none', $selectedFields)) !== false) {
                unset($selectedFields[$key]);

                $selectedFields = array_values($selectedFields);
            }

            if (!$this->schema->checkSelectedColumnsExist($selectedFields)) {
                $this->components->error("Some provided fields couldn't be mapped to the table's columns");

                $this->askForTranslatableFields();
            }

            $this->options->withTranslatable = count($selectedFields) > 0;
            $this->options->translatableFields = $this->mapFieldsData($selectedFields, true);

            if ($this->options->withTranslatable) {
                $this->options->tableFields->transform(function ($field) {
                    if (in_array($field['name'], array_column($this->options->translatableFields->toArray(), 'name'))) {
                        $field['translatable'] = true;
                    }

                    return $field;
                });

                $this->options->filterFields->transform(function ($field) {
                    if (in_array($field['name'], array_column($this->options->translatableFields->toArray(), 'name'))) {
                        $field['translatable'] = true;
                    }

                    return $field;
                });

                $this->options->formFields->transform(function ($field) {
                    if (in_array($field['name'], array_column($this->options->translatableFields->toArray(), 'name'))) {
                        $field['translatable'] = true;
                    }

                    return $field;
                });

                $this->schema->tableColumns->transform(function ($column) {
                    if (in_array($column['name'], array_column($this->options->translatableFields->toArray(), 'name'))) {
                        $column['translatable'] = true;
                    }

                    return $column;
                });

                $this->schema->validTableColumns->transform(function ($column) {
                    if (in_array($column['name'], array_column($this->options->translatableFields->toArray(), 'name'))) {
                        $column['translatable'] = true;
                    }

                    return $column;
                });
            }
        }
    }

    /**
     * @return void
     */
    protected function askForModelRelations()
    {
        $question = empty($this->options->modelRelations) ?
            'Do you want to add a relation?' :
            'Do you want to add another relation?';

        $answer = $this->components->choice(
            question: $question,
            choices: ['yes', 'no'],
            default: 'no'
        );

        if ($answer === 'no') {
            return;
        }

        $modelsNamespace = 'App\\Models\\';
        $modelClassName = Str::studly(Str::singular($this->schema->tableName));
        $modelRecords = Collection::make([]);
        $modelRelation = [];

        $intColumns = $this->schema->tableColumns
            ->whereIn('type', ['int', 'integer', 'bigint'])
            ->where('name', '!=', 'id')
            ->map(fn ($column) => $column['name']);

        Collection::make(File::allFiles(app_path('Models')))
            ->each(function ($file) use (&$modelRecords, $modelClassName, $modelsNamespace) {
                $relName = $file->getRelativePathName();
                $classFqn = $modelsNamespace . implode('\\', explode('/', substr($relName, 0, strrpos($relName, '.'))));
                $className = substr($classFqn, strlen($modelsNamespace));

                if ($className === $modelClassName || !class_exists($classFqn)) {
                    return;
                }

                $modelRecords->put($classFqn, $className);
            })->sort();

        $modelRelation['type'] = lcfirst($this->components->choice(
            question: "What is the type of the relation?",
            choices: ['belongsTo', 'belongsToMany'],
            default: 'belongsTo'
        ));

        $modelRelation['model_namespace'] = $this->components->choice(
            question: "What is the name of the related model?",
            choices: $modelRecords->all(),
        );

        $modelRelation['model_class'] = $modelRecords->get($modelRelation['model_namespace']);

        $modelRelation['name'] = $this->components->ask(
            question: "What should be the name of the relation defined on the model?",
            default: $modelRelation['type'] === 'belongsTo' ?
                Str::camel($modelRelation['model_class']) :
                Str::camel(Str::plural($modelRelation['model_class']))
        );

        $modelRelation['primary_key'] = class_exists($modelRelation['model_namespace']) ?
            App::make($modelRelation['model_namespace'])->getKeyName() : 'id';

        $modelRelation['table_related'] = class_exists($modelRelation['model_namespace']) ?
            App::make($modelRelation['model_namespace'])->getTable() :
            Str::snake(Str::plural($modelRelation['model_class']));

        switch ($modelRelation['type']) {
            case 'belongsTo':
                $modelRelation['table_name'] = $modelRelation['table_related'];

                $modelRelation['foreign_key'] = $this->components->choice(
                    question: "What is the foreign key column in {$modelClassName} model?",
                    choices: $intColumns->values()->all(),
                    default: $intColumns->first(function ($column) use ($modelRelation) {
                        return $column === Str::lower("{$modelRelation['model_class']}_{$modelRelation['primary_key']}");
                    }) ?? $intColumns->first(),
                );

                break;
            case 'belongsToMany':
                $modelRelation['table_name'] = $this->components->ask(
                    question: "What is the name of the pivot table?",
                    default: Str::lower(Arr::join(Arr::sort([Str::snake($modelClassName), Str::snake($modelRelation['model_class'])]), '_'))
                );

                $modelRelation['foreign_key'] = $this->components->ask(
                    question: "What is the foreign pivot key in '{$modelRelation['table_name']}' table?",
                    default: Str::lower(Str::snake($modelClassName)) . "_{$modelRelation['primary_key']}"
                );

                $modelRelation['owner_key'] = $this->components->ask(
                    question: "What is the related pivot key in '{$modelRelation['table_name']}' table?",
                    default: Str::lower(Str::snake($modelRelation['model_class'])) . "_{$modelRelation['primary_key']}"
                );

                break;
        }

        $relationTableColumns = $this->schema->getTableColumns($modelRelation['table_related'])
            ->where('hidden', false);

        $stringColumns = $relationTableColumns
            ->whereIn('type', ['varchar', 'string'])
            ->map(fn ($column) => $column['name']);

        $modelRelation['related_attribute'] = $this->components->choice(
            question: "Which attribute should represent the {$modelRelation['model_class']} model when working with the {$modelClassName} model? (typically 'name' or 'title')",
            choices: $stringColumns->values()->toArray(),
            default: $stringColumns->count() ? $stringColumns->values()->first() : null,
        );

        if (!$this->options->modelRelations) {
            $this->options->modelRelations = Collection::make([]);
        }

        $this->options->modelRelations->push($modelRelation);

        $this->askForModelRelations();
    }

    /**
     * @return void
     */
    protected function askForMediaCollections()
    {
        $this->options->mediaCollections = Collection::make([]);

        $mediaCollections = Collection::make(explode(',', $this->components->ask(
            "Write the names of media collections you want to register. Separate them by comma or leave empty if you don't want any."
        )))->map(function ($mediaCollection) {
            return [
                'name' => $mediaCollection,
                'is_single' => true,
                'is_image' => false,
            ];
        })->filter(function ($mediaCollection) {
            return !empty($mediaCollection['name'] ?? '');
        });

        if (!$mediaCollections->count()) {
            return;
        }

        $imageCollections = $this->components->choice(
            question: "Which of those media collections should be of type image?",
            choices: ['none', ...$mediaCollections->map->name->toArray()],
            multiple: true,
        );

        $multipleCollections = $this->components->choice(
            question: "Which of those media collections should support multiple files for the same record?",
            choices: ['none', ...$mediaCollections->map->name->toArray()],
            multiple: true,
        );

        $this->options->mediaCollections = $mediaCollections
            ->transform(function ($mediaCollection) use ($imageCollections, $multipleCollections) {
                if (in_array($mediaCollection['name'], $imageCollections)) {
                    $mediaCollection['is_image'] = true;
                }

                if (in_array($mediaCollection['name'], $multipleCollections)) {
                    $mediaCollection['is_single'] = false;
                }

                $mediaCollection['name'] = Str::plural($mediaCollection['name']);

                return $mediaCollection;
            });
    }

    /**
     * @return void
     * @throws MakeCrudException
     */
    protected function askForReorderableFeature()
    {
        $intColumns = $this->schema->tableColumns->filter(function ($column) {
            return in_array($column['type'], ['int', 'integer', 'bigint']) && !Str::contains($column['name'], 'id');
        })->map->name->values()->all();

        if (empty($intColumns)) {
            return;
        }

        $this->options->withReorderable = $this->components->choice(
            question: "Do you want to reorder records by drag & drop?",
            choices: ['yes', 'no'],
            default: 'no',
        ) == 'yes';

        if (!$this->options->withReorderable) {
            return;
        }

        $this->options->reorderableColumn = $this->components->choice(
            question: "In which column do you want to save the order?",
            choices: $intColumns,
        );

        $this->schema->validTableColumns = $this->schema->validTableColumns->filter(function ($column) {
            return $column['name'] != $this->options->reorderableColumn;
        });

        $this->options->tableFields = $this->options->tableFields->filter(function ($field) {
            return $field['name'] != $this->options->reorderableColumn;
        });

        $this->options->filterFields = $this->options->filterFields->filter(function ($field) {
            return $field['name'] != $this->options->reorderableColumn;
        });

        $this->options->formFields = $this->options->formFields->filter(function ($field) {
            return $field['name'] != $this->options->reorderableColumn;
        });
    }

    /**
     * @param array $fields
     * @param bool $translatable
     * @return Collection
     */
    protected function mapFieldsData(array $fields, bool $translatable = false): Collection
    {
        return Collection::make($fields)->map(function ($field) use ($translatable) {
            $data = $this->schema->tableColumns->where('name', $field)->first();

            return [
                'name' => $field,
                'label' => Str::of($field)->replace('_', ' ')->ucfirst()->toString(),
                'label_studly' => Str::studly($field),
                'type' => $data['type'],
                'required' => $data['required'],
                'translatable' => $translatable,
            ];
        });
    }

    /**
     * @return void
     */
    protected function finalSteps(): void
    {
        $this->callSilent(OptimizeClearCommand::class);

        $this->line('');
        $this->components->twoColumnDetail('<fg=green;options=bold>Successfully created the following classes:</>', '');

        foreach ($this->result['classes'] as $file) {
            $this->components->twoColumnDetail($file, '');
        }

        $this->line('');
        $this->components->twoColumnDetail('<fg=green;options=bold>Successfully created the following views:</>', '');

        foreach ($this->result['views'] as $file) {
            $this->components->twoColumnDetail($file, '');
        }

        $this->line('');
        $this->components->twoColumnDetail('<fg=green;options=bold>Successfully appended the routes to:</>', '');

        $this->components->twoColumnDetail($this->result['routes']['routes_file'], '');

        $this->line('');
        $this->components->twoColumnDetail('<fg=green;options=bold>Successfully created the permissions migration:</>', '');

        foreach ($this->result['migrations'] as $file) {
            $this->components->twoColumnDetail($file, '');
        }

        $migrate = $this->components->choice(
            question: "Do you want to execute the permission migration now?",
            choices: ['yes', 'no'],
            default: 'yes',
        );

        if ($migrate) {
            $this->call(MigrateCommand::class);
        }

        $this->line('');
        $this->components->twoColumnDetail('<fg=green;options=bold>ALL DONE !!!</>', '');
        $this->line('');
    }
}
