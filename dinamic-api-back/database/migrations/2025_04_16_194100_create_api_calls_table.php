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
        Schema::create('api_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_version_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('endpoint');
            $table->string('method');
            $table->string('request_type')->nullable();
            $table->string('response_type')->nullable();
            $table->json('payload_example')->nullable();
            $table->json('response_example')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_calls');
    }
};
