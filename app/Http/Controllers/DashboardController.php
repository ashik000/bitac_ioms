<?php

namespace App\Http\Controllers;

use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\ProductRepository;
use DateTime;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Data\Models\MachineStatus;
use mysql_xdevapi\Table;
use Illuminate\Support\Facades\Log;
use function Psy\debug;

class DashboardController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;
    protected $productRepository;

    public function __construct(MachineStatusRepository $machineStatusRepository, ProductRepository $productRepository)
    {
        $this->machineStatusRepository = $machineStatusRepository;
        $this->productRepository = $productRepository;
    }

    public function GetMachineStatus(Request $request)
    {
        $machineStatus = $this->machineStatusRepository->findLatestMachineStatusByStationId(7);
        $programStatus = false;
        $productSelected = $this->productRepository->findRunningProductOfStationByStationId(7);
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
