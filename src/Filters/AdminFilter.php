<?php

namespace Zbiller\Crudhub\Filters;

use Zbiller\Crudhub\Contracts\AdminFilterContract;

class AdminFilter extends Filter implements AdminFilterContract
{
    /**
     * @return string
     */
    public function morph(): string
    {
        return 'and';
    }

    /**
     * @return array
     */
    public function filters(): array
    {
        return [
            'keyword' => [
                'operator' => Filter::OPERATOR_LIKE,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'name,email',
            ],
            'active' => [
                'operator' => Filter::OPERATOR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'active',
            ],
            'roles' => [
                'operator' => Filter::OPERATOR_IN,
                'condition' => Filter::CONDITION_OR,
                'columns' => config('permission.table_names.roles', 'roles') . '.role_id',
            ],
            'start_date' => [
                'operator' => Filter::OPERATOR_DATE_GREATER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'created_at',
            ],
            'end_date' => [
                'operator' => Filter::OPERATOR_DATE_SMALLER_OR_EQUAL,
                'condition' => Filter::CONDITION_OR,
                'columns' => 'created_at',
            ],
        ];
    }
}
