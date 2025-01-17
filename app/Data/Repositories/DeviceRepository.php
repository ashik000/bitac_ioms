<?php


namespace App\Data\Repositories;

use App\Data\Models\Device;
use App\Data\Models\DeviceStation;
use App\Data\Repositories\Interfaces\PaginatedResultInterface;
use App\Data\Repositories\Interfaces\RawQueryBuilderOutputInterface;
use App\Data\Repositories\Traits\PaginatedOutputTrait;
use App\Data\Repositories\Traits\ProcessOutputTrait;
use App\Data\Repositories\Traits\RawQueryBuilderOutputTrait;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Log;


class DeviceRepository implements PaginatedResultInterface, RawQueryBuilderOutputInterface
{
    use ProcessOutputTrait, PaginatedOutputTrait, RawQueryBuilderOutputTrait;



    public function findByIdentifier(string $identifier)
    {
        $device = Device::where('identifier', '=', $identifier);

        $device = $device->first();

        if (empty($device)) {
            return null;
        }
        return $device;
    }

    public function findDeviceStationByDeviceAndPort(int $deviceId, int $port)
    {
        $deviceStation = DeviceStation::where('device_id', '=', $deviceId)
                                        ->where('port', '=', $port)->first();

        return empty($deviceStation)? null : $deviceStation;
    }

    public function findAllDeviceStationsOfADevice($deviceId) {
        return DeviceStation::where('device_id', '=', $deviceId)->get();
    }

    public function batchInsertDevice(array $devices)
    {
        Device::insert($devices);
    }
    public function getAllDeviceIdentifiers()
    {
        $devices = Device::join('device_stations', 'device_stations.device_id', '=', 'devices.id')
            ->select('devices.identifier', 'device_stations.station_id')
            ->groupBy('devices.identifier')
            ->groupBy('device_stations.station_id')
            ->whereNull('device_stations.deleted_at')
            ->get();
        return $devices;
    }
}
