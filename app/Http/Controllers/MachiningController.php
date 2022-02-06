<?php

namespace App\Http\Controllers;

use App\Data\Models\MachineStatus;
use App\Data\Models\Stations;
use App\Exports\ExcelDataExport;

use Carbon\Carbon;

use App\Data\Repositories\MachiningRepository;

use Illuminate\Http\Request;
use DB;
use Excel;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
     * @return \Illuminate\Http\Response
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

        $data = $this->machiningRepository->fetchAllMachiningData($startTime, $endTime);

        $formattedData = $this->formatData($data);
        $headers = $this->getHeaders();
    
        return Excel::download(new ExcelDataExport($formattedData, $headers), 'Machining Report.xlsx');
    }

    public function formatData($data)
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

    public function getFormattedDataForExcel($data): array
    {
        $excel_data = [];
        foreach ($data as $key) {
            $excel_data[] = [
                "station_name"  => $key['station_name'],
                "program_name"  => $key['program_name'],
                "spindle_speed" => $key['spindle_speed'],
                "feed_rate"     => $key['feed_rate'],
                "Produced At"   => $key['produced_at'],
            ];
        }
        return $excel_data;
    }

}
