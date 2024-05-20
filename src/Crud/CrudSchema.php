<?php

namespace Zbiller\Crudhub\Crud;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * The class that is responsible for interacting with the DB Schema for fetching relevant data based on the table.
 */
class CrudSchema
{
    /**
     * @var string
     */
    public string $tableName;

    /**
     * @var Collection
     */
    public Collection $tableIndexes;

    /**
     * @var Collection
     */
    public Collection $tableForeignKeys;

    /**
     * @var Collection
     */
    public Collection $tableColumns;

    /**
     * @var Collection
     */
    public Collection $validTableColumns;

    /**
     * @var string[]
     */
    public array $ignoredTables = [
        'admin_password_resets',
        'admins',
        'failed_jobs',
        'blockables',
        'blocks',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'languages',
        'menus',
        'media',
        'media_unassigned',
        'migrations',
        'model_has_permissions',
        'model_has_roles',
        'pages',
        'password_resets',
        'password_reset_tokens',
        'permissions',
        'personal_access_tokens',
        'role_has_permissions',
        'roles',
        'sessions',
        'translations',
        'users',
    ];

    /**
     * @var string[]
     */
    public array $hiddenTableColumns = [
        'email_verified_at',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        //'ord',
        'ord_column',
        'order_column',
    ];

    /**
     * @return array
     */
    public function getTables(): array
    {
        try {
            return Collection::make(Schema::getTableListing())->filter(function ($table) {
                return !in_array($table, $this->ignoredTables);
            })->values()->all();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param string $table
     * @return bool
     */
    public function checkTableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    /**
     * @param string|null $table
     * @return Collection
     * @throws Exception
     */
    public function getTableIndexes(?string $table = null): Collection
    {
        return Collection::make(Schema::getIndexes($table ?: $this->tableName));
    }

    /**
     * @param string|null $table
     * @return Collection
     * @throws Exception
     */
    public function getTableForeignKeys(?string $table = null): Collection
    {
        return Collection::make(Schema::getForeignKeys($table ?: $this->tableName));
    }

    /**
     * @param string|null $table
     * @return Collection
     */
    public function getTableColumns(?string $table = null): Collection
    {
        $table = $table ?: $this->tableName;
        $columns = Schema::getColumnListing($table);

        return Collection::make($columns)->map(function ($column) use ($table) {
            return [
                'name' => $column,
                'type' => $this->getTableColumnType($table, $column),
                'required' => $this->isRequiredTableColumn($table, $column),
                'hidden' => $this->isHiddenTableColumn($column),
                'nullable' => $this->isColumnNullable($table, $column),
                'default' => $this->getColumnDefaultValue($table, $column),
                'translatable' => false,
            ];
        })->filter();
    }

    /**
     * @param string|null $table
     * @return Collection
     */
    public function getValidTableColumns(?string $table = null): Collection
    {
        return $this->getTableColumns($table ?: $this->tableName)->filter(function ($tableColumn) {
            return !($tableColumn['hidden'] == true || $tableColumn['name'] == 'id');
        });
    }

    /**
     * @param string $table
     * @param string $column
     * @return string|null
     */
    public function getColumnDefaultValue(string $table, string $column): ?string
    {
        $col = Arr::first(Schema::getColumns($table), function ($value) use ($column) {
            return $value['name'] == $column;
        });

        return $col['default'] ?? null;
    }

    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function isColumnNullable(string $table, string $column): bool
    {
        $col = Arr::first(Schema::getColumns($table), function ($value) use ($column) {
            return $value['name'] == $column;
        });

        return (bool)($col['nullable'] ?? false) === true;
    }

    /**
     * @param array $selectedColumns
     * @param Collection|null $availableColumns
     * @return bool
     */
    public function checkSelectedColumnsExist(array $selectedColumns, ?Collection $availableColumns = null)
    {
        $availableColumns = $availableColumns ?: $this->tableColumns;
        $columnsNotFound = Collection::make([]);

        Collection::make($selectedColumns)->map(function ($column) use (&$columnsNotFound, $availableColumns) {
            $columnFoundInDb = $availableColumns->where("name", $column)->count();

            if (!$columnFoundInDb) {
                $columnsNotFound->push($column);
            }
        });

        return $columnsNotFound->isEmpty();
    }

    /**
     * @param string $table
     * @param string $column
     * @return string
     */
    protected function getTableColumnType(string $table, string $column): string
    {
        return Schema::getColumnType($table, $column);
    }

    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function checkTableColumnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }

    /**
     * @param string $table
     * @param string $column
     * @return bool
     */
    protected function isRequiredTableColumn(string $table, string $column): bool
    {
        $isNullable = $this->isColumnNullable($table, $column);
        $hasDefault = $this->getColumnDefaultValue($table, $column) !== null;

        return !($isNullable || $hasDefault);
    }

    /**
     * @param string $column
     * @return bool
     */
    protected function isHiddenTableColumn(string $column): bool
    {
        return in_array($column, $this->hiddenTableColumns);
    }
}
