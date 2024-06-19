<?php

use Illuminate\Contracts\Database\Eloquent\Builder;
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
        Schema::table('attendance_public_holidays', function (Blueprint $table) {
            $table->boolean('isAssigned')->after('date')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_public_holidays', function (Blueprint $table) {
            $table->dropColumn('isAssigned');
        });
    }
};
