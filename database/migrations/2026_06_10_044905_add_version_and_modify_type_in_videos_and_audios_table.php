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
        Schema::table('videos', function (Blueprint $table) {
            $table->string('version')->nullable()->after('section');
        });

        Schema::table('audios', function (Blueprint $table) {
            $table->string('version')->nullable()->after('section');
            $table->string('type')->default('upload')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('version');
        });

        Schema::table('audios', function (Blueprint $table) {
            $table->dropColumn('version');
            // Reverting string to enum is tricky and DBMS dependent, so we keep it as string in down migration
        });
    }
};
