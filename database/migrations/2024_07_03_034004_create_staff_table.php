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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('staff_id');
            $table->string('staff_firstname');
            $table->string('staff_middlename');
            $table->string('staff_lastname');
            $table->string('staff_rfid');
            $table->enum('access_type', ['administrative', 'departmental'])->default('administrative');
            $table->timestamps();
            
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
        
    }
};
