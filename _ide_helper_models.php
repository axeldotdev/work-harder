<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property mixed $name
 * @property mixed $content
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Entry whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEntry {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property \App\Enums\MealType $type
 * @property string $name
 * @property string $description
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meal whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMeal {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $url
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Motivation whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMotivation {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $task_model_id
 * @property \App\Enums\TaskStatus $status
 * @property \Carbon\CarbonImmutable $due_at
 * @property string|null $comment
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property string|null $name
 * @property string|null $description
 * @property-read \App\Models\TaskModel|null $model
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDueAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTaskModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Collection<int, \App\Enums\Day> $days
 * @property \App\Enums\TaskModelStatus $status
 * @property \Carbon\CarbonImmutable $start_at
 * @property \Carbon\CarbonImmutable|null $end_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $formated_days
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TaskModel whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTaskModel {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Entry> $entries
 * @property-read int|null $entries_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Meal> $meals
 * @property-read int|null $meals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Motivation> $motivations
 * @property-read int|null $motivations_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskModel> $taskModels
 * @property-read int|null $task_models_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

