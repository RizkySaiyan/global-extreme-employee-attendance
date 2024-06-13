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
        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->integer('employeeId');
            $table->date('date');
            $table->dateTime('clockIn');
            $table->dateTime('clockOut');
            $table->integer('approvedBy')->nullable();
            $table->string('approvedByName')->nullable();
            $table->string('notes')->nullable();
            $this->getDefaultCreatedBy($table);
            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_corrections');
    }
};
