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
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->time('clockIn')->change();
            $table->time('clockOut')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_corrections', function (Blueprint $table) {
            $table->datetime('clockIn')->change();
            $table->datetime('clockOut')->change();
        });
    }
};
