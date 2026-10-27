<?php

namespace App\Models\Concerns;

use App\Models\Employee\EmployeeBranch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Soft delete that also records which employee_branch performed the delete.
 */
trait SoftDeletesWithActor
{
    use SoftDeletes;

    public static function bootSoftDeletesWithActor(): void
    {
        static::restoring(function (self $model): void {
            $model->deleted_by = null;
        });
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(EmployeeBranch::class, 'deleted_by');
    }

    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();
        $deletedBy = function_exists('currentEmployeeBranchId')
            ? currentEmployeeBranchId()
            : null;

        $columns = [
            $this->getDeletedAtColumn() => $this->fromDateTime($time),
            'deleted_by' => $deletedBy,
        ];

        $this->{$this->getDeletedAtColumn()} = $time;
        $this->deleted_by = $deletedBy;

        if ($this->usesTimestamps() && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;
            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));

        $this->fireModelEvent('trashed', false);
    }
}
