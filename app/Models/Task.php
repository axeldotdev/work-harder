<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTask
 */
class Task extends Model
{
    /** @return BelongsTo<TaskModel, $this> */
    public function model(): BelongsTo
    {
        return $this->belongsTo(TaskModel::class, 'task_model_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'status' => TaskStatus::class,
        ];
    }
}
