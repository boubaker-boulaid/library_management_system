<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Deleting a language or type must not silently delete its books.
            $table->dropForeign(['language_id']);
            $table->dropForeign(['type_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('language_id')->nullable()->default(null)->change();
            $table->foreignId('type_id')->default(null)->change();
            $table->text('description')->nullable()->change();
            $table->decimal('prix', 10, 2)->default(0)->change();

            $table->foreign('language_id')->references('id')->on('languages')->nullOnDelete();
            $table->foreign('type_id')->references('id')->on('types')->restrictOnDelete();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['book_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['language_id']);
            $table->dropForeign(['type_id']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->foreignId('language_id')->nullable(false)->default(1)->change();
            $table->foreignId('type_id')->default(1)->change();
            $table->text('description')->nullable(false)->change();
            $table->double('prix')->default(0)->change();

            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
            $table->foreign('type_id')->references('id')->on('types')->cascadeOnDelete();
        });
    }
};
