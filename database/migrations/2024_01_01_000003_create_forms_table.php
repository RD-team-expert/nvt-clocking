<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Create forms table for storing form submissions
     */
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            // Primary key
            $table->id();
            
            // Form fields
            $table->string('name'); // Name field
            $table->date('date'); // Date field
            $table->string('file_path')->nullable(); // File upload path
            $table->string('file_original_name')->nullable(); // Original filename
            
            // User relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations - Drop forms table
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};