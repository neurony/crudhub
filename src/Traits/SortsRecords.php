<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Zbiller\Crudhub\Exceptions\SortException;
use Zbiller\Crudhub\Sorts\Sort;

trait SortsRecords
{
    /**
     * @var array
     */
    protected $sort = [
        /**
         * @var Builder
         */
        'query' => null,

        /**
         * @var array
         */
        'data' => null,

        /**
         * @var Sort
         */
        'instance' => null,

        /**
         * @var string
         */
        'field' => Sort::DEFAULT_SORT_FIELD,

        /**
         * @var string
         */
        'direction' => Sort::DEFAULT_DIRECTION_FIELD,

        /**
         * @var string[]
         */
        'defaults' => [],
    ];

    /**
     * @param Builder $query
     * @param array $data
     * @param ?Sort $sort
     * @throws SortException
     */
    public function scopeSorted(Builder $query, array $data, ?Sort $sort = null)
    {
        $this->sort['query'] = $query;
        $this->sort['data'] = $data;
        $this->sort['instance'] = $sort;

        $this->setFieldToSortBy();
        $this->setDirectionToSortIn();

        if ($this->isValidSort()) {
            $this->checkSortingDirection();

            switch ($this->sort['data'][$this->sort['direction']]) {
                case Sort::DIRECTION_RANDOM:
                    $this->sort['query']->inRandomOrder();
                    break;
                default:
                    if ($this->shouldSortByRelation()) {
                        $this->sortByRelation();
                    } else {
                        $this->sortNormally();
                    }
            }

            return;
        }

        $this->setDefaultSorting();

        if ($this->hasDefaultSort()) {
            $this->sortByDefaults();
        }
    }

    /**
     * @return bool
     */
    protected function isValidSort(): bool
    {
        return
            isset($this->sort['data'][$this->sort['field']]) &&
            isset($this->sort['data'][$this->sort['direction']]);
    }

    /**
     * @return bool
     */
    protected function hasDefaultSort(): bool
    {
        return !empty($this->sort['defaults']);
    }

    /**
     * @return void
     */
    protected function setFieldToSortBy(): void
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['field'] = $this->sort['instance']->field();
        }
    }

    /**
     * @return void
     */
    protected function setDirectionToSortIn(): void
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['direction'] = $this->sort['instance']->direction();
        }
    }

    /**
     * @return void
     */
    protected function setDefaultSorting(): void
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['defaults'] = $this->sort['instance']->defaults();
        }
    }

    /**
     * @return void
     */
    protected function sortNormally(): void
    {
        $this->sort['query']->orderBy(
            $this->sort['data'][$this->sort['field']],
            $this->sort['data'][$this->sort['direction']]
        );
    }

    /**
     * @return void
     */
    protected function sortByDefaults(): void
    {
        foreach ($this->sort['defaults'] as $default) {
            $this->sort['query']->orderBy($default['column'], $default['direction'] ?? 'asc');
        }
    }

    /**
     * @return void
     * @throws SortException
     */
    protected function sortByRelation(): void
    {
        $parts = explode('.', $this->sort['data'][$this->sort['field']]);
        $models = [];

        if (count($parts) > 2) {
            $field = array_pop($parts);
            $relations = $parts;
        } else {
            $field = Arr::last($parts);
            $relations = (array) Arr::first($parts);
        }

        foreach ($relations as $index => $relation) {
            $previousModel = $this;

            if (isset($models[$index - 1])) {
                $previousModel = $models[$index - 1];
            }

            $this->checkRelationToSortBy($previousModel, $relation);

            $rel = $previousModel->{$relation}();
            $models[] = $rel->getModel();

            $modelTable = $previousModel->getTable();
            $relationTable = $rel->getModel()->getTable();
            $modelKey = $rel->getForeignKeyName();
            $relationKey = $rel->getModel()->getKeyName();

            if ($this->alreadyJoinedForSorting($relationTable)) {
                continue;
            }

            switch (get_class($rel)) {
                case BelongsTo::class:
                    $this->sort['query']->join(
                        $relationTable, $modelTable.'.'.$modelKey, '=', $relationTable.'.'.$relationKey
                    );

                    break;
                case HasOne::class:
                    $this->sort['query']->join(
                        $relationTable, $modelTable.'.'.$relationKey, '=', $relationTable.'.'.$modelKey
                    );

                    break;
            }
        }

        $alias = implode('_', $relations).'_'.$field;

        if (isset($relationTable)) {
            $this->sort['query']->addSelect([
                $this->getTable().'.*',
                $relationTable.'.'.$field.' AS '.$alias,
            ]);
        }

        $this->sort['query']->orderBy(
            $alias, $this->sort['data'][$this->sort['direction']]
        );
    }

    /**
     * @return bool
     */
    protected function shouldSortByRelation(): bool
    {
        return Str::contains($this->sort['data'][$this->sort['field']], '.');
    }

    /**
     * @param string $table
     * @return bool
     */
    protected function alreadyJoinedForSorting(string $table): bool
    {
        return Str::contains(strtolower($this->sort['query']->toSql()), 'join `'.$table.'`');
    }

    /**
     * @return void
     * @throws SortException
     */
    protected function checkSortingDirection(): void
    {
        if (!in_array(strtolower($this->sort['data'][$this->sort['direction']]), array_map('strtolower', Sort::$directions))) {
            throw SortException::invalidDirectionSupplied($this->sort['data'][$this->sort['direction']]);
        }
    }

    /**
     * @param Model $model
     * @param string $relation
     * @return void
     * @throws SortException
     */
    protected function checkRelationToSortBy(Model $model, string $relation): void
    {
        $rel = $model->{$relation}();

        if (!($rel instanceof HasOne || $rel instanceof BelongsTo || $rel instanceof MorphOne)) {
            throw SortException::wrongRelationToSort($relation, get_class($rel));
        }
    }
}
