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
        Schema::create('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('employee_photo')->nullable();
            $table->string('employee_firstname');
            $table->string('employee_middlename');
            $table->string('employee_lastname');
            $table->string('employee_rfid');
            $table->timestamps();

            // Primary key on employee_id
            $table->primary('employee_id');

            // Unique constraint for composite key (employee_id, school_id, department_id)
            $table->unique(['employee_id', 'school_id', 'department_id']);
            // Index on department_id in employees table
            $table->index('department_id');
            // Foreign key constraints
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('restrict');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('restrict');



            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
