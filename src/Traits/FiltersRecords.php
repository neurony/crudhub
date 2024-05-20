<?php

namespace Zbiller\Crudhub\Traits;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Zbiller\Crudhub\Exceptions\FilterException;
use Zbiller\Crudhub\Filters\Filter;

trait FiltersRecords
{
    protected $filter = [
        /**
         * @var Builder
         */
        'query' => null,

        /**
         * @var Filter
         */
        'instance' => null,

        /**
         * @var array
         */
        'data' => [],

        /**
         * @var
         */
        'morph' => null,

        /**
         * @var
         */
        'method' => null,

        /**
         * @var
         */
        'having' => null,

        /**
         * @var
         */
        'field' => null,

        /**
         * @var
         */
        'value' => null,

        /**
         * @var
         */
        'operator' => null,

        /**
         * @var
         */
        'condition' => null,

        /**
         * @var
         */
        'columns' => null,
    ];

    /**
     * @param Builder $query
     * @param array $data
     * @param Filter $filter
     * @throws FilterException
     */
    public function scopeFiltered(Builder $query, array $data, Filter $filter)
    {
        $this->filter['query'] = $query;
        $this->filter['data'] = $data;
        $this->filter['instance'] = $filter;

        foreach ($this->filter['instance']->filters() as $field => $options) {
            $this->filter['field'] = $field;

            if (!$this->isValidFilter()) {
                continue;
            }

            $this->setOperatorForFiltering($options);
            $this->setConditionToFilterBy($options);
            $this->setColumnsToFilterIn($options);
            $this->setMethodsOfFiltering();
            $this->setValueToFilterBy();

            $this->checkOperatorForFiltering();
            $this->checkConditionToFilterBy();
            $this->checkColumnsToFilterIn();

            $this->morph()->filter();
        }
    }

    /**
     * @return $this
     */
    protected function morph(): static
    {
        $this->filter['morph'] = 'where';

        if (strtolower($this->filter['instance']->morph()) == 'or') {
            $this->filter['morph'] = 'or' . ucwords($this->filter['morph']);
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function filter(): void
    {
        $this->filter['query']->{$this->filter['morph']}(function ($query) {
            foreach (explode(',', trim($this->filter['columns'], ',')) as $column) {
                if ($this->shouldFilterByRelation($column)) {
                    $this->filterByRelation($query, $column);
                } else {
                    $this->filterNormally($query, $column);
                }
            }
        });
    }

    /**
     * @param Builder $query
     * @param string $column
     * @return void
     */
    protected function filterByRelation(Builder $query, string $column): void
    {
        $options = [];
        $relation = Str::camel(explode('.', $column)[0]);
        $options[$relation][] = explode('.', $column)[1];

        foreach ($options as $relation => $columns) {
            try {
                $query->{$this->filter['having']}($relation, function (Builder $q) use ($columns) {
                    foreach ($columns as $index => $column) {
                        $method = $index == 0 ? lcfirst(str_replace('or', '', $this->filter['method'])) : $this->filter['method'];

                        $this->filterIndividually($q, $method, $column);
                    }
                });
            } catch (BadMethodCallException $e) {
                $this->filterIndividually($query, $this->filter['method'], $column);
            }
        }
    }

    /**
     * @param Builder $query
     * @param string $column
     * @return void
     */
    protected function filterNormally(Builder $query, string $column): void
    {
        $this->filterIndividually($query, $this->filter['method'], $column);
    }

    /**
     * @param Builder $query
     * @param string $method
     * @param string $column
     * @return void
     */
    protected function filterIndividually(Builder $query, string $method, string $column)
    {
        if ($this->filter['operator'] == Filter::OPERATOR_LIKE_JSON) {
            $method = $this->filter['condition'] == Filter::CONDITION_OR ? 'orWhereRaw' : 'whereRaw';

            $query->{$method}("LOWER({$column}) like ?", strtolower($this->filter['value']));

            return;
        }

        switch ($_method = strtolower($method)) {
            case Str::contains($_method, Filter::OPERATOR_NULL):
                $query->{$method}($column);
                break;
            case Str::contains($_method, Filter::OPERATOR_IN):
            case Str::contains($_method, Filter::OPERATOR_BETWEEN):
                $query->{$method}($column, $this->filter['value']);
                break;
            case Str::contains($_method, Filter::OPERATOR_DATE):
                $operator = explode(' ', $this->filter['operator']);
                $query->{$method}($column, ($operator[1] ?? '='), $this->filter['value']);
                break;
            default:
                $query->{$method}($column, $this->filter['operator'], $this->filter['value']);
                break;
        }
    }

    /**
     * @return bool
     */
    protected function isValidFilter(): bool
    {
        return $this->isValidFilterField() && !$this->isNullFilterField();
    }

    /**
     * @return bool
     */
    protected function isValidFilterField(): bool
    {
        return isset($this->filter['data'][$this->filter['field']]) || in_array($this->filter['field'], Filter::$fields);
    }

    /**
     * @return bool
     */
    protected function isNullFilterField(): bool
    {
        if (is_array($this->filter['data'][$this->filter['field']])) {
            $count = 0;

            foreach ($this->filter['data'][$this->filter['field']] as $value) {
                if ($value === null) {
                    $count++;
                }
            }

            return $count == count($this->filter['data'][$this->filter['field']]);
        }

        return is_null($this->filter['data'][$this->filter['field']]);
    }

    /**
     * @param string $column
     * @return bool
     */
    protected function shouldFilterByRelation(string $column): bool
    {
        return Str::contains($column, '.');
    }

    /**
     * @return void
     */
    protected function setMethodsOfFiltering(): void
    {
        $this->filter['method'] = 'where';
        $this->filter['having'] = 'whereHas';

        if ($this->filter['condition'] == Filter::CONDITION_OR) {
            $this->filter['method'] = 'or' . ucwords($this->filter['method']);
            $this->filter['having'] = 'or' . ucwords($this->filter['having']);
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case Str::contains($operator, 'null'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Null';
                break;
            case Str::contains($operator, 'in'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'In';
                break;
            case Str::contains($operator, 'between'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Between';
                break;
            case Str::contains($operator, 'date'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Date';
                break;
            case Str::contains($operator, 'json'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = 'whereRaw';
                break;
        }
    }

    /**
     * @return void
     */
    protected function attemptToBuildNotMethod(): void
    {
        if (Str::contains(strtolower($this->filter['operator']), 'not')) {
            $this->filter['method'] = $this->filter['method'] . 'Not';
        }
    }

    /**
     * @return void
     */
    protected function setValueToFilterBy(): void
    {
        if (
            method_exists($this->filter['instance'], 'modifiers') &&
            array_key_exists($this->filter['field'], $this->filter['instance']->modifiers())
        ) {
            foreach ($this->filter['instance']->modifiers() as $field => $value) {
                if ($field == $this->filter['field']) {
                    $this->filter['value'] = $value instanceof Closure ? $value(null) : $value;
                    break;
                }
            }
        } else {
            $this->filter['value'] = $this->filter['data'][$this->filter['field']];
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case Str::contains($operator, Filter::OPERATOR_LIKE):
                $this->filter['value'] = "%" . strtolower($this->filter['value']) . "%";
                break;
            case Str::contains($operator, Filter::OPERATOR_IN):
                $this->filter['value'] = (array)$this->filter['value'];
                break;
            case Str::contains($operator, Filter::OPERATOR_BETWEEN):
                if (!isset($this->filter['value'][0])) {
                    $this->filter['value'][0] = 0;
                }

                if (!isset($this->filter['value'][1])) {
                    $this->filter['value'][1] = 0;
                }

                if (
                    isset($this->filter['value'][0]) && $this->filter['value'][0] > 0 &&
                    isset($this->filter['value'][1]) && $this->filter['value'][1] == 0
                ) {
                    $this->filter['value'][1] = 999 * 999 * 999 * 999 * 999;
                }

                $this->filter['value'] = (array)$this->filter['value'];
                break;
        }
    }

    /**
     * @param array $options
     * @return void
     */
    protected function setOperatorForFiltering(array $options): void
    {
        $this->filter['operator'] = $options['operator'] ?? null;
    }

    /**
     * @param array $options
     * @return void
     */
    protected function setConditionToFilterBy(array $options): void
    {
        $this->filter['condition'] = $options['condition'] ?? null;
    }

    /**
     * @param array $options
     * @return void
     */
    protected function setColumnsToFilterIn(array $options): void
    {
        $this->filter['columns'] = $options['columns'] ?? null;
    }

    /**
     * @return void
     * @throws FilterException
     */
    protected function checkOperatorForFiltering(): void
    {
        if (
            !isset($this->filter['operator']) ||
            !in_array(strtolower($this->filter['operator']), array_map('strtolower', Filter::$operators))
        ) {
            throw FilterException::noOperatorSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }

    /**
     * @return void
     * @throws FilterException
     */
    protected function checkConditionToFilterBy(): void
    {
        if (
            !isset($this->filter['condition']) ||
            !in_array(strtolower($this->filter['condition']), array_map('strtolower', Filter::$conditions))
        ) {
            throw FilterException::noConditionSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }

    /**
     * @return void
     * @throws FilterException
     */
    protected function checkColumnsToFilterIn(): void
    {
        if (!isset($this->filter['columns']) || empty($this->filter['columns'])) {
            throw FilterException::noColumnsSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }
}
