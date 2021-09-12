<?php

namespace App\ReportGenerators;

use App\Data\Models\Report;
use Carbon\CarbonImmutable;
use DB;

class WeeklyReportGenerator implements ReportGenerator
{

    public function generate(CarbonImmutable $start, CarbonImmutable $end)
    {
        // get last report id
        $lastReportData = DB::table('reports')
            ->select('id')
            ->orderBy('id', 'desc')
            ->first();

        $lastReportId = $lastReportData->id ?? 0;

        $reports = Report::where('tag', 'daily')
            ->whereBetween('generated_at', [
                $start->startOfDay()->toDateTimeString(),
                $end->endOfDay()->toDateTimeString()
            ])
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

        $weeklyReports = collect();
        $reports->each(function (Report $report) use ($start, $lastReportId, &$weeklyReports)
        {
            $weeklyReport               = $report->replicate(['tag', 'generated_at', 'created_at', 'updated_at']);
            $weeklyReport->tag          = 'weekly';
            $weeklyReport->id           = $lastReportId++;
            $weeklyReport->generated_at = $start->endOfDay()->toDateTimeString();

            $weeklyReports[] = $weeklyReport;
        });

        DB::transaction(function () use ($weeklyReports)
        {
            $weeklyReports->each(function (Report $report)
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
