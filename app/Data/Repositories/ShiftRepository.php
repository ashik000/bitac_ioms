<?php


namespace App\Data\Repositories;

use App\Data\Models\Downtime;
use App\Data\Models\Shift;
use App\Data\Models\StationShift;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;
use Illuminate\Support\Facades\DB;

class ShiftRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public const DAY_TO_NAME_MAP = [
            0 => "SUN",
            1 => "MON",
            2 => "TUE",
            3 => "WED",
            4 => "THU",
            5 => "FRI",
            6 => "SAT",
    ];

    public function findShiftsOfStation()
    {
        $query = StationShift::query();
        $query
            ->join('shifts', 'shifts.id', '=', 'station_shifts.shift_id')
            ->leftJoin('stations', 'stations.id', '=', 'station_shifts.station_id')
            ->select([
                DB::raw('shifts.id as shift_id'),
                DB::raw('shifts.name as shift_name'),
                DB::raw('shifts.start_time as shift_start_time'),
                DB::raw('shifts.end_time as shift_end_time'),
            ]);

        $result = $query->distinct()->get();

        if (empty($result)) {
            return null;
        }

        return $result;
    }

    public function findAllShiftsOfDeviceGroupByStationId(int $deviceId)
    {
        $query = StationShift::query();
        return $query
                ->join('shifts', 'shifts.id', '=', 'station_shifts.shift_id')
                ->join('stations', 'stations.id', '=', 'station_shifts.station_id')
                ->join('device_stations', 'device_stations.station_id', '=', 'stations.id')
                ->where('device_stations.device_id', '=', $deviceId)
                ->select(['shifts.*', 'station_shifts.station_id', 'station_shifts.schedule'])
                ->get()
                ->sortBy('start_time')
                ->groupBy('station_id');
    }

    public function fetchShiftDetails($stationId, $shiftId)
    {
        $query = StationShift::query();
        $query->leftJoin('stations', 'stations.id', '=', 'station_shifts.station_id')
            ->leftjoin('shifts', 'shifts.id', '=', 'station_shifts.shift_id')
            ->where([['station_shifts.station_id', '=', $stationId], ['station_shifts.shift_id', '=', $shiftId]])
            ->select([
                DB::raw('shifts.id as shift_id'),
                DB::raw('shifts.name as shift_name'),
                DB::raw('shifts.start_time'),
                DB::raw('shifts.end_time'),
            ]);

        $result = $query->get();
        return $result;
    }

}
