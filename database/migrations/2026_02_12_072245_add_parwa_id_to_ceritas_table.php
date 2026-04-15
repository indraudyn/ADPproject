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
        Schema::table('ceritas', function (Blueprint $table) {
            $table->foreignId('parwa_id')->nullable()->constrained()->onDelete('set null');
            $table->string('sub_parwa')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('ceritas', function (Blueprint $table) {
            $table->dropForeign(['parwa_id']);
            $table->dropColumn(['parwa_id', 'sub_parwa']);
        });
    }
};
