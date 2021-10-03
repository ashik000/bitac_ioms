<?php

namespace App\ReportGenerators;

use App\Data\Models\Report;
use Carbon\CarbonImmutable;
use DB;

class DailyReportGenerator implements ReportGenerator
{

    public function generate(CarbonImmutable $start, CarbonImmutable $end)
    {
        // get last report id
        $lastReportData = DB::table('reports')
            ->select('id')
            ->orderBy('id', 'desc')
            ->first();

        $lastReportId = $lastReportData->id ?? 0;

        $reports = Report::where('tag', 'hourly')
            ->whereDate('generated_at', $start->toDateString())
            ->groupBy([
                'station_id',
                'product_id',
                'shift_id',
                'operator_id'
            ])
            ->select([
                DB::raw('SUM(expected) as expected'),
                DB::raw('SUM(produced) as produced'),
                DB::raw('SUM(scraped) as scraped'),
                DB::raw('SUM(available) as available'),
                DB::raw('SUM(unplanned_downtime) as unplanned_downtime'),
                DB::raw('SUM(planned_downtime) as planned_downtime'),
                'station_id',
                'product_id',
                'operator_id',
                'shift_id'
            ])
            ->get();

        $dailyReports = collect();
        $reports->each(function (Report $report) use ($start, &$lastReportId, &$dailyReports)
        {
            $dailyReport               = $report->replicate(['tag', 'generated_at', 'created_at', 'updated_at']);
            $dailyReport->tag          = 'daily';
            $dailyReport->id           = ++$lastReportId;
            $dailyReport->generated_at = $start->endOfDay()->toDateTimeString();

            $dailyReports[] = $dailyReport;
        });

        DB::transaction(function () use ($dailyReports)
        {
            $dailyReports->each(function (Report $report)
            {
                $report->save();
            });
        }, 5);
    }

    public function truncate($cascadeUp)
    {
        // TODO: Implement truncate() method.
    }
}
