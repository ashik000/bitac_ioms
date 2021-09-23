<?php

namespace App\Console\Commands;

use App\Data\Models\Device;
use App\Data\Models\DeviceStation;
use App\Data\Models\Station;
use Illuminate\Console\Command;

class AssignDeviceToStation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:map-device {--I|identifier=} {--P|port=} {--S|Station=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $identifier = $this->option('identifier');
        $stationName = $this->option('station');
        $port = $this->option('port');

        $device = Device::where('identifier', $identifier)->first();
        if(empty($device)) {
            error_log('Device not found');
            return -1;
        }
        $station = Station::where('name', $stationName)->first();
        if(empty($station)) {
            error_log('Station not found');
            return -1;
        }
        $deviceStation = DeviceStation::where('device_id', $device->id)
                                        ->where('station_id', $station->id)
                                        ->where("port", $port)
                                        ->update([
                                            'deleted_at' => now()
                                        ]);
        $deviceStation = new DeviceStation();
        $deviceStation->device_id = $device->id;
        $deviceStation->station_id = $station->id;
        $deviceStation->port = $port;
        $deviceStation->save();
        error_log('Device mapped successfully');
        return 0;
    }
}
