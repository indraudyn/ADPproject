<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cerita', function (Blueprint $table) {
            $table->unsignedInteger('view_count')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('cerita', function (Blueprint $table) {
            $table->dropColumn('view_count');
        });
    }
};
