<?php

namespace App\Http\Controllers;

use App\Exports\ExcelDataExport;
use Carbon\Carbon;
use App\Data\Repositories\MachiningRepository;

use Illuminate\Http\Request;
use DB;
use Excel;


class MachiningController extends Controller
{
    /**
     * @var MachiningRepository
     */
    private $machiningRepository;

    public function __construct(MachiningRepository $machiningRepository)
    {
        $this->machiningRepository = $machiningRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $startTime = Carbon::parse($data['start_time'])->startOfDay();
        $endTime = Carbon::parse($data['end_time'])->endOfDay();
        $machiningData = $this->machiningRepository->fetchAllMachiningData($startTime, $endTime);
        return response()->json($machiningData);
    }

    public function getMachiningDataExcel(Request $request)
    {
        $data = $request->all();
        $startTime = Carbon::parse($data['start_time'])->startOfDay();
        $endTime = Carbon::parse($data['end_time'])->endOfDay();

        $data = $this->machiningRepository->fetchAllMachiningDataExcel($startTime, $endTime);

        $formattedData = $this->formatData($data);
        $headers = $this->getHeaders();

        return Excel::download(new ExcelDataExport($formattedData, $headers), 'Machining Report.xlsx');
    }

    public function formatData($data): array
    {
        $formattedData = [];
        foreach ($data as $key => $value) {
            $formattedData[$key] = [
                'station_name' => $value->station_name,
                'program_name' => $value->program_name,
                'power_status' => $value->power_status,
                'spindle_speed' => $value->spindle_speed,
                'spindle_speed_active' => $value->spindle_speed_active,
                'feed_rate' => $value->feed_rate,
                'feed_rate_active' => $value->feed_rate_active,
                'cycle_time' => $value->cycle_time,
                'production_counter1' => $value->production_counter1,
                'production_counter2' => $value->production_counter2,
                'machining_mode' => $value->machining_mode,
                'tool_life' => $value->tool_life,
                'load_on_table' => $value->load_on_table,
                'produced_at' => $value->produced_at,
            ];
        }
        return $formattedData;
    }

    public function getHeaders(): array
    {
        $headers = [
            "Station Name",
            "Program Name",
            "Power Status",
            "Spindle Speed",
            "Spindle Speed Active",
            "Feed Rate",
            "Feed Rate Active",
            "This Cycle",
            "M30 Counter 1",
            "M30 Counter 2",
            "Machining Mode",
            "Tool Life",
            "Load On",
            "Produced At"
        ];
        return $headers;
    }

}
