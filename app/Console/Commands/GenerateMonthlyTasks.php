<?php

namespace App\Console\Commands;

use App\Enums\TaskModelStatus;
use App\Models\TaskModel;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class GenerateMonthlyTasks extends Command
{
    /** @var string */
    protected $signature = 'tasks:generate';

    /** @var string */
    protected $description = 'Generate monthly tasks';

    public function handle(): int
    {
        $taskModels = TaskModel::query()
            ->where('status', '!=', TaskModelStatus::Completed)
            ->get();

        foreach ($taskModels as $taskModel) {
            $lastTask = $taskModel->tasks()->latest('due_at')->first();

            $nextTaskDay = $lastTask->due_at->addDay();

            if ($taskModel->end_at
                && $taskModel->end_at->isAfter($nextTaskDay)) {
                continue;
            }

            $days = CarbonPeriod::create($nextTaskDay, $nextTaskDay);

            $taskModel->createTasks($days);
        }

        $this->info('The tasks for the next month have been generated.');

        return self::SUCCESS;
    }
}
