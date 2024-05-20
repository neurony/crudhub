<?php

namespace Zbiller\Crudhub\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

trait ReordersRecords
{
    /**
     * @return void
     * @throws Exception
     */
    public static function bootReordersRecords()
    {
        static::creating(function ($model) {
            if ($model->shouldOrderWhenCreating()) {
                $model->setHighestOrderNumber();
            }
        });
    }

    /**
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->getOrderColumnName(), $direction);
    }

    /**
     * @return string
     */
    public function getOrderColumnName(): string
    {
        return 'ord';
    }

    /**
     * @return bool
     */
    public function shouldOrderWhenCreating(): bool
    {
        return true;
    }

    /**
     * @return void
     */
    public function setHighestOrderNumber(): void
    {
        $this->{$this->getOrderColumnName()} = $this->getHighestOrderNumber() + 1;
    }

    /**
     * @return int
     */
    public function getHighestOrderNumber(): int
    {
        return (int)$this->buildOrderQuery()->max($this->getOrderColumnName());
    }

    /**
     * @param array $ids
     * @param int $start
     * @throws InvalidArgumentException
     */
    public static function setNewOrder(array $ids, int $start = 0)
    {
        $model = app(static::class);

        foreach ($ids as $id) {
            static::withoutGlobalScopes()->where($model->getKeyName(), $id)->update([
                $model->getOrderColumnName() => ++$start
            ]);
        }
    }

    /**
     * @return $this
     */
    public function moveOrderUp(): self
    {
        $swap = $this->buildOrderQuery()
            ->orderBy($this->getOrderColumnName(), 'desc')
            ->where($this->getOrderColumnName(), '<', $this->{$this->getOrderColumnName()})
            ->first();

        if (!($swap instanceof Model && $swap->exists)) {
            return $this;
        }

        return $this->swapOrderWithModel($swap);
    }

    /**
     * @return $this
     */
    public function moveOrderDown(): self
    {
        $swap = $this->buildOrderQuery()
            ->orderBy($this->getOrderColumnName())
            ->where($this->getOrderColumnName(), '>', $this->{$this->getOrderColumnName()})
            ->first();

        if (!($swap instanceof Model && $swap->exists)) {
            return $this;
        }

        return $this->swapOrderWithModel($swap);
    }

    /**
     * @return $this
     */
    public function moveToStart(): self
    {
        $first = $this->buildOrderQuery()
            ->orderBy($this->getOrderColumnName())
            ->first();

        if (!($first instanceof Model && $first->exists)) {
            return $this;
        }

        if ($first->getKey() === $this->getKey()) {
            return $this;
        }

        $this->{$this->getOrderColumnName()} = $first->{$this->getOrderColumnName()};
        $this->save();

        $this->buildOrderQuery()
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->increment($this->getOrderColumnName());

        return $this;
    }

    /**
     * @return $this
     */
    public function moveToEnd(): self
    {
        $max = $this->getHighestOrderNumber();

        if ($this->{$this->getOrderColumnName()} === $max) {
            return $this;
        }

        $old = $this->{$this->getOrderColumnName()};

        $this->{$this->getOrderColumnName()} = $max;
        $this->save();

        $this->buildOrderQuery()
            ->where($this->getKeyName(), '!=', $this->getKey())
            ->where($this->getOrderColumnName(), '>', $old)
            ->decrement($this->getOrderColumnName());

        return $this;
    }

    /**
     * @param Model $model
     * @param Model $other
     * @return $this
     */
    public static function swapOrder(Model $model, Model $other): self
    {
        return $model->swapOrderWithModel($other);
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function swapOrderWithModel(Model $model)
    {
        $old = $model->{$this->getOrderColumnName()};

        $model->{$this->getOrderColumnName()} = $this->{$this->getOrderColumnName()};
        $model->save();

        $this->{$this->getOrderColumnName()} = $old;
        $this->save();

        return $this;
    }

    /**
     * @return Builder
     */
    public function buildOrderQuery(): Builder
    {
        return static::query();
    }
}
