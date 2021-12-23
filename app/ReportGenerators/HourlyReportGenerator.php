<?php

namespace App\ReportGenerators;

use App\Data\Models\Operator;
use App\Data\Models\Report;
use App\Data\Models\Shift;
use App\Data\Models\Station;
use App\Data\Models\StationOperator;
use App\Data\Repositories\OperatorRepository;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ProductRepository;
use App\Data\Repositories\ScrapRepository;
use App\Data\Repositories\ShiftRepository;
use App\Devices\InovaceDevice;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DB;
use Log;

class HourlyReportGenerator implements ReportGenerator
{
    protected $productionLogRepository;
    protected $productRepository;
    protected $scrapRepository;
    protected $shiftRepository;
    protected $operatorRepository;
    protected $deviceManager;

    public function __construct(ProductionLogRepository $productionLogRepository,
                                ScrapRepository $scrapRepository,
                                ProductRepository $productRepository,
                                ShiftRepository $shiftRepository,
                                OperatorRepository $operatorRepository, InovaceDevice $deviceManager)
    {
        $this->productionLogRepository = $productionLogRepository;
        $this->scrapRepository         = $scrapRepository;
        $this->productRepository       = $productRepository;
        $this->shiftRepository         = $shiftRepository;
        $this->operatorRepository      = $operatorRepository;
        $this->deviceManager           = $deviceManager;
    }

    public function generate(CarbonImmutable $start, CarbonImmutable $end)
    {
        $stations = Station::with('products')->get();

        $stationIdToShiftsMap = $this->shiftRepository->findAllShiftsGroupByStationId();
        $stationIdToStationOperatorMap = $this->operatorRepository->findAllStationOperatorsGroupByStationId();

//        $shift = Shift::where('start_time', '<=',$start->toTimeString())
//            ->where('end_time','>=',$end->toTimeString())->first();

        $allProductionLogs = $this->productionLogRepository->fetchProductionLogs([
            'between' => [
                'start' => $start,
                'end'   => $end,
            ],
        ])->groupBy(['station_id', 'product_id'], true);

        $allSlowLogs = $this->productionLogRepository->fetchSlowLogs([
            'between' => [
                'start' => $start,
                'end'   => $end,
            ],
        ])->groupBy(['station_id', 'product_id'], true);

        $allDowntimes = $this->productionLogRepository->fetchDowntimes([
            'between' => [
                'start' => $start,
                'end'   => $end,
            ],
        ])->groupBy(['station_id', 'product_id'], true);

        $allScraps = $this->scrapRepository->fetchScrapsBetweenADateRangeOfAllStations($start, $end)->groupBy(['station_id', 'product_id']);

        $reports = collect();
        $reportLastId = DB::table('reports')->max('id');
        foreach ($stations as $station) {
            $stationOperator = $this->deviceManager->findOperatorOfStation($stationIdToStationOperatorMap, $station->id, Carbon::parse($start));
            $shift = $this->deviceManager->findShiftOfStation($stationIdToShiftsMap, $station->id, Carbon::parse($start));
            $productIdToStationProductsMap = $this->productRepository->findAllStationProductsOfAStationKeyByProductId($station->id);
            foreach ($station->products as $product) {
                $stationProduct = $productIdToStationProductsMap->get($product->id);
                $productionLogs = $allProductionLogs->get($station->id, collect())->get($product->id, collect());
                $slowLogs       = $allSlowLogs->get($station->id, collect())->get($product->id, collect());
                $downtimes      = $allDowntimes->get($station->id, collect())->get($product->id, collect());
                $scraps         = $allScraps->get($station->id, collect())->get($product->id, collect());

                $nominalTime = $productionLogs->sum(function ($log) use (&$stationProduct) {
                    return $log->cycle_interval >= ($stationProduct->cycle_time + $stationProduct->cycle_timeout) ? ($stationProduct->cycle_time + $stationProduct->cycle_timeout) : $log->cycle_interval;
                });
//                $slowTime    = $slowLogs->sum('duration');
                $totalStopTime    = $downtimes->sum('duration');
                $plannedStopTime = $downtimes->filter(function($val, $key){
                    return isset($val['reason']) && $val->reason->type==='planned';
                })->sum('duration');
                $unplannedStopTime = $totalStopTime - $plannedStopTime;


                $totalTime = $nominalTime;

                $expected = floor(($totalTime  + $unplannedStopTime)/ $product->meta->cycle_time);
                $produced = $productionLogs->sum('units_per_signal');
                $reportLastId = $reportLastId ? $reportLastId+1 : 1;
                $report               = new Report();
                $report->id           = $reportLastId;
                $report->generated_at = $start->endOfHour();
                $report->tag          = 'hourly';
                $report->station_id   = $station->id;
                $report->team_id      = $station->team_id ?? null;
                $report->product_id   = $product->id;
                $report->shift_id = $shift['id']?? 1;
                $report->operator_id  = $stationOperator->operator_id?? null;

                $report->available = $totalTime;

                $report->produced = $produced;

                $report->scraped = $scraps->filter(function($s) use ($report){
                    $d = Carbon::parse($report->generated_at)->hour;
                    return $s->hour === $d;
                })->sum('value');

                $report->expected = $expected;

                $report->unplanned_downtime = $unplannedStopTime;
                $report->planned_downtime   = $plannedStopTime;

                $reports->push($report);
            }
        }

        DB::transaction(function () use ($reports) {
            $reports->each(function (Report $report) {
                $report->save();
            });
        }, 5);
    }

    public function truncate($cascadeUp)
    {
        // TODO: Implement truncate() method.
    }
}
