<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('motivations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->text('url');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motivations');
    }
};
