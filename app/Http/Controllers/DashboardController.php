<?php

namespace App\Http\Controllers;

use App\Data\Repositories\MachineStatusRepository;
use App\Data\Repositories\ProductRepository;
use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Data\Models\MachineStatus;

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
        return [
            'machineStatus' => $machineStatus,
            'programStatus' => $programStatus
        ];
    }
}
