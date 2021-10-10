<?php

namespace App\Console\Commands;

use App\ReportGenerators\DailyReportGenerator;
use App\ReportGenerators\HourlyReportGenerator;
use App\ReportGenerators\MonthlyReportGenerator;
use App\ReportGenerators\ReportGenerator;
use App\ReportGenerators\WeeklyReportGenerator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate
                                {--T|tag= : Type of report `hourly`, `daily`, `weekly`, `monthly`}
                                {--S|start=}
                                {--E|end=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Reports from Production Logs';

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
     * @return mixed
     */
    public function handle()
    {
        $tag   = $this->option('tag');
        $start = $this->option('start');
        $end   = $this->option('end');

        $start = Carbon::parse($start)->toImmutable();
        $end   = Carbon::parse($end)->toImmutable();

        switch ($tag) {
            case 'hourly':
            case 'daily':
            case 'weekly':
            case 'monthly':
                $generator = $this->getGenerator($tag);

                $generator->generate($start, $end);
                break;
            default:
                $this->error("Report Generator with Tag `{$tag}` Not Found");
        }
    }

    private function getGenerator($tag): ReportGenerator
    {
        switch ($tag) {
            case 'hourly':
                return app(HourlyReportGenerator::class);
            case 'daily':
                return new DailyReportGenerator();
            case 'weekly':
                return new WeeklyReportGenerator();
            case 'monthly':
                return new MonthlyReportGenerator();
        }
    }
}
