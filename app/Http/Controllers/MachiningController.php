<?php

namespace App\Http\Controllers;

use App\Data\Models\MachineStatus;
use App\Data\Models\Stations;

use Carbon\Carbon;

use App\Data\Repositories\MachiningRepository;

use Illuminate\Http\Request;
use DB;
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
        $produced_at = $request->input('produced_at');

        if (empty($produced_at))
        {
            $produced_at = Carbon::now()->toDateString();
        }

        $produced_at = Carbon::parse($produced_at);

        $start_of_day = $produced_at->copy()->startOfDay();
        $end_of_day = $produced_at->copy()->endOfDay();

        $machiningData = $this->machiningRepository->fetchAllMachiningData($start_of_day, $end_of_day);
        return response()->json($machiningData);
    }

}
