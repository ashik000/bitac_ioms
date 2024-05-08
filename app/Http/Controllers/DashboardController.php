<?php

namespace App\Http\Controllers;

use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\ProductRepository;
use DateTime;
use Illuminate\Http\Request;
use App\Data\Models\MachineStatus;

class DashboardController extends Controller
{
    protected $machineStatusRepository;
    protected $productRepository;

    public function __construct(MachineStatusRepository $machineStatusRepository, ProductRepository $productRepository)
    {
        $this->machineStatusRepository = $machineStatusRepository;
        $this->productRepository = $productRepository;
    }

    public function getMachineStatus(Request $request)
    {
        $stationId = $request['station_id'];
        $machineStatus = $this->machineStatusRepository->findLatestMachineStatusByStationId($stationId);
        $programStatus = false;
        $productSelected = $this->productRepository->findRunningProductOfStationByStationId($stationId);
        if(!empty($productSelected))
        {
            if($machineStatus->program_name !== $productSelected->name)
            {
                $programStatus = true;
            }
        }
        $lastStop = MachineStatus::query()
            ->where('power_status', '=', 'STOPPED')
            ->orderBy('produced_at', 'DESC')
            ->first();

        $diff = '00:00:00';
        if(!empty($lastStop)) {
            $lastStart = MachineStatus::query()
                ->where('power_status', '=', 'ACTIVE')
                ->where('produced_at', '>', $lastStop['produced_at'])
                ->orderBy('produced_at')->first();

            if(!empty($lastStart)) {
                $startTime = new DateTime($lastStart['produced_at']);
                $dt = new DateTime();
                $diff = $startTime->diff($dt);
                $diff = $diff->format('%H:%I:%S');
            }
        }

        $machineStatus->machine_uptime = $diff;

        return [
            'machineStatus' => $machineStatus,
            'programStatus' => $programStatus
        ];
    }
}
