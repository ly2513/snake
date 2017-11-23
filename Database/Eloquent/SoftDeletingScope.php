<?php

namespace Snake\Database\Eloquent;

class SoftDeletingScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['Restore', 'WithTrashed', 'WithoutTrashed', 'OnlyTrashed'];

    // 新增的关于deleted_at 值的定义
    const DELETED_NORMAL = 0;
    const DELETED_DEL    = 1;

    /**
     * 获取正常数据
     *
     * @param Builder $builder
     * @param Model   $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $model = $builder->getModel();
        $builder->where($model->getQualifiedDeletedAtColumn(), '=', self::DELETED_NORMAL);
        $this->extend($builder);
    }

    /**
     * 只获取软删除数据
     *
     * @param Builder $builder
     */
    public function addOnlyTrashed(Builder $builder)
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $model = $builder->getModel();
            $builder->withoutGlobalScope($this)->where($model->getQualifiedDeletedAtColumn(), '=', self::DELETED_DEL);

            return $builder;
        });
    }

    /**
     * 恢复被删除的数据
     *
     * @param Builder $builder
     */
    public function addRestore(Builder $builder)
    {
        $builder->macro('restore', function (Builder $builder) {
            $builder->withTrashed();

            return $builder->update([$builder->getModel()->getDeletedAtColumn() => self::DELETED_NORMAL]);
        });
    }

    /**
     * 软删除 delete
     *
     * @param Builder $builder
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
        $builder->onDelete(function (Builder $builder) {
            $column = $this->getDeletedAtColumn($builder);

            return $builder->update([
                $column => self::DELETED_DEL,
            ]);
        });
    }


    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @param  \Snake\Database\Eloquent\Model  $model
     * @return void
     */
//    public function apply(Builder $builder, Model $model)
//    {
//        $builder->whereNull($model->getQualifiedDeletedAtColumn());
//    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return void
     */
//    public function extend(Builder $builder)
//    {
//        foreach ($this->extensions as $extension) {
//            $this->{"add{$extension}"}($builder);
//        }
//
//        $builder->onDelete(function (Builder $builder) {
//            $column = $this->getDeletedAtColumn($builder);
//
//            return $builder->update([
//                $column => $builder->getModel()->freshTimestampString(),
//            ]);
//        });
//    }

    /**
     * Get the "deleted at" column for the builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getDeletedAtColumn(Builder $builder)
    {
        if (count((array) $builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedDeletedAtColumn();
        }

        return $builder->getModel()->getDeletedAtColumn();
    }

    /**
     * Add the restore extension to the builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return void
     */
//    protected function addRestore(Builder $builder)
//    {
//        $builder->macro('restore', function (Builder $builder) {
//            $builder->withTrashed();
//
//            return $builder->update([$builder->getModel()->getDeletedAtColumn() => null]);
//        });
//    }

    /**
     * Add the with-trashed extension to the builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithTrashed(Builder $builder)
    {
        $builder->macro('withTrashed', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-trashed extension to the builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutTrashed(Builder $builder)
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedDeletedAtColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-trashed extension to the builder.
     *
     * @param  \Snake\Database\Eloquent\Builder  $builder
     * @return void
     */
//    protected function addOnlyTrashed(Builder $builder)
//    {
//        $builder->macro('onlyTrashed', function (Builder $builder) {
//            $model = $builder->getModel();
//
//            $builder->withoutGlobalScope($this)->whereNotNull(
//                $model->getQualifiedDeletedAtColumn()
//            );
//
//            return $builder;
//        });
//    }
}
