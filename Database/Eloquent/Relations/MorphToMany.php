<?php

namespace Snake\Database\Eloquent\Relations;

use Snake\Support\Arr;
use Snake\Database\Eloquent\Model;
use Snake\Database\Eloquent\Builder;

class MorphToMany extends BelongsToMany
{
    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The class name of the morph type constraint.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Indicates if we are connecting the inverse of the relation.
     *
     * This primarily affects the morphClass constraint.
     *
     * @var bool
     */
    protected $inverse;

    /**
     * Create a new morph to many relationship instance.
     *
     * @param  \Snake\Database\Eloquent\Builder  $query
     * @param  \Snake\Database\Eloquent\Model  $parent
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relationName
     * @param  bool  $inverse
     * @return void
     */
    public function __construct(Builder $query, Model $parent, $name, $table, $foreignPivotKey,
                                $relatedPivotKey, $parentKey, $relatedKey, $relationName = null, $inverse = false)
    {
        $this->inverse = $inverse;
        $this->morphType = $name.'_type';
        $this->morphClass = $inverse ? $query->getModel()->getMorphClass() : $parent->getMorphClass();

        parent::__construct(
            $query, $parent, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $relationName
        );
    }

    /**
     * Set the where clause for the relation query.
     *
     * @return $this
     */
    protected function addWhereConstraints()
    {
        parent::addWhereConstraints();

        $this->query->where($this->table.'.'.$this->morphType, $this->morphClass);

        return $this;
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array  $models
     * @return void
     */
    public function addEagerConstraints(array $models)
    {
        parent::addEagerConstraints($models);

        $this->query->where($this->table.'.'.$this->morphType, $this->morphClass);
    }

    /**
     * Create a new pivot attachment record.
     *
     * @param  int   $id
     * @param  bool  $timed
     * @return array
     */
    protected function baseAttachRecord($id, $timed)
    {
        return Arr::add(
            parent::baseAttachRecord($id, $timed), $this->morphType, $this->morphClass
        );
    }

    /**
     * Add the constraints for a relationship count query.
     *
     * @param  \Snake\Database\Eloquent\Builder  $query
     * @param  \Snake\Database\Eloquent\Builder  $parentQuery
     * @param  array|mixed  $columns
     * @return \Snake\Database\Eloquent\Builder
     */
    public function getRelationExistenceQuery(Builder $query, Builder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)->where(
            $this->table.'.'.$this->morphType, $this->morphClass
        );
    }

    /**
     * Create a new query builder for the pivot table.
     *
     * @return \Snake\Database\Query\Builder
     */
    protected function newPivotQuery()
    {
        return parent::newPivotQuery()->where($this->morphType, $this->morphClass);
    }

    /**
     * Create a new pivot model instance.
     *
     * @param  array  $attributes
     * @param  bool   $exists
     * @return \Snake\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(array $attributes = [], $exists = false)
    {
        $using = $this->using;

        $pivot = $using ? $using::fromRawAttributes($this->parent, $attributes, $this->table, $exists)
                        : MorphPivot::fromAttributes($this->parent, $attributes, $this->table, $exists);

        $pivot->setPivotKeys($this->foreignPivotKey, $this->relatedPivotKey)
              ->setMorphType($this->morphType)
              ->setMorphClass($this->morphClass);

        return $pivot;
    }

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getMorphType()
    {
        return $this->morphType;
    }

    /**
     * Get the class name of the parent model.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return $this->morphClass;
    }
}
