<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table): void {
            $table->after('task_model_id', function ($table): void {
                $table->string('name')->nullable();
                $table->text('description')->nullable();
            });
        });
    }
};
