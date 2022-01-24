<?php

namespace App\Http\Controllers;

use App\Data\Repositories\MachineStatusRepository;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Data\Models\MachineStatus;

class DashboardController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;

    public function __construct(MachineStatusRepository $machineStatusRepository)
    {
        $this->machineStatusRepository = $machineStatusRepository;
    }

    public function GetMachineStatus(Request $request)
    {
        $machineStatus = $this->machineStatusRepository->findLatestMachineStatusByStationId(7);
        return $machineStatus;
    }
}
