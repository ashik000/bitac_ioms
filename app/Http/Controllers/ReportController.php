<?php

namespace App\Http\Controllers;

use App\Data\Models\ProductionLog;
use App\Data\Models\Report;
use App\Data\Models\Downtime;
use App\Data\Models\Scrap;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Http\Requests\HourlyProductionFetchRequest;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Log;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type  = $request->get('type');
        $start = $request->get('start');
        $end   = $request->get('end');

        $start = CarbonImmutable::parse($start);
        $end   = CarbonImmutable::parse($end);

        $stationId = @$request->get('station_id');
        $productId = null;
        $shiftId = null;
        $operatorId = null;
        $stationProductId = @$request->get('station_product_id');
        $stationOperatorId = @$request->get('station_operator_id');
        $stationShiftId = @$request->get('station_shift_id');

        if (!empty($stationProductId)) {
            $stationProduct = StationProduct::find($stationProductId);
            $stationId = $stationProduct->station_id;
            $productId = $stationProduct->product_id;
        }

        if (!empty($stationOperatorId)) {
            $stationOperator = StationOperator::find($stationOperatorId);
            $stationId = $stationOperator->station_id;
            $operatorId = $stationOperator->operator_id;
        }

        if (!empty($stationShiftId)) {
            $stationShift = StationShift::find($stationShiftId);
            $stationId = $stationShift->station_id;
            $shiftId = $stationShift->shift_id;
        }

//        $operatorId = $request->get('operator_id');
//        $shiftId   = $request->get('shift_id');

        $query = Report::where('tag', $type);

        $groupedBy     = ['generated_at'];
        $availableBase = 3600;

        $title = '';
        switch ($type) {
            case 'hourly':
                $title = 'Hourly report';
                $query->whereBetween('generated_at', [$start->startOfDay(), $end->endOfDay()]);
                break;

            case 'daily':
                $title         = 'Daily report';
                $availableBase = 24 * 3600;
                $query->whereBetween('generated_at', [$start->startOfDay(), $end->endOfDay()]);
                break;

            case 'weekly':
                $title         = 'Weekly Report';
                $availableBase = 24 * 7 * 3600;
                $query->whereBetween('generated_at', [$start->startOfWeek(Carbon::SATURDAY), $end->endOfWeek(Carbon::FRIDAY)]);
                break;

            case 'monthly':
                $title         = 'Monthly Report';
                $availableBase = 30 * 24 * 7 * 3600;
                $query->whereBetween('generated_at', [$start->startOfMonth(), $end->endOfMonth()]);
                break;
        }

        $query->when($stationId, function ($q, $stationId) use (&$groupedBy) {
            $groupedBy[] = 'station_id';
            return $q->where('station_id', $stationId);
        });

        $query->when($productId, function ($q, $productId) use (&$groupedBy) {
            $groupedBy[] = 'product_id';

            return $q->where('product_id', $productId);
        });

        $query->when($shiftId, function ($q, $shiftId) use (&$groupedBy) {
            $groupedBy[] = 'shift_id';
            return $q->where('shift_id', $shiftId);
        });

        $query->when($operatorId, function ($q, $operatorId) use (&$groupedBy) {
            $groupedBy[] = 'operator_id';
            return $q->where('operator_id', $operatorId);
        });

        $query->groupBy($groupedBy);
        $query->orderBy('generated_at');
        $query->select([
            'generated_at',
            DB::raw('SUM(produced) as produced'),
            DB::raw('SUM(scraped) as scraped'),
            DB::raw('SUM(expected) as expected'),
            DB::raw('SUM(available) as available'),
            DB::raw('SUM(planned_downtime) as planned_downtime'),
        ]);

        $reports = $query->get();

        $reports = $reports->reduce(function ($carry, $item) use ($availableBase, $type) {
            $performance  = $item->produced ? ($item->produced / $item->expected) : 0;
//            $quality      = 1; //$item->produced ? ($item->scrapped / $item->produced) : 0;
            $quality      = $item->produced ? (($item->produced-$item->scraped) / $item->produced) : 0;
            $availability = $item->available ? ($item->available / ($availableBase - $item->planned_downtime)) : 0;

            $carry['produced'][]         =(int) $item->produced;
            $carry['scraped'][]          =(int)  $item->scraped;
            $carry['expected'][]         =(int)  $item->expected;
            $carry['available'][]        =(int)  $item->available;
            $carry['planned_downtime'][] =(int)  $item->planned_downtime;


            $carry['labels'][]       = $this->getLabel($item->generated_at, $type);
            $carry['generated_at'][] = $item->generated_at;
            $carry['performance'][]  = $performance * 100;
            $carry['quality'][]      = $quality * 100;
            $carry['availability'][] = $availability * 100;
            $carry['oee'][]          = $performance * $quality * $availability * 100;

            return $carry;
        }, [
            'labels'       => [],
            'generated_at' => [],
            'performance'  => [],
            'quality'      => [],
            'availability' => [],
            'oee'          => [],
        ]);

        return response()->json(
            [
                'title'   => $title,
                'dataset' => $reports,
            ]
        );
    }

    private function getLabel($generated_at, $type)
    {
        $date = CarbonImmutable::parse($generated_at);

        switch ($type) {
            case 'hourly':
                if ($date->hour == 0 || $date->hour == 12) {
                    return $date->format("Y-m-d H:00");
                } else {
                    return $date->format('H:00');
                }

            case 'daily':
                return $date->format("M d");

            case 'weekly':
                $start = $date->startOfWeek(Carbon::SATURDAY)->format("M d");
                $end   = $date->endOfWeek(Carbon::FRIDAY)->format("M d");
                return "{$start} - {$end}";


            case 'monthly':
                if ($date->month % 6 == 0) {
                    return $date->format("Y M");
                } else {
                    return $date->format("M");
                }
        }
    }

    public function getDowntimeReport(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');

        $start = CarbonImmutable::parse($start);
        $end = CarbonImmutable::parse($end);

        $stationId = @$request->get('station_id');
        $productId = null;
        $shiftId = null;
        $operatorId = null;

        $stationOperatorStartTime = null;
        $stationOperatorEndTime = null;
        $stationProductId = @$request->get('station_product_id');
        $stationShiftId = @$request->get('station_shift_id');
        $stationOperatorId = @$request->get('station_operator_id');

        if(!empty($stationProductId)){
            $stationProduct = StationProduct::find($stationProductId);
            $productId = $stationProduct->product_id;
            $stationId = $stationProduct->station_id;
        }
        if(!empty($stationShiftId)){
            $stationShift = StationShift::find($stationShiftId);
            $stationId = $stationShift->station_id;
            $shiftId = $stationShift->shift_id;
        }
        if(!empty($stationOperatorId)){
            $stationOperator = StationOperator::find($stationOperatorId);
            $operatorId = $stationOperator->operator_id;
            $stationId = $stationOperator->station_id;
            $stationOperatorStartTime = $stationOperator->start_time;
            $stationOperatorEndTime = $stationOperator->end_time;
        }

        $downtimeQuery = Downtime::query();
        $downtimeQuery->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
            ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
            ->when($stationId, function ($q, $sId) {
                $q->where( 'production_logs.station_id', '=', $sId);
            })
            ->when($productId, function ($q, $pId) {
                $q->where( 'production_logs.product_id', '=', $pId);
            })
            ->when($shiftId,function ($q,$shId){
                $q->where('downtimes.shift_id',$shId);
            })
            ->when($operatorId,function ($q,$opId){
                $q->where('downtimes.operator_id',$opId);
            })
            ->whereBetween('downtimes.start_time', [
                $start->startOfDay(),
                $end->endOfDay()
            ])
            ->select([DB::raw('SUM(downtimes.duration) as duration'), 'downtime_reasons.name'])
            ->orderBy('duration', 'DESC')
            ->groupBy(['downtimes.reason_id','downtime_reasons.name']);

        $downtimeResult = $downtimeQuery->get()->reduce(function ($carry, $item) {
            $carry['labels'][] = $item->name == null ? "Uncommented" : $item->name;
            $carry['duration'][] = $item->duration;
            return $carry;
        }, [
            'labels' => [],
            'duration' => []
        ]);

        return response()->json([
            'title' => 'Downtime Report',
            'dataset' => $downtimeResult
        ], 200);
    }

    public function getHourlyProducedAndScrapedCountOfADay(HourlyProductionFetchRequest $request)
    {
        $date = CarbonImmutable::parse($request->get('date'));
        $stationId = $request->get('stationId');
        $query = ProductionLog::join('products', 'products.id', '=', 'production_logs.product_id')
            ->whereBetween('production_logs.produced_at', [$date->startOfDay(), $date->endOfDay()])
            ->where('production_logs.station_id', '=', $request->get('stationId'))
            ->select('products.id', 'products.name', DB::raw('extract(hour from production_logs.produced_at) as hour'), DB::raw('count(*) as produced'))
            ->groupBy(DB::raw('extract(hour from production_logs.produced_at)'), 'products.id');

        $reports = $query->get();

        $query = Scrap::where('station_id', '=', $stationId)
            ->where('date', '=', $date);

        $scraps = $query->get()->groupBy(function (Scrap $scrap) {
            return $scrap->hour;
        })->map(function ($items) {
            return $items->reduce(function ($carry, $current) {
                $carry[$current->product_id] = $current->value;
                $carry[$current->product_id] = [
                    'value'=> $current->value,
                    'scrapId' => $current->id
                ];
                return $carry;
            }, []);
        });

        $results = $reports->map(function ($val, $key) use ($scraps, $date, $stationId) {
            $hour = $val->hour;
            $pId = $val->id;
            $val['scraped'] = 0;
            $val['stationId'] = $stationId;
            $val['date'] = $date->toDateString();
            if (isset($scraps[$hour])) {
                $t = $scraps[$hour];
                if (isset($t[$pId])) {
                    $val['scraped'] = $scraps[$hour][$pId]['value'];
                    $val['scrapId'] = $scraps[$hour][$pId]['scrapId'];
                }
            }
            return $val;
        });

        return $results;
    }

    public function getOEETableReportByStation(Request $request){
        $stationId  = $request->get('stationId');
        $start  = CarbonImmutable::parse($request->get('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->get('end'))->endOfDay();
        $type = $request->get('type');
        $type = ( is_null($type) || empty($type) ) ? 'hourly' : $type;
        if(is_null($stationId)){
            // Info: stationId null means all stations. In this case, there will be as many rows as stations and (availability,performance,quality,oee) fields will be from start and end duration only
            $result = Report::where('tag', 'hourly')
                ->whereBetween('generated_at', [$start, $end])
                ->groupBy('station_id')
                ->select([
                    'station_id',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get()->load('station.stationGroup');
            foreach ($result as &$row){
                $station = $row->station;
                $row['station_name'] = $station->name;
                $row['station_group_id'] = $station->station_group_id;
                $row['station_group_name'] = $row->station->stationGroup->name;
                unset($row->station);
                $totalTimeDuration = $end->diffInSeconds($start);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            if($type === 'hourly' || $type === 'daily'){
                $queryStart = $start->startOfDay();
                $queryEnd = $end->endOfDay();
            } else if($type == 'weekly'){
                $queryStart = $start->startOfWeek(Carbon::SATURDAY);
                $queryEnd = $end->endOfWeek(Carbon::FRIDAY);
            } else {
                $queryStart = $start->startOfMonth();
                $queryEnd = $end->endOfMonth();
            }

            $result = Report::where('tag', $type)
                ->whereBetween('generated_at', [$queryStart, $queryEnd])
                ->where('station_id', '=', $stationId)
                ->groupBy('generated_at')
                ->orderBy('generated_at')
                ->select([
                    'generated_at',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get();
            foreach ($result as &$row){
                switch($type){
                    case 'hourly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfHour()->toDateTimeString() . " - " . Carbon::parse($row->generated_at)->endOfHour()->toDateTimeString();
                        break;
                    case 'daily':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->toDateTimeString();
                        break;
                    case 'weekly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfWeek(Carbon::SATURDAY)->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfWeek(Carbon::FRIDAY)->toDateString();
                        break;
                    case 'monthly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfMonth()->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfMonth()->toDateString();
                        break;

                }
                $totalTimeDuration = $queryStart->diffInSeconds($queryEnd);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        }
    }

    public function getOEETableReportByStationProduct(Request $request){
        $stationProductId = $request->get('stationProductId');
        $start  = CarbonImmutable::parse($request->get('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->get('end'))->endOfDay();
        $type = $request->get('type');
        $type = ( is_null($type) || empty($type) ) ? 'hourly' : $type;

        if(is_null($stationProductId)){
            // Info: stationId null means all stations. In this case, there will be as many rows as stations and (availability,performance,quality,oee) fields will be from start and end duration only
            $result = Report::where('tag', 'hourly')
                ->whereBetween('generated_at', [$start, $end])
                ->groupBy(['product_id','station_id'])
                ->select([
                    'product_id',
                    'station_id',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get()->load('station.stationGroup', 'product.productGroup');
            foreach ($result as &$row){
                $station = $row->station;
                $row['station_name'] = $station->name;
                $row['station_group_id'] = $station->station_group_id;
                $row['station_group_name'] = $station->stationGroup->name;
                unset($row->station);
                $product = $row->product;
                $row['product_name'] = $product->name;
                $row['product_group_id'] = $product->product_group_id;
                $row['product_group_name'] = $product->productGroup->name;
                unset($row->product);
                $totalTimeDuration = $start->diffInSeconds($end);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationProduct = StationProduct::find($stationProductId);
            if($type === 'hourly' || $type === 'daily'){
                $queryStart = $start->startOfDay();
                $queryEnd = $end->endOfDay();
            } else if($type == 'weekly'){
                $queryStart = $start->startOfWeek(Carbon::SATURDAY);
                $queryEnd = $end->endOfWeek(Carbon::FRIDAY);
            } else {
                $queryStart = $start->startOfMonth();
                $queryEnd = $end->endOfMonth();
            }

            $result = Report::where('tag', $type)
                ->whereBetween('generated_at', [$queryStart, $queryEnd])
                ->where('product_id', '=', $stationProduct->product_id)
                ->where('station_id', '=', $stationProduct->station_id)
                ->groupBy('generated_at')
                ->orderBy('generated_at')
                ->select([
                    'generated_at',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get();
            foreach ($result as &$row){
                switch($type){
                    case 'hourly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfHour()->toDateTimeString() . " - " . Carbon::parse($row->generated_at)->endOfHour()->toDateTimeString();
                        break;
                    case 'daily':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->toDateTimeString();
                        break;
                    case 'weekly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfWeek(Carbon::SATURDAY)->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfWeek(Carbon::FRIDAY)->toDateString();
                        break;
                    case 'monthly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfMonth()->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfMonth()->toDateString();
                        break;

                }
                $totalTimeDuration = $queryStart->diffInSeconds($queryEnd);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        }
    }

    public function getOEETableReportByStationShift(Request $request){
        $stationShiftId = $request->get('stationShiftId');
        $start  = CarbonImmutable::parse($request->get('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->get('end'))->endOfDay();
        $type = $request->get('type');
        $type = ( is_null($type) || empty($type) ) ? 'hourly' : $type;

        if(is_null($stationShiftId)){
            // Info: stationId null means all stations. In this case, there will be as many rows as stations and (availability,performance,quality,oee) fields will be from start and end duration only
            $result = Report::where('tag', 'hourly')
                ->whereBetween('generated_at', [$start, $end])
                ->groupBy(['shift_id','station_id'])
                ->select([
                    'shift_id',
                    'station_id',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get()->load('station.stationGroup', 'shift');
            foreach ($result as &$row){
                $station = $row->station;
                $row['station_name'] = $station->name;
                $row['station_group_id'] = $station->station_group_id;
                $row['station_group_name'] = $station->stationGroup->name;
                unset($row->station);
                $shift = $row->shift;
                $row['shift_name'] = $shift->name;
                unset($row->shift);
                $totalTimeDuration = $start->diffInSeconds($end);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationShift = StationShift::find($stationShiftId);
            if($type === 'hourly' || $type === 'daily'){
                $queryStart = $start->startOfDay();
                $queryEnd = $end->endOfDay();
            } else if($type == 'weekly'){
                $queryStart = $start->startOfWeek(Carbon::SATURDAY);
                $queryEnd = $end->endOfWeek(Carbon::FRIDAY);
            } else {
                $queryStart = $start->startOfMonth();
                $queryEnd = $end->endOfMonth();
            }

            $result = Report::where('tag', $type)
                ->whereBetween('generated_at', [$queryStart, $queryEnd])
                ->where('shift_id', '=', $stationShift->shift_id)
                ->where('station_id', '=', $stationShift->station_id)
                ->groupBy('generated_at')
                ->orderBy('generated_at')
                ->select([
                    'generated_at',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get();
            foreach ($result as &$row){
                switch($type){
                    case 'hourly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfHour()->toDateTimeString() . " - " . Carbon::parse($row->generated_at)->endOfHour()->toDateTimeString();
                        break;
                    case 'daily':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->toDateTimeString();
                        break;
                    case 'weekly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfWeek(Carbon::SATURDAY)->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfWeek(Carbon::FRIDAY)->toDateString();
                        break;
                    case 'monthly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfMonth()->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfMonth()->toDateString();
                        break;

                }
                $totalTimeDuration = $queryStart->diffInSeconds($queryEnd);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        }
    }

    public function getOEETableReportByStationOperator(Request $request){
        $stationOperatorId = $request->get('stationOperatorId');
        $start  = CarbonImmutable::parse($request->get('start'))->startOfDay();
        $end = CarbonImmutable::parse($request->get('end'))->endOfDay();
        $type = $request->get('type');
        $type = ( is_null($type) || empty($type) ) ? 'hourly' : $type;

        if(is_null($stationOperatorId)){
            // Info: stationId null means all stations. In this case, there will be as many rows as stations and (availability,performance,quality,oee) fields will be from start and end duration only
            $result = Report::where('tag', 'hourly')
                ->whereBetween('generated_at', [$start, $end])
                ->groupBy(['operator_id','station_id'])
                ->select([
                    'operator_id',
                    'station_id',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get()->load('station.stationGroup', 'operator');
            foreach ($result as &$row){
                $station = $row->station;
                $row['station_name'] = $station->name;
                $row['station_group_id'] = $station->station_group_id;
                $row['station_group_name'] = $station->stationGroup->name;
                unset($row->station);
                $operator = $row->operator;
                $row['operator_name'] = $operator->first_name . " " . $operator->last_name;
                unset($row->operator);
                $totalTimeDuration = $start->diffInSeconds($end);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $stationOperator = StationOperator::find($stationOperatorId);
            if($type === 'hourly' || $type === 'daily'){
                $queryStart = $start->startOfDay();
                $queryEnd = $end->endOfDay();
            } else if($type == 'weekly'){
                $queryStart = $start->startOfWeek(Carbon::SATURDAY);
                $queryEnd = $end->endOfWeek(Carbon::FRIDAY);
            } else {
                $queryStart = $start->startOfMonth();
                $queryEnd = $end->endOfMonth();
            }

            $result = Report::where('tag', $type)
                ->whereBetween('generated_at', [$queryStart, $queryEnd])
                ->where('operator_id', '=', $stationOperator->operator_id)
                ->where('station_id', '=', $stationOperator->station_id)
                ->groupBy('generated_at')
                ->orderBy('generated_at')
                ->select([
                    'generated_at',
                    DB::raw('SUM(produced) as produced'),
                    DB::raw('SUM(scraped) as scraped'),
                    DB::raw('SUM(expected) as expected'),
                    DB::raw('SUM(available) as available'),
                    DB::raw('SUM(planned_downtime) as planned_downtime'),
                ])->get();
            foreach ($result as &$row){
                switch($type){
                    case 'hourly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfHour()->toDateTimeString() . " - " . Carbon::parse($row->generated_at)->endOfHour()->toDateTimeString();
                        break;
                    case 'daily':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->toDateTimeString();
                        break;
                    case 'weekly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfWeek(Carbon::SATURDAY)->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfWeek(Carbon::FRIDAY)->toDateString();
                        break;
                    case 'monthly':
                        $row['time_duration'] = Carbon::parse($row->generated_at)->startOfMonth()->toDateString() . " - " . Carbon::parse($row->generated_at)->endOfMonth()->toDateString();
                        break;

                }
                $totalTimeDuration = $queryStart->diffInSeconds($queryEnd);
                $row['availability'] = ($totalTimeDuration - $row->planned_downtime)<=0 ? 0 : $row->available / ($totalTimeDuration - $row->planned_downtime);
                $row['performance'] = $row->expected == 0 ? 0 : $row->produced / $row->expected;
                $row['quality'] = ($row->produced <= 0 || $row->produced < $row->scraped) ? 0 : ($row->produced - $row->scraped) / $row->produced;
                $row['oee'] = $row['availability'] * $row['performance'] *  $row['quality'];
            }
            return $result;
        }
    }
}
