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
        Schema::create('api_call_mapping_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_call_mapping_id')->constrained()->onDelete('cascade');
            $table->string('source_field'); // campo en el origen (api o db)
            $table->string('target_field'); // campo en el destino (api o db)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_call_mapping_fields');
    }
};
