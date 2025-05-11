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
        Schema::create('api_call_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('direction', ['to_api', 'from_api', 'both'])->default('to_api');
            $table->text('description')->nullable();

            // Origen
            $table->foreignId('source_api_call_id')->nullable()->constrained('api_calls')->nullOnDelete();
            $table->foreignId('source_db_connection_id')->nullable()->constrained('database_connections')->nullOnDelete();
            $table->string('source_table')->nullable();

            // Destino
            $table->foreignId('target_api_call_id')->nullable()->constrained('api_calls')->nullOnDelete();
            $table->foreignId('target_db_connection_id')->nullable()->constrained('database_connections')->nullOnDelete();
            $table->string('target_table')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_call_mappings');
    }
};
