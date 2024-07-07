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
        
        Schema::create('departments', function (Blueprint $table) {
            // Columns definition
            $table->unsignedBigInteger('department_id'); // Ensure department_id is NOT NULL
            $table->unsignedBigInteger('school_id'); // Allow school_id to be NULL
            $table->string('department_abbreviation');
            $table->string('department_name');
            $table->timestamps();

            // Define the composite primary key
            $table->primary(['school_id', 'department_id']);

            // Define foreign key constraint
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('restrict');

             $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
