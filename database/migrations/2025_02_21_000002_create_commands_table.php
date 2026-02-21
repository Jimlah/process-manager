<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('command');
            $table->string('alias')->unique();
            $table->string('status')->default('stopped');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
