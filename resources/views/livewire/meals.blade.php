<?php

use App\Enums\MealType;
use App\Enums\TaskStatus;
use App\Models\Entry;
use App\Models\Meal;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Flux\DateRange;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Session;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    #[Session]
    public ?DateRange $range = null;

    public ?Meal $currentMeal = null;

    public string $name = '';

    public string $description = '';

    public ?MealType $type = null;

    public function mount(): void
    {
        if (!$this->range instanceof \Flux\DateRange) {
            $this->range = new DateRange(
                today()->startOfWeek(),
                today()->endOfWeek(),
            );
        }
    }

    public function closeMealForm(): void
    {
        $this->reset('name', 'description', 'type');
    }

    public function deleteMeal(Meal $meal): void
    {
        $meal->delete();

        Flux::toast(__('Meal deleted successfully'), variant: 'success');
    }

    public function editMeal(Meal $meal): void
    {
        $this->currentMeal = $meal;
        $this->name = $meal->name;
        $this->description = $meal->description;
        $this->type = $meal->type;

        Flux::modal('meal-form')->show();
    }

    public function retrieveMeals(): void
    {
        unset($this->meals);
    }

    public function saveMeal(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::enum(MealType::class)],
        ]);

        if ($this->currentMeal) {
            $this->currentMeal->update($validated);
        } else {
            $this->user->meals()->create($validated);
        }

        Flux::modal('meal-form')->close();

        Flux::toast(__('Meal saved successfully'), variant: 'success');
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
                'meals' => $this->user->meals()
                    ->whereDate('created_at', $day)
                    ->get(),
            ]);
        }

        return $days;
    }

    #[Computed]
    public function typesEnum(): array
    {
        return MealType::cases();
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
            wire:change="retrieveMeals"
            mode="range"
            presets="today thisWeek lastWeek thisMonth lastMonth thisQuarter thisYear allTime"
            start-day="1"
            class="min-w-64"/>

        <div class="flex gap-2">
            <flux:modal.trigger name="meal-form">
                <flux:button variant="primary">
                    {{ __('New meal') }}
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
                    @forelse ($day->meals as $meal)
                        <flux:card size="sm" class="grid grid-cols-3 gap-2 items-center">
                            <div>
                                <flux:heading level="3" size="lg">
                                    {{ $meal->name }}
                                </flux:heading>

                                <flux:tooltip :content="$meal->description">
                                    <flux:text variant="subtle" class="truncate">
                                        {{ $meal->description }}
                                    </flux:text>
                                </flux:tooltip>
                            </div>

                            <div>
                                <flux:badge
                                    color="zinc"
                                    size="sm"
                                    inset="top bottom">
                                    {{ $meal->type->label() }}
                                </flux:badge>
                            </div>

                            <div class="text-end">
                                <flux:button
                                    wire:click="editMeal({{ $meal->id }})"
                                    :tooltip="__('Edi the meal')"
                                    variant="ghost"
                                    icon="pencil"
                                    size="xs"/>

                                <flux:button
                                    wire:click="deleteMeal({{ $meal->id }})"
                                    :tooltip="__('Delete the meal')"
                                    variant="ghost"
                                    icon="trash"
                                    size="xs"/>
                            </div>
                        </flux:card>
                    @empty
                        <div class="flex justify-center p-4 bg-zinc-100 rounded-lg">
                            <flux:text variant="subtle">
                                {{ __('No meal for that day') }}
                            </flux:text>
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <flux:modal name="meal-form" @close="closeMealForm" class="w-6xl">
        <form wire:submit="saveMeal" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Add new meal') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Create a meal with all thins you eat') }}
                </flux:text>
            </div>

            <flux:input
                wire:model="name"
                :label="__('Name')"
                required/>

            <flux:textarea
                wire:model="description"
                :label="__('Description')"/>

            <flux:radio.group
                wire:model="type"
                :label="__('Type')"
                variant="pills">
                @foreach ($this->typesEnum as $type)
                    <flux:radio
                        :value="$type->value"
                        :label="$type->label()"/>
                @endforeach
            </flux:radio.group>

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
