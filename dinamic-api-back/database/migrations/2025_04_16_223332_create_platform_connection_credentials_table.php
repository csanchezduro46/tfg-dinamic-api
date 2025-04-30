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
        Schema::create('platform_connection_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_connection_id')->constrained()->onDelete('cascade');
            $table->foreignId('necessary_key_id')->nullable()->constrained('platform_necessary_keys')->nullOnDelete();
            $table->text('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_connection_credentials');
    }
};
