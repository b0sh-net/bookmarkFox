<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmark_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('bookmark_folders')->cascadeOnDelete();
            $table->string('firefox_id', 64);
            $table->string('name', 255);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'firefox_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmark_folders');
    }
};
