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
        Schema::table('forum_messages', function (Blueprint $table) {
            $table->foreignId('topic_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('forum_topics')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forum_messages', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['topic_id']);
            $table->dropColumn('topic_id');
        });
    }
};
