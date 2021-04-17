<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Http\Resources\DowntimeLineData;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Data\Repositories\ProductionLogRepository;

class DowntimeController extends Controller
{
    public function getDowntimeSummary(Request $request)
    {
        $requestDate = $request->input('date');
        $stationId = $request->input('stationId');

        $downtimes = Downtime::query()
                            ->join('production_logs', 'production_logs.id', '=', 'downtimes.production_log_id')
                            ->where('production_logs.station_id', '=', $stationId)
                            ->whereDate('start_time', Carbon::parse($requestDate)->toDateString())
                            ->select('downtimes.*')
                            ->get();

        $downtimes->load('reason.downtimeReasonGroup');

        return DowntimeLineData::collection($downtimes);
    }

    public function testMyFunctions(Request $request, ProductionLogRepository $productionLogRepository){
        return $productionLogRepository->fetchDowntimes([
            'between' => [
                'start' => CarbonImmutable::parse($request->start),
                'end'   => CarbonImmutable::parse($request->end),
            ],
        ]);
    }
}
