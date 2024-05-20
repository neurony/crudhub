<?php

namespace Zbiller\Crudhub\Filters;

use Zbiller\Crudhub\Contracts\UserFilterContract;

class UserFilter extends Filter implements UserFilterContract
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
