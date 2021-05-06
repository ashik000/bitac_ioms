<?php

namespace App\Console\Commands;

use App\Data\Models\Report;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class RegenerateReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:refresh
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
     * @return mixed
     */
    public function handle()
    {
        $tag   = $this->option('tag');
        $start = $this->option('start');
        $end   = $this->option('end');

        /**
         * @var $start CarbonImmutable
         * @var $end CarbonImmutable
         */
        $start = Carbon::parse($start)->startOfHour();
        $end   = Carbon::parse($end)->endOfHour();

        $this->warn("Deleting `{$tag}` Reports between {$start->toDateTimeString()} and {$end->toDateTimeString()} ...");
        Report::whereTag($tag)
              ->whereBetween('generated_at', [
                  $start,
                  $end,
              ])->delete();
        $this->warn("Deleted `{$tag}` Reports between {$start->toDateTimeString()} and {$end->toDateTimeString()}");

        switch ($tag) {
            case 'hourly':
                $increment = '1 hour';
                $start     = Carbon::parse($start)->startOfHour();
                $end        = Carbon::parse($end)->endOfHour();
                break;

            case 'daily':
                $increment = '1 day';
                $start     = Carbon::parse($start)->startOfDay();
                $end        = Carbon::parse($end)->endOfDay();
                break;

            case 'weekly':
                $increment = '1 week';
                $start     = Carbon::parse($start)->startOfWeek(Carbon::SATURDAY);
                $end     = Carbon::parse($end)->endOfWeek(Carbon::FRIDAY);
                break;

            case 'monthly':
                $start     = Carbon::parse($start)->startOfMonth();
                $end     = Carbon::parse($end)->endOfMonth();
                $increment = '1 month';
                break;
        }

        for (
            $rangeStart = $start->copy(), $rangeEnd = $start->copy()->add($increment)->subSecond(1);
            $end->greaterThanOrEqualTo($rangeStart) && $end->greaterThanOrEqualTo($rangeEnd);
            $rangeStart->add($increment), $rangeEnd->add($increment)
        ) {
            $this->info("Regenerating `{$tag}` Report between {$rangeStart->toDateTimeString()} and {$rangeEnd->toDateTimeString()} ...");
            $this->call(GenerateReportCommand::class, [
                '-T' => $tag,
                '-S' => $rangeStart->toDateTimeString(),
                '-E' => $rangeEnd->toDateTimeString(),
            ]);
        }
    }
}
