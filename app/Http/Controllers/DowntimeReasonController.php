<?php

namespace App\Http\Controllers;

use App\Data\Models\Downtime;
use App\Data\Models\DowntimeReason;
use App\Data\Repositories\DowntimeReasonRepository;
use App\Http\Requests\DowntimeReasonCreateRequest;
use App\Http\Resources\DowntimeLineData;
use App\Http\Resources\DowntimeReasonCollection;
use App\Http\Resources\DowntimeReasonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class DowntimeReasonController extends Controller
{
    /**
     * @var DowntimeReasonRepository
     */
    private $downtimeReasonRepository;

    public function __construct(DowntimeReasonRepository $downtimeReasonRepository) {
        $this->downtimeReasonRepository = $downtimeReasonRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return DowntimeReasonCollection
     */
    public function index()
    {
        $downtimeReasons = DowntimeReason::all();
        $downtimeReasons->load('downtimeReasonGroup');
        return new DowntimeReasonCollection($downtimeReasons);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(DowntimeReasonCreateRequest $request)
    {
        $checkStore = $this->downtimeReasonRepository->storeDowntimeReason($request);
        if(!$checkStore) throw new BadRequestException();
        $downtimeReasons = $this->downtimeReasonRepository->fetchAllDowntimeReasonsByGroup($request['reason_group_id'], $orderBy = 'asc');
        return new DowntimeReasonCollection($downtimeReasons);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new DowntimeReasonResource(DowntimeReason::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return DowntimeReasonCollection
     */
    public function update(Request $request, $id)
    {
        $checkUpdate = $this->downtimeReasonRepository->updateDowntimeReason($request, $id);
        if(!$checkUpdate) throw new BadRequestException();
        $downtimeReasons = $this->downtimeReasonRepository->fetchAllDowntimeReasonsByGroup($request['reason_group_id'], $orderBy = 'asc');
        return new DowntimeReasonCollection($downtimeReasons);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return DowntimeReasonCollection
     */
    public function destroy($id)
    {
        $downtimeReason = DowntimeReason::find($id);
        $checkDelete = $this->downtimeReasonRepository->deleteDowntimeReason($id);
        if(!$checkDelete) throw new BadRequestException();
        $downtimeReasons = $this->downtimeReasonRepository->fetchAllDowntimeReasonsByGroup($downtimeReason['reason_group_id'], $orderBy = 'asc');
        return new DowntimeReasonCollection($downtimeReasons);
    }

    public function assignDowntimeReason(Request $request) {
        $downtime = Downtime::find($request->input('downtimeId'));

        if (empty($downtime)) {
            return response()->json(["code" => 404], 404);
        }

        $downtime->reason_id = $request->input('downtimeReasonId');
        $downtime->save();

        return new DowntimeLineData($downtime);
    }

    public function removeDowntimeReason(Request $request) {
        $downtime = Downtime::find($request->input('downtimeId'));

        if (empty($downtime)) {
            return response()->json(["code" => 404], 404);
        }

        $downtime->reason_id = null;
        $downtime->save();

        return new DowntimeLineData($downtime);
    }
}
