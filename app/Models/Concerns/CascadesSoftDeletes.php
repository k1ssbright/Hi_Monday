<?php

namespace App\Models\Concerns;

trait CascadesSoftDeletes
{
    public static function bootCascadesSoftDeletes(): void
    {
        static::deleting(function (self $model): void {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }

            foreach ($model->cascadeSoftDeleteRelations() as $relation) {
                $model->{$relation}()->each(function ($child): void {
                    $child->delete();
                });
            }
        });
    }

    /**
     * @return list<string>
     */
    protected function cascadeSoftDeleteRelations(): array
    {
        return $this->cascadeSoftDeletes ?? [];
    }
}
