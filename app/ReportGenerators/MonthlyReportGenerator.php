<?php

namespace App\ReportGenerators;

use App\Data\Models\Report;
use Carbon\CarbonImmutable;
use DB;

class MonthlyReportGenerator implements ReportGenerator
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
                $start->startOfMonth()->toDateTimeString(),
                $end->endOfMonth()->toDateTimeString()
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

        $monthlyReports = collect();
        $reports->each(function (Report $report) use ($start, &$lastReportId, &$monthlyReports)
        {
            $monthlyReport               = $report->replicate(['tag', 'generated_at', 'created_at', 'updated_at']);
            $monthlyReport->tag          = 'monthly';
            $monthlyReport->id           = $lastReportId++;
            $monthlyReport->generated_at = $start->endOfDay()->toDateTimeString();

            $monthlyReports[] = $monthlyReport;
        });

        DB::transaction(function () use ($monthlyReports)
        {
            $monthlyReports->each(function (Report $report)
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
