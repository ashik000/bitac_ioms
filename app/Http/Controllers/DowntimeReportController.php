<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\DowntimeReportRepository;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DowntimeReportController extends Controller
{
    /**
     * @var DowntimeReportRepository
     */
    private $downtimeReportRepository;

    public function __construct(DowntimeReportRepository $downtimeReportRepository)
    {
        $this->downtimeReportRepository = $downtimeReportRepository;
    }

    public function getDowntimeTableReportByStation(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStation($request);
    }

    public function getDowntimeTableReportByStationProduct(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationProduct($request);
    }

    public function getDowntimeTableReportByStationShift(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationShift($request);
    }

    public function getDowntimeTableReportByStationOperator(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationOperator($request);
    }

    /**
     * testing function
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function testDowntime()
    {
        $stationId = 1;
        $start = CarbonImmutable::parse('2019-09-01');
        $end = CarbonImmutable::parse('2019-09-30');

        if (is_null($stationId)) {
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('stations', 'stations.id', '=', 'production_logs.station_id')
                ->leftjoin('station_groups', 'station_groups.id', '=', 'stations.station_group_id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->groupBy([DB::raw('DATE(downtimes.start_time)'),'downtime_reasons.type','station_id','station_group_id','station_name','stations.station_group_id','station_group_name'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    'station_id',
                    'station_group_id',
                    DB::raw('stations.name as station_name'),
                    DB::raw('stations.station_group_id as station_group_id'),
                    DB::raw('station_groups.name as station_group_name'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date')
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
            return response()->json($result, 200);
        } else {
            // Info: one single station is selected. In this case, we will serve hourly/daily/weekly/monthly data to the table.
            $result = Downtime::query()
                ->join('production_logs', 'downtimes.production_log_id', '=', 'production_logs.id')
                ->leftJoin('downtime_reasons', 'downtimes.reason_id', '=', 'downtime_reasons.id')
                ->whereBetween('start_time', [$start->startOfDay(), $end->endOfDay()])
                ->where('station_id', '=', $stationId)
                ->groupBy([DB::raw('DATE(downtimes.start_time)'), 'downtime_reasons.type'])
                ->orderBy(DB::raw('DATE(downtimes.start_time)'), 'asc')
                ->select([
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(duration) as duration'),
                    DB::raw('DATE(downtimes.start_time) as date'),
                    DB::raw('downtime_reasons.type as reason_type'),
                ])->get();
            $totalSum = $result->sum('duration');
            foreach ($result as &$row) {
//                if(is_null($row['reason_id'])){
//                    $row['name'] = 'Uncommented';
//                    $row['reason_group_name'] = ' - ';
//                    $row['type'] = ' - ';
//                }
//                unset($row->downtimeReasonGroup);
                $row['stop_percent'] = $row['duration'] / $totalSum;
            }
//            return $result;
            return response()->json($result, 200);
        }

    }

}
