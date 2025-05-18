<?php

namespace App\Console\Commands;

use App\Models\Execution;
use App\Services\Executions\ExecutionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ScheduledExecutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scheduled-executions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lanza las ejecuciones programadas según su configuración de repetición';

    /**
     * Execute the console command.
     */
    public function handle(ExecutionService $executionService)
    {
        $now = Carbon::now();
        $shoulds = 0;

        $scheduled = Execution::where('execution_type', 'scheduled')
            ->where('status', '!=', 'running')
            ->get();

        foreach ($scheduled as $execution) {
            $lastRun = $execution->finished_at ?? $execution->started_at;
            $shouldRun = false;
            $firstTime = (is_null($execution->started_at) && is_null($execution->finished_at));

            switch ($execution->repeat) {
                case 'none':
                    // Solo si no se ha ejecutado aún
                    $shouldRun = is_null($execution->finished_at);
                    break;
                case 'hourly':
                    $shouldRun = $lastRun->diffInHours($now) >= 1;
                    break;
                case 'daily':
                    $shouldRun = $firstTime || $lastRun->diffInDays($now) >= 1;
                    break;
                case 'weekly':
                    $shouldRun = $firstTime || $lastRun->diffInWeeks($now) >= 1;
                    break;
            }

            if ($shouldRun) {
                $shoulds++;
                $this->info("Lanzando ejecución ID {$execution->id}...");
                $executionService->runExecution($execution);
            }
        }

        $this->info('Ejecuciones programadas revisadas correctamente: ' . $shoulds);
        return 0;
    }
}