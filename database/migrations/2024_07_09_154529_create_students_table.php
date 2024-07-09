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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('department_id');
            $table->string('student_photo')->nullable();
            $table->string('student_id')->unique();
            $table->string('student_lastname');
            $table->string('student_firstname');
            $table->string('student_middlename');
            $table->string('student_rfid')->unique();
            $table->timestamps();

            // Foreign keys
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('restrict');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
