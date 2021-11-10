<?php

namespace App\Http\Controllers;

use App\Data\Models\Scrap;
use App\Data\Models\Shift;
use App\Data\Models\Product;
use App\Data\Models\Station;
use App\Data\Models\StationProduct;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ScrapRepository;
use App\Exceptions\BadRequestException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class ScrapController extends Controller
{
    /**
     * @var ScrapRepository
     */
    private $scrapRepository;

    public function __construct(ScrapRepository $scrapRepository)
    {
        $this->scrapRepository = $scrapRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $query = Scrap::when($request->date, function (EloquentBuilder $q, $date) {
            $q->whereDate('scraps.date', '=', Carbon::parse($date)->toDateString());
        });

        $query->when($request->hour, function (EloquentBuilder $q, $hour) {
            $q->where('scraps.hour', '=', $hour);
        });

        $query->when($request->productId, function (EloquentBuilder $q, $productId) {
            $q->where('scraps.product_id', '=', $productId);
        });

        $query->when($request->stationId, function (EloquentBuilder $q, $stationId) {
            $q->where('scraps.station_id', '=', $stationId);
        });

        $query->when($request->shiftId, function (EloquentBuilder $q, $shiftId) {
            $q->where('scraps.shift_id', '=', $shiftId);

        });
        //TODO implement actual operator id
//
//        $query = Scrap::when($request->operatorId,function (EloquentBuilder $q,$operatorId){
//            $q->where('scraps.operator_id','=',$operatorId);
//        });

        $scraps = $query->get();

        $scraps = $scraps->groupBy(function ($scrap) {
            return $scrap->hour;
        });


        return response()->json($scraps, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return void
     */
    public function create(Request $request)
    {


    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $scrap = new Scrap();
        $scrap->product_id = $data['productId'];
        $scrap->station_id = $data['stationId'];
        $date = Carbon::parse($data['date']);
        $datetime = $date->setHour($data['hour']);
        /**
         * @var $shift Shift
         */
        $shift = Shift::where('start_time', '<=', $datetime->toTimeString())
            ->where('end_time', '>=', $datetime->toTimeString())
            ->first();
        $scrap->shift_id = $shift->id;
        $scrap->hour = $data['hour'];
        $scrap->date = $date->toDateString();
        $scrap->value = $data['value'];
        $scrap->created_by = $request->user()->id;
        // TODO insert operator id here: search it from the station - operator join table
        $scrap->save();
        return response()->json([], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Scrap::find($id), 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $scrap = Scrap::find($id);
        $data = $request->input();
        $scrap['product_id'] = $request->$data['productId'];
        $scrap['station_id'] = $request->$data['stationId'];
        //TODO get shift from operator station
        $shift = Shift::where('start_time', '<=', Carbon::now()->toTimeString())
            ->where('end_time', '>=', Carbon::now()->toTimeString())->first();
        $scrap['shift_id'] = $shift->id;
        $scrap['hour'] = $data['hour'];
        $scrap['date'] = $data['date'];
        $scrap->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scrap = Scrap::find($id);
        $scrap->delete();
        return response()->json([], 200);
    }

    public function uploadHourlyScrapCount(Request $request)
    {
        $data = collect($request->input());
        $scraps = Scrap::where('date', '=', ((object)$data[0])->date)
            ->where('station_id', '=', ((object)$data[0])->stationId)
            ->whereIn('hour', $data->pluck('hour'))
            ->whereIn('product_id', $data->pluck('id'))
            ->get()
            ->groupBy(['hour','product_id']);

        $data->each(function($reqScrap) use ($scraps, $request) {
            $reqScrap = (object)$reqScrap;
            $date = Carbon::parse($reqScrap->date);
            $datetime = $date->setHour($reqScrap->hour);
            $reqHour = $reqScrap->hour;
            $reqProductId = $reqScrap->id;

            //Todo DB call inside a loop is an inexcusable sin, but sadly we are under a deadline
            $logCount = app(ProductionLogRepository::class)->fetchProductionLogCountOfHour($reqScrap->stationId, $reqScrap->id, $date->copy());
            if($reqScrap->scraped > $logCount) throw new BadRequestException('Defect entry cannot be more than logs');

            $shift = Shift::where('start_time', '<=', $datetime->toTimeString())
                ->where('end_time', '>=', $datetime->toTimeString())
                ->first();

            if(empty($scraps[$reqHour]) || empty($scraps[$reqHour][$reqProductId])){
                $scrap = new Scrap();
                $scrap->created_by = $request->user()->id;
                $scrap->value = $reqScrap->scraped;
                $scrap->date = $date;
                $scrap->hour = $reqHour;
                $scrap->product_id = $reqProductId;
                $scrap->station_id = $reqScrap->stationId;
                $scrap->shift_id = $shift->id?? null;
            } else {
                $scrap = $scraps[$reqHour][$reqProductId][0];
                $scrap->value = $reqScrap->scraped;
            }
            $scrap->save();
        });
        return $scraps;
    }
}
