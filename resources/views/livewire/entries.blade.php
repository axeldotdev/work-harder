<?php

use App\Enums\TaskStatus;
use App\Models\Entry;
use App\Models\Task;
use App\Models\User;
use Carbon\CarbonPeriod;
use Flux\DateRange;
use Livewire\Attributes\Session;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';

    public string $content = '';

    public ?Entry $currentEntry = null;

    public function closeEntryForm(): void
    {
        $this->reset('name', 'content');
    }

    public function deleteEntry(Entry $entry): void
    {
        $entry->delete();

        Flux::toast(__('Entry deleted successfully'), variant: 'success');
    }

    public function editEntry(Entry $entry): void
    {
        $this->currentEntry = $entry;
        $this->name = $entry->name;
        $this->content = $entry->content;

        Flux::modal('entry-form')->show();
    }

    public function saveEntry(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        if ($this->currentEntry instanceof \App\Models\Entry) {
            $this->currentEntry->update($validated);

            $this->currentEntry = null;
        } else {
            $this->user->entries()->create($validated);
        }

        Flux::modal('entry-form')->close();

        Flux::toast(__('Entry saved successfully'), variant: 'success');
    }

    #[Computed]
    public function hasEntryForToday(): bool
    {
        return $this->entries
            ->filter(fn (Entry $entry) => $entry->created_at->isToday())
            ->isNotEmpty();
    }

    #[Computed]
    public function entries(): Collection
    {
        return $this->user->entries()->latest()->get();
    }

    #[Computed]
    public function user(): User
    {
        return auth()->user();
    }
};

?>

<section class="mx-auto w-full max-w-7xl px-6 lg:px-8">
    <div class="flex justify-end items-center mb-8">
        <flux:modal.trigger name="entry-form">
            <flux:button variant="primary">
                {{ __('New journal entry') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="w-full flex flex-col gap-4">
        @if (! $this->hasEntryForToday)
            <div class="flex justify-center p-4 bg-red-100 rounded-lg">
                <flux:text color="red">
                    {{ __("No entry for today, don't forget to write one") }}
                </flux:text>
            </div>
        @endif

        @foreach ($this->entries as $entry)
            <flux:card>
                <div class="flex justify-between">
                    <div>
                        <flux:text variant="subtle">
                            {{ $entry->created_at->format('l j F Y') }}
                        </flux:text>

                        <flux:heading level="2" size="xl" class="mt-1">
                            {{ $entry->name }}
                        </flux:heading>
                    </div>

                    <div class="">
                        <flux:button
                            wire:click="editEntry({{ $entry->id }})"
                            :tooltip="__('Edit entry')"
                            variant="ghost"
                            icon="pencil"
                            size="xs"/>

                        <flux:button
                            wire:click="deleteEntry({{ $entry->id }})"
                            :tooltip="__('Delete entry')"
                            variant="ghost"
                            icon="trash"
                            size="xs"/>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-3">
                    {!! $entry->content !!}
                </div>
            </flux:card>
        @endforeach
    </div>

    <flux:modal name="entry-form" @close="closeEntryForm" class="w-6xl">
        <form wire:submit="saveEntry" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ __('Create a journal entry') }}
                </flux:heading>

                <flux:text class="mt-2">
                    {{ __('Make sure to note your mood at the moment, your goals, etc.') }}
                </flux:text>
            </div>

            <flux:input
                wire:model="name"
                :label="__('Name')"
                required/>

            <flux:editor
                wire:model="content"
                :label="__('Content')"
                toolbar="heading | bold italic underline strike code link | bullet ordered blockquote"
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
