<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->text('title')->change();
        });

        Schema::table('bookmark_folders', function (Blueprint $table) {
            $table->text('name')->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->string('title', 255)->change();
        });

        Schema::table('bookmark_folders', function (Blueprint $table) {
            $table->string('name', 255)->change();
        });
    }
};
