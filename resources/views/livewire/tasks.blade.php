<?php

use App\Enums\TaskStatus;
use App\Models\Entry;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Flux\DateRange;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Session;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    #[Session]
    public ?DateRange $range = null;

    public string $name = '';

    public string $description = '';

    public ?CarbonImmutable $due_at = null;

    public ?string $comment = null;

    public ?Task $currentTask = null;

    public ?CarbonImmutable $reprogram_due_at = null;

    public string $speech_name = '';

    public string $speech_url = '';

    public function mount(): void
    {
        if (!$this->range instanceof \Flux\DateRange) {
            $this->range = new DateRange(
                today()->startOfWeek(),
                today()->endOfWeek(),
            );
        }
    }

    public function closeMotivationSpeechForm(): void
    {
        $this->reset('speech_name', 'speech_url');
    }

    public function closeTaskForm(): void
    {
        $this->reset('name', 'description', 'due_at');
    }

    public function closeReprogramTaskForm(): void
    {
        $this->reset('reprogram_due_at');
    }

    public function closeTaskCommentForm(): void
    {
        $this->reset('comment');
    }

    public function cancelTask(Task $task): void
    {
        $task->update(['status' => TaskStatus::Canceled]);

        Flux::toast(__('Task canceled successfully'), variant: 'success');
    }

    public function completeTask(Task $task): void
    {
        $task->update(['status' => TaskStatus::Completed]);

        Flux::toast(__('Task completed successfully'), variant: 'success');
    }

    public function deleteTask(Task $task): void
    {
        $task->delete();

        Flux::toast(__('Task deleted successfully'), variant: 'success');
    }

    public function editTaskComment(Task $task): void
    {
        $this->currentTask = $task;
        $this->comment = $task->comment;

        Flux::modal('task-comment-form')->show();
    }

    public function reprogramTask(Task $task): void
    {
        $this->currentTask = $task;
        $this->reprogram_due_at = $task->due_at;

        Flux::modal('task-due-at-form')->show();
    }

    public function retrieveTasks(): void
    {
        unset($this->days);
    }

    public function saveMotivationSpeech(): void
    {
        $this->validate([
            'speech_name' => ['required', 'string', 'max:255'],
            'speech_url' => ['required', 'string'],
        ]);

        $this->user->motivations()->create([
            'name' => $this->speech_name,
            'url' => $this->speech_url,
        ]);

        Flux::modal('motivation-speech-form')->close();

        Flux::toast(__('Motivation speech saved successfully'), variant: 'success');
    }

    public function saveTask(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['required', 'date'],
        ]);

        $this->user->tasks()->create([
            ...$validated,
            'status' => TaskStatus::Pending,
        ]);

        Flux::modal('create-task-form')->close();

        Flux::toast(__('Task saved successfully'), variant: 'success');
    }

    public function saveTaskComment(): void
    {
        $validated = $this->validate([
            'comment' => ['nullable', 'string'],
        ]);

        $this->currentTask->update($validated);

        Flux::modal('task-comment-form')->close();

        Flux::toast(__('Task comment saved successfully'), variant: 'success');
    }

    public function saveTaskDueAt(): void
    {
        $this->validate([
            'reprogram_due_at' => ['required', 'date'],
        ]);

        $this->currentTask->update([
            'due_at' => $this->reprogram_due_at,
        ]);

        Flux::modal('task-due-at-form')->close();

        Flux::toast(__('Task reprogrammed successfully'), variant: 'success');
    }

    public function startTask(Task $task): void
    {
        $task->update(['status' => TaskStatus::Started]);

        Flux::toast(__('Task started successfully'), variant: 'success');
    }

    #[Computed]
    public function days(): Collection
    {
        $days = collect();
        $period = CarbonPeriod::create(
            $this->range->start(),
            $this->range->end(),
        );

        foreach ($period as $day) {
            $days->push((object)[
                'heading' => $day->format('l j F Y'),
                'tasks' => $this->user->tasks()
                    ->with('model')
                    ->where('due_at', $day)
                    ->get(),
            ]);
        }

        return $days;
    }

    #[Computed]
    public function motivations(): Collection
    {
        return $this->user->motivations;
    }

    #[Computed]
    public function user(): User
    {
        return auth()->user();
    }
};

?>

<section class="mx-auto w-full max-w-7xl px-6 lg:px-8">
    <div class="flex justify-between items-center mb-8">
        <flux:date-picker
            wire:model="range"
            wire:change="retrieveTasks"
            mode="range"
            presets="today thisWeek lastWeek thisMonth lastMonth thisQuarter thisYear allTime"
            start-day="1"
            class="min-w-64"/>

        <div class="flex gap-2">
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ __('Motivation speech') }}
                </flux:button>

                <flux:menu>
                    @foreach ($this->motivations as $motivation)
                        <flux:menu.item :href="$motivation->url">
                            {{ $motivation->name }}
                        </flux:menu.item>
                    @endforeach

                    <flux:menu.separator/>

                    <flux:modal.trigger name="motivation-speech-form">
                        <flux:menu.item>
                            {{ __('New motivation speech') }}
                        </flux:menu.item>
                    </flux:modal.trigger>
                </flux:menu>
            </flux:dropdown>

            <flux:button :href="route('settings.task-models')">
                {{ __('Edit task models') }}
            </flux:button>

            <flux:modal.trigger name="create-task-form">
                <flux:button variant="primary">
                    {{ __('New standalone task') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <div class="w-full">
        @foreach ($this->days as $day)
            <div class="mb-8">
                <flux:heading level="2" size="lg" class="mb-4">
                    {{ $day->heading }}
                </flux:heading>

                <div class="flex flex-col gap-2">
                    @forelse ($day->tasks as $task)
                        <flux:card size="sm" class="grid grid-cols-4 gap-2 items-center">
                            <div>
                                <flux:heading level="3" size="lg">
                                    {{ $task->name ?? $task->model->name }}
                                </flux:heading>

                                <flux:tooltip :content="$task->description ?? $task->model->description">
                                    <flux:text variant="subtle" class="truncate">
                                        {{ $task->description ?? $task->model->description }}
                                    </flux:text>
                                </flux:tooltip>
                            </div>

                            <div>
                                <flux:badge
                                    :color="$task->status->color()"
                                    size="sm"
                                    inset="top bottom">
                                    {{ $task->status->label() }}
                                </flux:badge>
                            </div>

                            <div>
                                @if ($task->comment)
                                    <div class="flex items-center gap-1">
                                        <div class="truncate">
                                            <flux:tooltip :content="$task->comment">
                                                <flux:text variant="subtle">
                                                    {{ $task->comment }}
                                                </flux:text>
                                            </flux:tooltip>
                                        </div>

                                        <flux:button
                                            wire:click="editTaskComment({{ $task->id }})"
                                            :tooltip="__('Edit entry')"
                                            variant="ghost"
                                            icon="pencil"
                                            size="xs"/>
                                    </div>
                                @else
                                    <flux:button
                                        wire:click="editTaskComment({{ $task->id }})"
                                        variant="filled"
                                        size="sm">
                                        {{ __('Add comment') }}
                                    </flux:button>
                                @endif
                            </div>

                            <div class="text-end">
                                @if ($task->status === TaskStatus::Pending)
                                    <flux:button
                                        wire:click="startTask({{ $task->id }})"
                                        :tooltip="__('Start the task')"
                                        variant="ghost"
                                        icon="play"
                                        size="xs"/>
                                @endif

                                @if ($task->status === TaskStatus::Started)
                                    <flux:button
                                        wire:click="completeTask({{ $task->id }})"
                                        :tooltip="__('Complete the task')"
                                        variant="ghost"
                                        icon="check"
                                        size="xs"/>

                                    <flux:button
                                        wire:click="cancelTask({{ $task->id }})"
                                        :tooltip="__('Cancel the task')"
                                        variant="ghost"
                                        icon="x-mark"
                                        size="xs"/>
                                @endif

                                @if ($task->status !== TaskStatus::Completed && $task->status !== TaskStatus::Canceled)
                                    <flux:button
                                        wire:click="reprogramTask({{ $task->id }})"
                                        :tooltip="__('Reprogram the task')"
                                        variant="ghost"
                                        icon="calendar"
                                        size="xs"/>
                                @endif

                                <flux:button
                                    wire:click="deleteTask({{ $task->id }})"
                                    :tooltip="__('Delete the task')"
                                    variant="ghost"
                                    icon="trash"
                                    size="xs"/>
                            </div>
                        </flux:card>
                    @empty
                        <div class="flex justify-center p-4 bg-zinc-100 dark:bg-zinc-700 rounded-lg">
                            <flux:text variant="subtle">
                                {{ __('No task for that day') }}
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <flux:modal name="create-task-form" @close="closeTaskForm" class="w-6xl">
        <form wire:submit="saveTask" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Add new standalone task') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Create a task not related to recurrent models') }}
                </flux:text>
            </div>

            <flux:input
                wire:model="name"
                :label="__('Name')"
                required/>

            <flux:textarea
                wire:model="description"
                :label="__('Description')"/>

            <flux:date-picker
                wire:model="due_at"
                :label="__('Due date')"
                required
                with-today
                start-day="1"/>

            <div class="flex gap-2">
                <flux:spacer/>

                <flux:modal.close>
                    <flux:button variant="subtle">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="task-comment-form" @close="closeTaskCommentForm" class="w-6xl">
        <form wire:submit="saveTaskComment" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Edit task comment') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Write your progress during the task or every useful info') }}
                </flux:text>
            </div>

            <flux:textarea
                wire:model="comment"
                :label="__('Comment')"/>

            <div class="flex gap-2">
                <flux:spacer/>

                <flux:modal.close>
                    <flux:button variant="subtle">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="task-due-at-form" @close="closeReprogramTaskForm" class="w-6xl">
        <form wire:submit="saveTaskDueAt" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Update the task date') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Change the task date to reprogram it instead of cancel it') }}
                </flux:text>
            </div>

            <flux:date-picker
                wire:model="reprogram_due_at"
                :label="__('Due date')"
                required
                with-today
                start-day="1"/>

            <div class="flex gap-2">
                <flux:spacer/>

                <flux:modal.close>
                    <flux:button variant="subtle">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="motivation-speech-form" @close="closeMotivationSpeechForm" class="w-6xl">
        <form wire:submit="saveMotivationSpeech" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Add new motivation speech') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Add links to Youtube videos, podcats, etc.') }}
                </flux:text>
            </div>

            <flux:input
                wire:model="speech_name"
                :label="__('Name')"
                required/>

            <flux:input
                wire:model="speech_url"
                :label="__('Url')"
                type="url"
                required/>

            <div class="flex gap-2">
                <flux:spacer/>

                <flux:modal.close>
                    <flux:button variant="subtle">
                        {{ __('Cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="primary">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>
