<?php

use App\Enums\Day;
use App\Enums\TaskModelStatus;
use App\Events\TaskModelCreated;
use App\Events\TaskModelDeleted;
use App\Events\TaskModelStopped;
use App\Models\TaskModel;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    public ?string $description = null;

    public CarbonImmutable $start_at;

    public ?CarbonImmutable $end_at = null;

    public array $days = [];

    public ?TaskModel $currentTaskModel = null;

    public string $editName = '';

    public ?string $editDescription = null;

    public function closeTaskModelForm(): void
    {
        $this->reset('editName', 'editDescription');
    }

    public function deleteTaskModel(TaskModel $taskModel): void
    {
        $taskModel->tasks()->delete();
        $taskModel->delete();
    }

    public function editTaskModel(TaskModel $taskModel): void
    {
        $this->currentTaskModel = $taskModel;
        $this->editName = $taskModel->name;
        $this->editDescription = $taskModel->description;

        Flux::modal('task-model-form')->show();
    }

    public function updateTaskModel(): void
    {
        $this->validate([
            'editName' => ['required', 'string', 'max:255'],
            'editDescription' => ['nullable', 'string'],
        ]);

        $this->currentTaskModel->update([
            'name' => $this->editName,
            'description' => $this->editDescription,
        ]);

        Flux::modal('task-model-form')->close();

        Flux::toast('Task model updated successfully', variant: 'success');
    }

    public function createTaskModel(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date'],
            'days' => ['required', 'array'],
            'days.*' => ['required', Rule::enum(Day::class)],
        ]);

        $taskModel = $this->user->taskModels()->create([
            ...$validated,
            'status' => $this->start_at->isAfter(today())
                ? TaskModelStatus::Pending
                : TaskModelStatus::Started,
        ]);

        if ($taskModel->end_at && !$taskModel->end_at->isAfter(today()->addDays(10))) {
            $endDate = $taskModel->end_at;
        } else {
            $endDate = today()->addDays(10);
        }

        $days = CarbonPeriod::create($taskModel->start_at, $endDate);

        $taskModel->createTasks($days);

        $this->reset();

        Flux::toast('Task model created successfully', variant: 'success');
    }

    public function stopTaskModel(TaskModel $taskModel): void
    {
        tap($taskModel)->update(['status' => TaskModelStatus::Completed]);

        $taskModel->tasks()
            ->where('due_at', '>', today())
            ->delete();
    }

    #[Computed]
    public function daysEnum(): array
    {
        return Day::cases();
    }

    #[Computed]
    public function taskModels(): Collection
    {
        return $this->user->taskModels;
    }

    #[Computed]
    public function user(): User
    {
        return auth()->user();
    }
};

?>

<section class="mx-auto w-full max-w-7xl px-6 lg:px-8">
    @include('partials.settings-heading')

    <x-settings.layout
        :heading="__('Manage your task models')"
        :subheading="__('Manage your task models and their planning')">
        <form wire:submit="createTaskModel" class="mt-6 space-y-6 max-w-lg">
            <flux:input
                wire:model="name"
                :label="__('Name')"
                required/>

            <flux:field>
                <flux:label :badge="__('Optional')">
                    {{ __('Description') }}
                </flux:label>

                <flux:textarea wire:model="description"/>
            </flux:field>

            <flux:date-picker
                wire:model="start_at"
                :label="__('Start date')"
                required
                with-today
                start-day="1"/>

            <flux:field>
                <flux:label :badge="__('Optional')">
                    {{ __('End date') }}
                </flux:label>

                <flux:date-picker
                    wire:model="end_at"
                    with-today
                    start-day="1"/>
            </flux:field>

            <flux:checkbox.group
                wire:model="days"
                :label="__('Days')"
                variant="pills">
                @foreach ($this->daysEnum as $day)
                    <flux:checkbox
                        :value="$day->value"
                        :label="$day->label()"/>
                @endforeach
            </flux:checkbox.group>

            <flux:button variant="primary" type="submit">
                {{ __('Save') }}
            </flux:button>
        </form>

        <flux:table class="mt-10">
            <flux:table.columns>
                <flux:table.column>
                    {{ __('Name') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('Status') }}
                </flux:table.column>

                <flux:table.column>
                    {{ __('Period') }}
                </flux:table.column>

                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->taskModels as $taskModel)
                    <flux:table.row>
                        <flux:table.cell>
                            <flux:text variant="strong">
                                {{ $taskModel->name }}
                            </flux:text>

                            <flux:text variant="subtle" class="truncate">
                                {{ $taskModel->description }}
                            </flux:text>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge
                                :color="$taskModel->status->color()"
                                size="sm"
                                inset="top bottom">
                                {{ $taskModel->status->label() }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:text variant="strong">
                                {{ $taskModel->start_at->format('d/m/Y') }} -
                                {{ $taskModel->end_at?->format('d/m/Y') ?? __('Undefined') }}
                            </flux:text>

                            <flux:text variant="subtle">
                                {{ $taskModel->formatedDays }}
                            </flux:text>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:button
                                wire:click="editTaskModel({{ $taskModel->id }})"
                                :tooltip="__('Edit the model name and description')"
                                variant="ghost"
                                icon="pencil"
                                size="xs"/>

                            <flux:button
                                wire:click="stopTaskModel({{ $taskModel->id }})"
                                :tooltip="__('Stop the model and related tasks, it will keep your completed tasks and delete the future ones')"
                                variant="ghost"
                                icon="stop"
                                size="xs"/>

                            <flux:button
                                wire:click="deleteTaskModel({{ $taskModel->id }})"
                                :tooltip="__('Delete the model and all tasks related')"
                                variant="ghost"
                                icon="trash"
                                size="xs"/>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </x-settings.layout>

    <flux:modal name="task-model-form" @close="closeTaskModelForm" class="w-6xl">
        <form wire:submit="updateTaskModel" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Edit the task model') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Update the name and description of your task model') }}
                </flux:text>
            </div>

            <flux:input
                wire:model="editName"
                :label="__('Name')"
                required/>

            <flux:textarea
                wire:model="editDescription"
                :label="__('Description')"/>

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
