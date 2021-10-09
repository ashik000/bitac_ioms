<?php

namespace App\Console\Commands;

use App\Data\Models\ProductionLog;
use App\Data\Models\Report;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportWithPreviousDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:regenerate
                                {--T|tag= : Type of report `hourly`, `daily`, `weekly`, `monthly`}
                                {--S|start=}
                                {--E|end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tag   = $this->option('tag');
        $start = $this->option('start');
        $end   = $this->option('end');

        $start = Carbon::parse($start)->toImmutable();
        $end   = Carbon::parse($end)->toImmutable();

        $lastReport = Report::where('tag', $tag)
                            ->orderBy('generated_at', 'desc')->first();

        $productionLogCounts = ProductionLog::where('produced_at', '>', empty($lastReport)? '2001-01-01 00:00:00' : $lastReport->generated_at)->count();
        $this->info($productionLogCounts);
        if($productionLogCounts < 1) {
            $this->info('Report will not be generated since the are no logs');
            return 0;
        }
        $this->call(RegenerateReportsCommand::class, [
            '-T' => $tag,
            '-S' => !empty($lastReport)? $lastReport->generated_at : $start->toDateTimeString(),
            '-E' => $end->toDateTimeString(),
        ]);
        return 0;
    }
}
