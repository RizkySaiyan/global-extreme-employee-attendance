<?php

use Database\Migrations\Traits\HasCustomMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use HasCustomMigration;


    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employeeId');
            $table->date('fromDate');
            $table->date('toDate');
            $table->string('notes');
            $table->integer('status');
            $table->integer('approvedBy')->nullable();
            $table->string('approvedByName')->nullable();
            $this->getDefaultTimestamps($table);
            $this->getDefaultCreatedBy($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_leaves');
    }
};
