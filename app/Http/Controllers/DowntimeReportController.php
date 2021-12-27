<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\StationOperator;
use App\Data\Models\StationProduct;
use App\Data\Models\StationShift;
use App\Data\Repositories\DowntimeReportRepository;
use App\Exports\ExcelDataExport;
use Carbon\CarbonImmutable;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Excel;

class DowntimeReportController extends Controller
{
    /**
     * @var DowntimeReportRepository
     */
    private $downtimeReportRepository;

    private $headers_with_selected = ['Downtime Reason', "Downtime Reason Group", "Downtime Type", "Count", "Duration", "Stop%"];
    private $headers_all           = ['Station Name', 'Station Group', "Count", "Duration", "Stop%"];

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

    public function getDowntimeTableReportByStationTeam(Request $request)
    {
        return $this->downtimeReportRepository->getDowntimeTableReportByStationTeam($request);
    }


    public function getDowntimeTableReportByStationExcel(Request $request)
    {
        $data       = $this->downtimeReportRepository->getDowntimeTableReportByStation($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationId', []);
        $headers    = $this->getHeaders($request, 'stationId', []);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Station Report.xlsx');
    }

    public function getDowntimeTableReportByStationProductExcel(Request $request)
    {
        $data       = $this->downtimeReportRepository->getDowntimeTableReportByStationProduct($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationProductId', ['product_name', 'product_group_name']);
        $headers    = $this->getHeaders($request, 'stationProductId', ['Product', 'Product Group']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Product Report.xlsx');
    }

    public function getDowntimeTableReportByStationShiftExcel(Request $request)
    {
        $data       = $this->downtimeReportRepository->getDowntimeTableReportByStationShift($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationShiftId', ['shift_name']);
        $headers    = $this->getHeaders($request, 'stationShiftId', ['Shift']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Shift Report.xlsx');

    }

    public function getDowntimeTableReportByStationOperatorExcel(Request $request): BinaryFileResponse
    {
        $data       = $this->downtimeReportRepository->getDowntimeTableReportByStationOperator($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationOperatorId', ['operator_name']);
        $headers    = $this->getHeaders($request, 'stationOperatorId', ['Operator']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Station Report.xlsx');
    }


    public function getDowntimeTableReportByStationTeamExcel(Request $request): BinaryFileResponse
    {
        $data       = $this->downtimeReportRepository->getDowntimeTableReportByStationTeam($request);
        $excel_data = $this->getFormattedDataForExcel($data, $request, 'stationTeamId', ['team_name']);
        $headers    = $this->getHeaders($request, 'stationTeamId', ['Team']);
        return Excel::download(new ExcelDataExport($excel_data, $headers), 'Team Report.xlsx');
    }

    public function getHeaders(Request $request, $key_to_check_for, $items_to_add): array
    {
        if ($request->get($key_to_check_for)) {
            $headers = $this->headers_with_selected;
        }
        else {
            $headers = $this->headers_all;
            foreach (array_reverse($items_to_add) as $item) {
                array_unshift($headers, $item);
            }
        }
        return $headers;
    }

    public function getFormattedDataForExcel($data, Request $request, $keyToCheckFor, $keysToSelect): array
    {
        $excel_data = [];
        Log::debug($request->get($keyToCheckFor));
        if ($request->get($keyToCheckFor)) {
            foreach ($data as $datum) {
                $excel_data[] = [
                    "name"              => $datum['name'],
                    "reason_group_name" => $datum['reason_group_name'],
                    "type"              => $datum['type'],
                    "count"             => $datum['count'],
                    "duration"          => $this->formatDuration($datum['duration']),
                    "stop_percent"      => number_format($datum['stop_percent'] * 100, 2) . "%",
                ];
            }
        }
        else {
            foreach ($data as $datum) {
                $row = [];
                foreach ($keysToSelect as $key) {
                    $row[$key] = $datum[$key];
                }
                $row["station_name"]       = $datum['station_name'];
                $row["station_group_name"] = $datum['station_group_name'];
                $row["count"]              = $datum['count'];
                $row["duration"]           = $this->formatDuration($datum['duration']);
                $row["stop_percent"]       = number_format($datum['stop_percent'] * 100, 2) . "%";
                $excel_data[]              = $row;
            }
        }
        return $excel_data;
    }

    public function formatDuration($sec_num): string
    {
        $hours   = floor($sec_num / 3600);
        $minutes = floor(($sec_num - ($hours * 3600)) / 60);
        $seconds = $sec_num - ($hours * 3600) - ($minutes * 60);

        if ($hours < 10) {
            $hours = "0" . $hours;
        }
        if ($minutes < 10) {
            $minutes = "0" . $minutes;
        }
        if ($seconds < 10) {
            $seconds = "0" . $seconds;
        }
        return $hours . ':' . $minutes . ':' . $seconds;
    }

}
