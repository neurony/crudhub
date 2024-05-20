<?php

namespace Zbiller\Crudhub\Crud;

use Illuminate\Support\Collection;

/**
 * The class that is responsible for holding all user generated input.
 */
class CrudOptions
{
    /**
     * @var Collection
     */
    public Collection $tableFields;

    /**
     * @var Collection
     */
    public Collection $filterFields;

    /**
     * @var Collection
     */
    public Collection $formFields;

    /**
     * @var bool
     */
    public bool $withTranslatable = false;

    /**
     * @var Collection|null
     */
    public ?Collection $translatableFields = null;

    /**
     * @var bool
     */
    public bool $softDelete = false;

    /**
     * @var Collection|null
     */
    public ?Collection $mediaCollections = null;

    /**
     * @var Collection|null
     */
    public ?Collection $modelRelations = null;

    /**
     * @var bool
     */
    public bool $withReorderable = false;

    /**
     * @var string|null
     */
    public ?string $reorderableColumn = null;
}
