<?php


namespace App\Data\Repositories;

use App\Data\Models\Operator;
use App\Data\Models\StationOperator;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;

class OperatorRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;

    public function fetchAllOperator($orderBy) {
        return Operator::orderBy('first_name', $orderBy)->with('stations')->get();
    }

    public function storeOperator($request) {
        $operator = new Operator();
        $operator['first_name'] = $request['first_name'];
        $operator['last_name'] = $request['last_name'];
        $operator['code'] = $request['code'];
        $check = $operator->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function updateOperator($request, $id) {
        $operator = Operator::find($id);
        $operator['first_name'] = $request['first_name'];
        $operator['last_name'] = $request['last_name'];
        $operator['code'] = $request['code'];
        $check = $operator->save();
        if ($check) {
            return true;
        }
        return false;
    }

    public function deleteOperator($id) {
        $operator = Operator::find($id);
        $check = $operator->delete();
        if ($check) {
            return true;
        }
        return false;
    }

    public function findAllOperatorsOfDeviceSortedGroupByStationId(int $deviceId)
    {
        $query = StationOperator::query();
        return $query->join('stations', 'stations.id', '=', 'station_operators.station_id')
                    ->join('device_stations', 'device_stations.station_id', '=', 'stations.id' )
                    ->where('device_stations.device_id', '=', $deviceId)
                    ->select('station_operators.*')
                    ->get()
                    ->sortBy('start_time')
                    ->groupBy('station_id');
    }

    public function findAllStationOperatorsGroupByStationId()
    {
        $query = StationOperator::query();
        return $query->get()
            ->sortBy('start_time')
            ->groupBy('station_id');
    }
}
