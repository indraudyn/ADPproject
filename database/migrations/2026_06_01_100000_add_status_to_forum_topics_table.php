<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('forum_topics', 'status')) {
            Schema::table('forum_topics', function (Blueprint $table) {
                $table->enum('status', ['pending', 'approved', 'rejected'])
                      ->default('pending')
                      ->after('slug');
            });
        }
    }

    public function down(): void
    {
        Schema::table('forum_topics', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
