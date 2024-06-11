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
        Schema::create('component_company_office_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companyOfficeId')->constrained('component_company_offices')->onDelete('cascade');
            $table->foreignId('departmentId')->constrained('component_departments')->onDelete('restrict');
            $this->getDefaultTimestamps($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_office_departments');
    }
};
