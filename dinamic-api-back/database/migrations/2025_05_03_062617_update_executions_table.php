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
        Schema::table('executions', function (Blueprint $table) {
            $table->enum('repeat', ['none', 'hourly', 'daily', 'weekly', 'custom'])
                  ->default('none')
                  ->after('execution_type');

            $table->string('cron_expression')
                  ->nullable()
                  ->after('repeat');

            $table->timestamp('last_executed_at')
                  ->nullable()
                  ->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('executions', function (Blueprint $table) {
            $table->dropColumn(['repeat', 'cron_expression', 'last_executed_at']);
        });
    }
};
