<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\DowntimeReportRepository;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DowntimeReportController extends Controller
{
    /**
     * @var DowntimeReportRepository
     */
    private $downtimeReportRepository;

    public function __construct(DowntimeReportRepository $downtimeReportRepository)
    {
        $this->downtimeReportRepository = $downtimeReportRepository;
    }

    public function getDowntimeTableReportByStation(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStation($request);
    }

    public function getDowntimeTableReportByStationProduct(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationProduct($request);
    }

    public function getDowntimeTableReportByStationShift(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationShift($request);
    }

    public function getDowntimeTableReportByStationOperator(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationOperator($request);
    }

}
