<?php

namespace App\Http\Controllers;

use App\Data\Repositories\MachineStatusRepository;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    protected $deviceRepository;
    protected $machineStatusRepository;
    protected $packetRepository;

    public function __construct(MachineStatusRepository $machineStatusRepository)
    {
        $this->machineStatusRepository = $machineStatusRepository;
    }

    public function Index(Request $stationId): Builder
    {
        return $this->machineStatusRepository->findLatestMachineStatusByStationId($stationId);
    }
}
