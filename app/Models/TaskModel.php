<?php

namespace App\Models;

use App\Enums\Day;
use App\Enums\TaskModelStatus;
use App\Enums\TaskStatus;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperTaskModel
 */
class TaskModel extends Model
{
    public function createTasks(CarbonPeriod $days): void
    {
        foreach ($days as $day) {
            if (($day->isMonday() && $this->days->contains(Day::Monday))
                || ($day->isTuesday() && $this->days->contains(Day::Tuesday))
                || ($day->isWednesday() && $this->days->contains(Day::Wednesday))
                || ($day->isThursday() && $this->days->contains(Day::Thursday))
                || ($day->isFriday() && $this->days->contains(Day::Friday))
                || ($day->isSaturday() && $this->days->contains(Day::Saturday))
                || ($day->isSunday() && $this->days->contains(Day::Sunday))) {
                $this->tasks()->create([
                    'user_id' => $this->user_id,
                    'status' => TaskStatus::Pending,
                    'due_at' => $day,
                ]);
            }
        }
    }

    /** @return HasMany<Task, $this> */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formatedDays(): Attribute
    {
        return Attribute::make(function () {
            if ($this->days->sort()->values()->toArray() === Day::all()->toArray()) {
                return __('All week');
            }

            if ($this->days->sort()->values()->toArray() === Day::weekdays()->toArray()) {
                return __('Weekdays');
            }

            if ($this->days->sort()->values()->toArray() === Day::weekend()->toArray()) {
                return __('Weekend');
            }

            return $this->days
                ->map(fn (Day $day): string => $day->shortLabel())
                ->join(', ');
        });
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'days' => AsEnumCollection::of(Day::class),
            'end_at' => 'datetime',
            'start_at' => 'datetime',
            'status' => TaskModelStatus::class,
        ];
    }
}
