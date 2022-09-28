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
                'spindle_speed' => $value->spindle_speed,
                'feed_rate' => $value->feed_rate,
                'produced_at' => $value->produced_at,
            ];
        }
        return $formattedData;
    }

    public function getHeaders(): array
    {
        $headers = ["Station Name", "Program Name", "Spindle Speed", "Feed Rate", "Produced At"];
        return $headers;
    }

}
