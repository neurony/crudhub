<?php

namespace Zbiller\Crudhub\Filters;

abstract class Filter
{
    /**
     * @const
     */
    const FIELD_ANY = '*';

    /**
     * @const
     */
    const CONDITION_AND = 'and';
    const CONDITION_OR = 'or';

    /**
     * @const
     */
    const OPERATOR_EQUAL = '=';
    const OPERATOR_NOT_EQUAL = '!=';
    const OPERATOR_SMALLER = '<';
    const OPERATOR_GREATER = '>';
    const OPERATOR_SMALLER_OR_EQUAL = '<=';
    const OPERATOR_GREATER_OR_EQUAL = '>=';
    const OPERATOR_NULL = 'null';
    const OPERATOR_NOT_NULL = 'not null';
    const OPERATOR_LIKE = 'like';
    const OPERATOR_LIKE_JSON = 'like json';
    const OPERATOR_NOT_LIKE = 'not like';
    const OPERATOR_IN = 'in';
    const OPERATOR_NOT_IN = 'not in';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_NOT_BETWEEN = 'not between';
    const OPERATOR_DATE = 'date';
    const OPERATOR_DATE_EQUAL = 'date =';
    const OPERATOR_DATE_NOT_EQUAL = 'date !=';
    const OPERATOR_DATE_SMALLER = 'date <';
    const OPERATOR_DATE_GREATER = 'date >';
    const OPERATOR_DATE_SMALLER_OR_EQUAL = 'date <=';
    const OPERATOR_DATE_GREATER_OR_EQUAL = 'date >=';

    /**
     * @var string[]
     */
    public static array $fields = [
        self::FIELD_ANY,
    ];

    /**
     * @var string[]
     */
    public static array $conditions = [
        self::CONDITION_AND,
        self::CONDITION_OR,
    ];

    /**
     * @var string[]
     */
    public static array $operators = [
        self::OPERATOR_EQUAL,
        self::OPERATOR_NOT_EQUAL,
        self::OPERATOR_SMALLER,
        self::OPERATOR_GREATER,
        self::OPERATOR_SMALLER_OR_EQUAL,
        self::OPERATOR_GREATER_OR_EQUAL,
        self::OPERATOR_NULL,
        self::OPERATOR_NOT_NULL,
        self::OPERATOR_IN,
        self::OPERATOR_NOT_IN,
        self::OPERATOR_LIKE,
        self::OPERATOR_LIKE_JSON,
        self::OPERATOR_NOT_LIKE,
        self::OPERATOR_BETWEEN,
        self::OPERATOR_NOT_BETWEEN,
        self::OPERATOR_DATE,
        self::OPERATOR_DATE_EQUAL,
        self::OPERATOR_DATE_NOT_EQUAL,
        self::OPERATOR_DATE_SMALLER,
        self::OPERATOR_DATE_GREATER,
        self::OPERATOR_DATE_SMALLER_OR_EQUAL,
        self::OPERATOR_DATE_GREATER_OR_EQUAL,
    ];

    /**
     * @return array
     */
    abstract public function filters(): array;

    /**
     * @return string
     */
    abstract public function morph(): string;

    /**
     * @return array
     */
    public function modifiers(): array
    {
        return [];
    }
}
