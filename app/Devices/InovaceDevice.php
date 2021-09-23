<?php


namespace App\Devices;

use App\Data\Models\Device;
use App\Data\Models\DeviceStation;
use App\Data\Models\Downtime;
use App\Data\Models\Operator;
use App\Data\Models\Packet;
use App\Data\Models\ProductionLog;
use App\Data\Models\Shift;
use App\Data\Models\SlowProduction;
use App\Data\Models\StationOperator;
use App\Data\Models\StationShift;
use App\Data\Repositories\DeviceRepository;
use App\Data\Repositories\OperatorRepository;
use App\Data\Repositories\ProductionLogRepository;
use App\Data\Repositories\ProductRepository;
use App\Data\Repositories\ShiftRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use DB;
use Log;


class InovaceDevice
{

    protected $deviceRepository;
    protected $productRepository;
    protected $productionLogRepository;
    protected $shiftRepository;
    protected $operatorRepository;

    public function __construct(DeviceRepository $deviceRepository,
                                ProductRepository $productRepository,
                                ProductionLogRepository $productionLogRepository,
                                ShiftRepository $shiftRepository,
                                OperatorRepository $operatorRepository)
    {
        $this->deviceRepository = $deviceRepository;
        $this->productRepository = $productRepository;
        $this->productionLogRepository = $productionLogRepository;
        $this->shiftRepository = $shiftRepository;
        $this->operatorRepository = $operatorRepository;
    }


    public function parseAndSaveProductionLogs($packet) : ParsedProductionLogPacket
    {
        /**
         * Log Packet Protocol
         *
         * The first 10 bytes of the log packet will be the device identifier timestamp
         *
         * Each log packet will be 7 bytes, 6 bytes timestamp, 1 byte line id
         *
         * There will be a terminating byte at the end (0xff)
         *
         */

        $perDataPacketSize = 7;
        $headerSize = 10;
        $deviceIdentifier = '0001'; //todo hardcoded for now, will be included in the packet later

        $packet->processing_start = now();
        $content = hex2bin($packet->request);

        $packetLength = strlen($content);
        $numPackets = ($packetLength-($headerSize+1))/$perDataPacketSize;
        $timestamp = Carbon::minValue();

        try {
            $parts  = unpack("Vdevice/Cyear/Cmonth/Cdate/Chour/Cminute/Csecond/", substr($content, 0, $headerSize));
            $deviceIdentifier = sprintf("%04u", $parts['device']);
            $timestamp = $this->buildTimestamp($parts);
        } catch (Exception $ex) {
            Log::error($ex);
            $parsedProductionLogPacket = new ParsedProductionLogPacket($deviceIdentifier, $timestamp, $numPackets);
            $parsedProductionLogPacket->setPayload([], [], [], [], $ex);
            return $parsedProductionLogPacket;
        }

        $parsedProductionLogPacket = new ParsedProductionLogPacket($deviceIdentifier, $timestamp, $numPackets);

        $productionLogs = [];
        $downTimes = [];
        $slowProductions = [];
        $parseErrors = [];

        $device = $this->deviceRepository->findByIdentifier($deviceIdentifier);

        if ($device == null) {
            $parsedProductionLogPacket->setException(new Exception("Device not found"));
            return $parsedProductionLogPacket;
        }

        $devicePortToDeviceStationMap = $this->deviceRepository->findAllDeviceStationsOfADevice($device->id)->mapWithKeys(function ($deviceStation) {
            return [$deviceStation['port'] + 1 => $deviceStation]; // 1 is added to port
        });
        $stationIdToStationProductMap = $this->productRepository->findAllStationProductsKeyByStationId();
        $productIdToProductMap = $this->productRepository->findAllProductsKeyById();
//        $stationIdToStationShiftMap = StationShift::all()->keyBy('station_id');

        $downtimes = [];
        $slowProductions = [];

        for ($i = 0; $i < $numPackets; $i++) {
            $parts = unpack("Cyear/Cmonth/Cdate/Chour/Cminute/Csecond/Cport/", substr($content, $headerSize+($i*$perDataPacketSize), $perDataPacketSize));
            $logTimestamp = $this->buildTimestamp($parts);
            $port = sprintf("%01u", $parts['port']);

            $deviceStation = $devicePortToDeviceStationMap->get($port+1);
            $stationProduct = $stationIdToStationProductMap->get($deviceStation->station_id);
            $product = $productIdToProductMap->get($stationProduct->product_id);

            $shiftIds = StationShift::where('station_id', '=', $deviceStation->station_id)->get()->pluck('shift_id');
//            $shiftIds = $stationIdToStationShiftMap->get($deviceStation->station_id)->pluck('shift_id');
//            $operatorIds = StationOperator::where('station_id', '=', $deviceStation->station_id)->get()->pluck('operator_id');

            $previousProductionLog = $this->productionLogRepository->findLastProductionLogByStationIdAndProductId($deviceStation->station_id, $product->id);
            $parsedProductionLogPacket->setCycleTime($stationProduct->cycle_time);

            if ($previousProductionLog == null) {
                $previousProductionLog = new ProductionLog();
                $previousProductionLog->produced_at = $logTimestamp->copy()->startOfHour();
            }

            $productionLog = new ProductionLog();
            $productionLog->station_id = $deviceStation->station_id;
            $productionLog->product_id = $product->id;
            $productionLog->produced_at = $logTimestamp;
            $productionLog->synced_at = Carbon::now();
            $productionLog->cycle_interval = $cycleInterval = $logTimestamp->diffInSeconds($previousProductionLog->produced_at);
            $productionLog->save();

            if (($logTimestamp->copy()->startOfHour()->diffInHours(Carbon::parse($previousProductionLog->produced_at)->startOfHour()) >= 2)) {
//                $downtimePrev = new Downtime();
//                $downtimePrev->start_time = Carbon::parse($previousProductionLog->produced_at);
//                $downtimePrev->duration = $downtimePrev->start_time->copy()->endOfHour()->diffInSeconds($downtimePrev->start_time);
//                $downtimePrev->production_log_id = $productionLog->id;

                $downtimePrevStartTime = Carbon::parse($previousProductionLog->produced_at);
                $downtimePrevDuration = $downtimePrevStartTime->copy()->endOfHour()->diffInSeconds($downtimePrevStartTime);
                $shift = Shift::whereIn('id', $shiftIds)
                    ->where('start_time', '<=', $downtimePrevStartTime)
                    ->where('end_time', '>=', $downtimePrevStartTime->copy()->addSeconds($downtimePrevDuration))->first();


                $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                    ->where('start_time', '<=', $downtimePrevStartTime)
                    ->where('end_time', '>=', $downtimePrevStartTime->copy()->addSeconds($downtimePrevDuration))
                    ->orWhere('end_time', '=', null)
                    ->first();
                $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;

//                $downtimePrev->shift_id = $shift->id?? null;
//                $downtimePrev->operator_id = $operator->id?? null;
//                $downtimePrev->save();

                array_push($downTimes, [
                    'start_time'        => $downtimePrevStartTime,
                    'duration'          => $downtimePrevDuration,
                    'production_log_id' => $productionLog->id,
                    'shift_id'          => $shift->id?? null,
                    'operator_id'       => $operator->id?? null,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);

                $hour = $downtimePrevStartTime->copy()->addHours(1)->addSeconds(-10);

                for ($j = 0; $j < (($logTimestamp->copy()->startOfHour()->diffInHours(Carbon::parse($previousProductionLog->produced_at)->startOfHour()))-1); $j++) {
//                    $downtime = new Downtime();
//                    $downtime->start_time = $hour->copy()->startOfHour();
//                    $downtime->duration = 3600;
//                    $downtime->production_log_id = $productionLog->id;

                    $downtimeStart = $hour->copy()->startOfHour();
                    $downtimeDuration = 3600;
                    $downtimeEnd = $downtimeStart->copy()->addSeconds($downtimeDuration);

                    $shift = Shift::whereIn('id', $shiftIds)
                        ->where('start_time', '<=', $downtimeStart)
                        ->where('end_time', '>=', $downtimeEnd)->first();

                    $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                        ->where('start_time', '<=', $downtimeStart)
                        ->where('end_time', '>=', $downtimeEnd)
                        ->orWhere('end_time', '=', null)
                        ->first();
                    $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;
//                    $downtime->shift_id = $shift->id?? null;
//                    $downtime->operator_id = $operator->id?? null;

                    array_push($downTimes, [
                        'start_time'        => $hour->copy()->startOfHour(),
                        'duration'          => $downtimeDuration,
                        'production_log_id' => $productionLog->id,
                        'shift_id'          => $shift->id?? null,
                        'operator_id'       => $operator->id?? null,
                        'created_at'        => now(),
                        'updated_at'        => now()
                    ]);

//                    $downtime->save();
                    $hour->addHours(1);
                }
                $previousProductionLog->produced_at = $hour->startOfHour();
                $cycleInterval = $logTimestamp->diffInSeconds($previousProductionLog->produced_at);
            }

            try {

                $differentHour = $logTimestamp->hour != Carbon::parse($previousProductionLog->produced_at)->hour;

                if ($cycleInterval > $stationProduct->cycle_time + $stationProduct->cycle_timeout) { //downtime, slowprod exists
                    $downtimeSecond = $cycleInterval - ($stationProduct->cycle_time + $stationProduct->cycle_timeout);
                    if ($differentHour) { //there must be a downtime or slow prod or good prod that lies between two hours
                        $downtimeStart = Carbon::parse($previousProductionLog->produced_at);
                        $downtimeEnd = $downtimeStart->copy()->addSeconds($downtimeSecond);

                        if ($downtimeStart->hour != $downtimeEnd->hour) {// 2 downtimes, 1 slowprod
//                            $downtimePrev = new Downtime();
//                            $downtimeNext = new Downtime();
//
//                            $downtimePrev->production_log_id = $productionLog->id;
//                            $downtimeNext->production_log_id = $productionLog->id;
//
//                            $downtimePrev->start_time = $downtimeStart;
//                            $downtimePrev->duration = $downtimeStart->copy()->endOfHour()->diffInSeconds($downtimeStart) + 1;
//
//                            $downtimeNext->start_time = $downtimeEnd->copy()->startOfHour();
//                            $downtimeNext->duration = $downtimeSecond - $downtimePrev->duration;

                            $downtimePrevDuration = $downtimeStart->copy()->endOfHour()->diffInSeconds($downtimeStart) + 1;
                            $downtimeNextDuration = $downtimeSecond - $downtimePrevDuration;

                            $shift = Shift::whereIn('id', $shiftIds)
                                ->where('start_time', '<=', $downtimeStart)
                                ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimePrevDuration))->first();



                            $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                                ->where('start_time', '<=', $downtimeStart)
                                ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimePrevDuration))
                                ->orWhere('end_time', '=', null)
                                ->first();
                            $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;
//                            $downtimePrev->shift_id = $shift->id?? null;
//                            $downtimePrev->operator_id = $operator->id?? null;

                            $shift = Shift::whereIn('id', $shiftIds)
                                ->where('start_time', '<=', $downtimeEnd->copy()->startOfHour())
                                ->where('end_time', '>=', Carbon::parse($downtimeEnd->copy()->startOfHour())->addSeconds($downtimeNextDuration))->first();



                            $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                                ->where('start_time', '<=', $downtimeEnd->copy()->startOfHour())
                                ->where('end_time', '>=', Carbon::parse($downtimeEnd->copy()->startOfHour())->addSeconds($downtimeNextDuration))
                                ->orWhere('end_time', '=', null)
                                ->first();
                            $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;
//                            $downtimeNext->shift_id = $shift->id?? null;
//                            $downtimeNext->operator_id = $operator->id?? null;

//                            $downtimePrev->save();
//                            $downtimeNext->save();

                            array_push($downTimes, [
                                'start_time'        => $downtimeStart,
                                'duration'          => $downtimePrevDuration,
                                'production_log_id' => $productionLog->id,
                                'shift_id'          => $shift->id?? null,
                                'operator_id'       => $operator->id?? null,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ]);

                            array_push($downTimes, [
                                'start_time'        => $downtimeEnd->copy()->startOfHour(),
                                'duration'          => $downtimeNextDuration,
                                'production_log_id' => $productionLog->id,
                                'shift_id'          => $shift->id?? null,
                                'operator_id'       => $operator->id?? null,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ]);

//                            $slowProduction = new SlowProduction();
//                            $slowProduction->production_log_id = $productionLog->id;
//                            $slowProduction->start_time = $downtimeEnd;
//                            $slowProduction->duration = $stationProduct->cycle_timeout;
//                            $slowProduction->save();

                            array_push($slowProductions, [
                                'production_log_id' => $productionLog->id,
                                'start_time'        => $downtimeEnd,
                                'duration'          => $stationProduct->cycle_timeout,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ]);
                        }
                        else if ($downtimeEnd->copy()->addSeconds($stationProduct->cycle_timeout)->hour != $downtimeEnd->hour) { //1 downtime, 2 slowprod
//                            $downtime = new Downtime();
//                            $downtime->production_log_id = $productionLog->id;
//                            $downtime->start_time = $downtimeStart;
//                            $downtime->duration = $downtimeSecond;

                            $shift = Shift::whereIn('id', $shiftIds)
                                ->where('start_time', '<=', $downtimeStart)
                                ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimeSecond))->first();


                            $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                                ->where('start_time', '<=', $downtimeStart)
                                ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimeSecond))
                                ->orWhere('end_time', '=', null)
                                ->first();

                            $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;
//                            $downtime->shift_id = $shift->id?? null;
//                            $downtime->operator_id = $operator->id?? null;
//                            $downtime->save();

                            array_push($downTimes, [
                                'start_time'        => $downtimeStart,
                                'duration'          => $downtimeSecond,
                                'production_log_id' => $productionLog->id,
                                'shift_id'          => $shift->id?? null,
                                'operator_id'       => $operator->id?? null,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ]);

//                            $slowProductionPrev = new SlowProduction();
//                            $slowProductionNext = new SlowProduction();
//
//                            $slowProductionPrev->production_log_id = $productionLog->id;
//                            $slowProductionNext->production_log_id = $productionLog->id;
//
//                            $slowProductionPrev->start_time = $downtimeEnd;
//                            $slowProductionPrev->duration = $downtimeEnd->copy()->endOfHour()->diffInSeconds($downtimeEnd) + 1;
//
//                            $slowProductionNext->start_time = $downtimeEnd->copy()->addSeconds($stationProduct->cycle_timeout)->copy()->startOfHour();
//                            $slowProductionNext->duration = $stationProduct->cycle_timeout - $slowProductionPrev->duration;

                            $slowProdPrevDuration = $downtimeEnd->copy()->endOfHour()->diffInSeconds($downtimeEnd) + 1;

                            array_push($slowProductions, [
                                'production_log_id' => $productionLog->id,
                                'start_time'        => $downtimeEnd,
                                'duration'          => $slowProdPrevDuration
                            ]);

                            array_push($slowProductions, [
                                'production_log_id' => $productionLog->id,
                                'start_time'        => $downtimeEnd->copy()->addSeconds($stationProduct->cycle_timeout)->startOfHour(),
                                'duration'          => $stationProduct->cycle_timeout - $slowProdPrevDuration
                            ]);

//                            $slowProductionPrev->save();
//                            $slowProductionNext->save();
                        }
                    }
                    else { //same hour but downtime and slowprod both exist

//                        $downtime = new Downtime();
//                        $downtime->production_log_id = $productionLog->id;
//                        $downtime->start_time = Carbon::parse($previousProductionLog->produced_at);
//                        $downtime->duration = $downtimeSecond;
                        $downtimeStart = Carbon::parse($previousProductionLog->produced_at);
                        $shift = Shift::whereIn('id', $shiftIds)
                            ->where('start_time', '<=', $downtimeStart)
                            ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimeSecond))->first();

                        $stationOperator = StationOperator::where('station_id', '=', $deviceStation->station_id)
                            ->where('start_time', '<=', $downtimeStart)
                            ->where('end_time', '>=', Carbon::parse($downtimeStart)->addSeconds($downtimeSecond))
                            ->orWhere('end_time', '=', null)
                            ->first();
                        $operator = !empty($stationOperator)? Operator::find($stationOperator->operator_id) : null;
//                        $downtime->shift_id = $shift->id?? null;
//                        $downtime->operator_id = $operator->id?? null;

//                        $downtime->save(); //todo change it later

                        array_push($downTimes, [
                            'start_time'        => $downtimeStart,
                            'duration'          => $downtimeSecond,
                            'production_log_id' => $productionLog->id,
                            'shift_id'          => $shift->id?? null,
                            'operator_id'       => $operator->id?? null,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);

//                        $slowProduction = new SlowProduction();
//                        $slowProduction->production_log_id = $productionLog->id;
//                        $slowProduction->start_time = $downtime->start_time->copy()->addSeconds($downtimeSecond);
//                        $slowProduction->duration = $stationProduct->cycle_timeout;
//                        $slowProduction->save(); //todo change later

                        array_push($slowProductions, [
                            'production_log_id' => $productionLog->id,
                            'start_time'        => $downtimeStart->copy()->addSeconds($downtimeSecond),
                            'duration'          => $stationProduct->cycle_timeout,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                    }
                }
                else if ($cycleInterval > $stationProduct->cycle_time) { //only slow production exists
                    $slowProductionTimeSeconds = $cycleInterval - $stationProduct->cycle_time;
                    $slowProductionStart = Carbon::parse($previousProductionLog->produced_at);
                    $slowProductionEnd = $slowProductionStart->copy()->addSeconds($slowProductionTimeSeconds);
                    if ($slowProductionStart->hour != $slowProductionEnd->hour) { //two slowprod

                        $slowProdPrevDuration = $slowProductionStart->copy()->endOfHour()->diffInSeconds($slowProductionStart) + 1;

//                        $slowProductionPrev = new SlowProduction();
//                        $slowProductionNext = new SlowProduction();
//
//                        $slowProductionPrev->production_log_id = $productionLog->id;
//                        $slowProductionNext->production_log_id = $productionLog->id;
//
//                        $slowProductionPrev->start_time = $slowProductionStart;
//                        $slowProductionPrev->duration = $slowProductionPrev->start_time->copy()->endOfHour()->diffInSeconds($slowProductionPrev->start_time) + 1;
////                        $slowProductionPrev->duration = $logTimestamp->copy()->endOfHour()->diffInSeconds($logTimestamp);
//
//
//                        $slowProductionNext->start_time = $slowProductionEnd->copy()->startOfHour();
//                        $slowProductionNext->duration = $slowProductionTimeSeconds - $slowProductionPrev->duration;
//
//                        $slowProductionPrev->save();
//                        $slowProductionNext->save();

                        array_push($slowProductions, [
                            'production_log_id' => $productionLog->id,
                            'start_time'        => $slowProductionStart,
                            'duration'          => $slowProdPrevDuration,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);

                        array_push($slowProductions, [
                            'production_log_id' => $productionLog->id,
                            'start_time'        => $slowProductionEnd->copy()->startOfHour(),
                            'duration'          => $slowProductionTimeSeconds - $slowProdPrevDuration,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                    }
                    else { //one slowprod
//                        $slowProduction = new SlowProduction();
//                        $slowProduction->production_log_id = $productionLog->id;
//                        $slowProduction->start_time = $previousProductionLog->produced_at;
//                        $slowProduction->duration = $slowProductionTimeSeconds;
//                        $slowProduction->save();

                        array_push($slowProductions, [
                            'production_log_id' => $productionLog->id,
                            'start_time'        => $previousProductionLog->produced_at,
                            'duration'          => $slowProductionTimeSeconds,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ]);
                    }
                }
            } catch (Exception $ex) {
                Log::error($ex);
                Log::debug($productionLog);
//                return $parsedProductionLogPacket;
                $parsedProductionLogPacket->setException($ex);
            }
        }
        Downtime::insert($downTimes);
        SlowProduction::insert($slowProductions);
        $packet->processing_end = now();
        $packet->save();
        return $parsedProductionLogPacket;
    }

    private function buildTimestamp($parts)
    {
        $dateFormat = "20%02d-%02d-%02d %02d:%02d:%02d";

        $dateFormatted = sprintf($dateFormat, $parts['year'] % 100, $parts['month'] % 13, $parts['date'] % 32,
            $parts['hour'] % 24, $parts['minute'] % 60, $parts['second'] % 60);

        return new Carbon($dateFormatted);
    }

    public function buildStatusResponse($error = 0, $templates = 0, $cycleTime = 0, $syncTime = 0)
    {
        /**
         * Log/Status Response Packet Protocol
         *
         * - 1st byte is error state for current request. 0 for "no errors" else "error"
         * - 2nd byte states sync state. 0 for sync queue is empty, 1 for sync packet waiting
         * - 3rd byte --- Command Byte ---
         * - 4th byte is time sync state. 0 for no syncing required, 1 for sync time now
         * - Bytes [ 5 - 10 ] is the sync time from server
         *
         * ----------------------------------------------------------------------------------------
         * Bytes  ->  1   |   2   |   3   |   4   |   5   |   6   |   7   |   8   |   9   |   10
         * ----------------------------------------------------------------------------------------
         *        | ERROR | SYNC  |  cit  |  TIME |   s   |   i   |   H   |   d   |   m   |   Y
         */

        $datetime = Carbon::now();

        $data = pack(
            'C10', 0, 0, $cycleTime, $syncTime > 10,
            $datetime->second,
            $datetime->minute,
            $datetime->hour,
            $datetime->day,
            $datetime->month,
            $datetime->year - 2000
        );

        return $data;
    }


    public function parseLogPacketAndSave(Device $device, Packet $packet)
    {
        $packetContent = $packet->request;
        $packetContentBin = hex2bin($packetContent);
//        $deviceTimeStamp  = sprintf("%04u", unpack("Ntimestamp/", substr($packetContentBin, 0, 4))['timestamp']);
//        $deviceTimeObject = Carbon::createFromTimestamp($deviceTimeStamp);
        $numberOfLogs = unpack('cnumOfLogs/', substr($packetContentBin, 4, 1))['numOfLogs'];

        $productionLogs = [];
        $downTimes = [];
        $slowProductions = [];

        $devicePortToDeviceStationMap = $this->deviceRepository->findAllDeviceStationsOfADevice($device->id)->mapWithKeys(function ($deviceStation) {
            return [$deviceStation['port'] + 1 => $deviceStation]; // 1 is added to port
        });

        $stationIdToStationProductMap = $this->productRepository->findAllStationProductsKeyByStationId();

        $stationIdToShiftListMap = $this->shiftRepository->findAllShiftsOfDeviceSortedGroupByStationId($device->id);
        $stationIdToOperatorListMap = $this->operatorRepository->findAllOperatorsOfDeviceSortedGroupByStationId($device->id);

        $previousProductionLog = null;
        $topProductionLog = $this->productionLogRepository->fetchLastProductionLog();
        $topSlowProduction = $this->productionLogRepository->fetchLastSlowProduction();
        $topDowntime = $this->productionLogRepository->fetchLastDowntime();

        $topProductionLogId = empty($topProductionLog)? 1 : $topProductionLog->id;
        $topSlowProductionId = empty($topSlowProduction)? 1 : $topSlowProduction->id;
        $topDowntimeId = empty($topDowntime)? 1 : $topDowntime->id;


        for ($i = 0; $i < $numberOfLogs; $i++) {

            $startingIndex = 5 + ($i*7);
            $logTimeStamp = sprintf("%04u", unpack("Ntimestamp/", substr($packetContentBin, $startingIndex, 4))['timestamp']);
            $logTimeObject = Carbon::createFromTimestamp($logTimeStamp);
            //we are omitting the length of packets, channel length bytes because for logs, the length of packets will always be 1
            $port = unpack('cport/', substr($packetContentBin, $startingIndex+6, 1))['port'];

            $deviceStation = $devicePortToDeviceStationMap->get($port+1);
            if(empty($deviceStation)) continue;
            $stationProduct = $stationIdToStationProductMap->get($deviceStation->station_id);
            if(empty($stationProduct)) continue;

            if($previousProductionLog == null) $previousProductionLog = $this->productionLogRepository->findLastProductionLogByStationIdAndProductId($deviceStation->station_id, $stationProduct->product_id);

            if ($previousProductionLog == null) {
                $previousProductionLog = new ProductionLog();
                $previousProductionLog->produced_at = $logTimeObject->copy()->startOfHour();
            }

            $shift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $logTimeObject->copy());
            $stationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $logTimeObject->copy());

            $productionLogs[] = [
                'id' => ++$topProductionLogId,
                'packet_id' => $packet->id,
                'station_id' => $deviceStation->station_id,
                'product_id' => $stationProduct->product_id,
                'produced_at' => $logTimeObject,
                'synced_at' => now(),
                'shift_id' => empty($shift)? null : $shift['id'],
                'operator_id' => empty($stationOperator)? null : $stationOperator->operator_id, //object for operators and array for shifts is a very bad design
                'cycle_interval' => $cycleInterval = $logTimeObject->diffInSeconds($previousProductionLog->produced_at),
                'created_at' => now(),
                'updated_at' => now()
            ];

            $pLog = new ProductionLog();
            $pLog->produced_at = $logTimeObject->copy();

            $hourDiff = $logTimeObject->copy()->startOfHour()->diffInHours(Carbon::parse($previousProductionLog->produced_at)->startOfHour());

            if ($hourDiff >= 2) {
                $downtimePrevStartTime = Carbon::parse($previousProductionLog->produced_at);
                $downtimePrevDuration = $downtimePrevStartTime->copy()->endOfHour()->diffInSeconds($downtimePrevStartTime);

                $dTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimePrevStartTime);
                $stationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimePrevStartTime);

                $downTimes[] = [
                    'id'                => ++$topDowntimeId,
                    'start_time'        => $downtimePrevStartTime,
                    'duration'          => $downtimePrevDuration,
                    'production_log_id' => $topProductionLogId,
                    'shift_id'          => empty($dTimeShift)? null : $dTimeShift['id'],
                    'operator_id'       => empty($stationOperator)? null : $stationOperator->operator_id,
                    'created_at'        => now(),
                    'updated_at'        => now()
                ];

                $hour = $downtimePrevStartTime->copy()->addHours(1);
                $remainingHours = $logTimeObject->copy()->startOfHour()->diffInHours(Carbon::parse($previousProductionLog->produced_at)->startOfHour());
                for ($j = 0; $j < $remainingHours-1; $j++) {
                    $downtimeStart = $hour->copy()->startOfHour();
                    $downtimeDuration = 3600;
                    $dTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimeStart);
                    $stationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimeStart);

                    $downTimes[] = [
                        'id'                => ++$topDowntimeId,
                        'start_time'        => $hour->copy()->startOfHour(),
                        'duration'          => $downtimeDuration,
                        'production_log_id' => $topProductionLogId,
                        'shift_id'          => empty($dTimeShift)? null : $dTimeShift['id'],
                        'operator_id'       => empty($stationOperator)? null : $stationOperator->operator_id,
                        'created_at'        => now(),
                        'updated_at'        => now()
                    ];
                    $hour->addHours(1);
                }
                $previousProductionLog->produced_at = $hour->startOfHour();
                $cycleInterval = $logTimeObject->diffInSeconds($previousProductionLog->produced_at);
            }

            try {

                $differentHour = $logTimeObject->hour != Carbon::parse($previousProductionLog->produced_at)->hour;

                if ($cycleInterval > $stationProduct->cycle_time + $stationProduct->cycle_timeout) { //downtime, slowprod exists
                    $downtimeSecond = $cycleInterval - ($stationProduct->cycle_time + $stationProduct->cycle_timeout);
                    if ($differentHour) { //there must be a downtime or slow prod or good prod that lies between two hours
                        $downtimeStart = Carbon::parse($previousProductionLog->produced_at);
                        $downtimeEnd = $downtimeStart->copy()->addSeconds($downtimeSecond);

                        if ($downtimeStart->hour != $downtimeEnd->hour) {// 2 downtimes, 1 slowprod
                            $downtimePrevDuration = $downtimeStart->copy()->endOfHour()->diffInSeconds($downtimeStart) + 1;
                            $downtimeNextDuration = $downtimeSecond - $downtimePrevDuration;

                            $previousDTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimeStart);
                            $prevStationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimeStart);
                            $downTimes[] = [
                                'id'                => ++$topDowntimeId,
                                'start_time'        => $downtimeStart,
                                'duration'          => $downtimePrevDuration,
                                'production_log_id' => $topProductionLogId,
                                'shift_id'          => empty($previousDTimeShift)? null : $previousDTimeShift['id'],
                                'operator_id'       => empty($prevStationOperator)? null : $prevStationOperator->id,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ];

                            $nextDTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimeEnd->copy()->startOfHour());
                            $nextStationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimeEnd->copy()->startOfHour());
                            $downTimes[] = [
                                'id'                => ++$topDowntimeId,
                                'start_time'        => $downtimeEnd->copy()->startOfHour(),
                                'duration'          => $downtimeNextDuration,
                                'production_log_id' => $topProductionLogId,
                                'shift_id'          => empty($nextDTimeShift)? null : $nextDTimeShift['id'],
                                'operator_id'       => empty($nextStationOperator)? null : $nextStationOperator->id,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ];

                            $slowProductions[] = [
                                'id'                => ++$topSlowProductionId,
                                'production_log_id' => $topProductionLogId,
                                'start_time'        => $downtimeEnd,
                                'duration'          => $stationProduct->cycle_timeout,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ];
                        }
                        else if ($downtimeEnd->copy()->addSeconds($stationProduct->cycle_timeout)->hour != $downtimeEnd->hour) { //1 downtime, 2 slowprod

                            $dTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimeStart);
                            $stationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimeStart);
                            $downTimes[] = [
                                'id'                => ++$topSlowProductionId,
                                'start_time'        => $downtimeStart,
                                'duration'          => $downtimeSecond,
                                'production_log_id' => $topProductionLogId,
                                'shift_id'          => empty($dTimeShift)? null : $dTimeShift['id'],
                                'operator_id'       => empty($stationOperator) ? null: $stationOperator->operator_id,
                                'created_at'        => now(),
                                'updated_at'        => now()
                            ];

                            $slowProdPrevDuration = $downtimeEnd->copy()->endOfHour()->diffInSeconds($downtimeEnd) + 1;

                            $slowProductions[] = [
                                'id'                => ++$topSlowProductionId,
                                'production_log_id' => $topProductionLogId,
                                'start_time'        => $downtimeEnd,
                                'duration'          => $slowProdPrevDuration
                            ];

                            $slowProductions[] = [
                                'id'                => ++$topSlowProductionId,
                                'production_log_id' => $topProductionLogId,
                                'start_time'        => $downtimeEnd->copy()->addSeconds($stationProduct->cycle_timeout)->startOfHour(),
                                'duration'          => $stationProduct->cycle_timeout - $slowProdPrevDuration
                            ];
                        }
                    }
                    else { //same hour but downtime and slowprod both exist

                        $downtimeStart = Carbon::parse($previousProductionLog->produced_at);

                        $dTimeShift = $this->findShiftOfStation($stationIdToShiftListMap, $deviceStation->station_id, $downtimeStart);
                        $stationOperator = $this->findOperatorOfStation($stationIdToOperatorListMap, $deviceStation->station_id, $downtimeStart);
                        $downTimes[] = [
                            'id'                => ++$topDowntimeId,
                            'start_time'        => $downtimeStart,
                            'duration'          => $downtimeSecond,
                            'production_log_id' => $topProductionLogId,
                            'shift_id'          => empty($dTimeShift)? null : $dTimeShift['id'],
                            'operator_id'       => empty($stationOperator)? null : $stationOperator->operator_id,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ];

                        $slowProductions[] = [
                            'id'                => ++$topSlowProductionId,
                            'production_log_id' => $topProductionLogId,
                            'start_time'        => $downtimeStart->copy()->addSeconds($downtimeSecond),
                            'duration'          => $stationProduct->cycle_timeout,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ];
                    }
                }
                else if ($cycleInterval > $stationProduct->cycle_time) { //only slow production exists
                    $slowProductionTimeSeconds = $cycleInterval - $stationProduct->cycle_time;
                    $slowProductionStart = Carbon::parse($previousProductionLog->produced_at);
                    $slowProductionEnd = $slowProductionStart->copy()->addSeconds($slowProductionTimeSeconds);
                    if ($slowProductionStart->hour != $slowProductionEnd->hour) { //two slowprod

                        $slowProdPrevDuration = $slowProductionStart->copy()->endOfHour()->diffInSeconds($slowProductionStart) + 1;


                        $slowProductions[] = [
                            'id'                => ++$topSlowProductionId,
                            'production_log_id' => $topProductionLogId,
                            'start_time'        => $slowProductionStart,
                            'duration'          => $slowProdPrevDuration,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ];

                        $slowProductions[] = [
                            'id'                => ++$topSlowProductionId,
                            'production_log_id' => $topProductionLogId,
                            'start_time'        => $slowProductionEnd->copy()->startOfHour(),
                            'duration'          => $slowProductionTimeSeconds - $slowProdPrevDuration,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ];
                    }
                    else { //one slowprod

                        $slowProductions[] = [
                            'id'                => ++$topSlowProductionId,
                            'production_log_id' => $topProductionLogId,
                            'start_time'        => $previousProductionLog->produced_at,
                            'duration'          => $slowProductionTimeSeconds,
                            'created_at'        => now(),
                            'updated_at'        => now()
                        ];
                    }
                }
            } catch (Exception $ex) {
                Log::error($ex);
            }
            $previousProductionLog->produced_at = $logTimeObject->copy();
        }
        ProductionLog::insertOrIgnore($productionLogs);
        Downtime::insertOrIgnore($downTimes);
        SlowProduction::insertOrIgnore($slowProductions);
        $packet->processing_end = now();
        $packet->save();
    }

    public function findShiftOfStation($stationIdToShiftListMap, int $stationId, Carbon $producedAt)
    {
        $shiftList = $stationIdToShiftListMap->get($stationId);
        $day = $producedAt->dayOfWeek;
        $selectedShift = null;
        if(empty($shiftList)) return null;
//        $shiftList = collect($shiftList)->sortBy('start_time')->toArray();
        $timeStamp = $producedAt->toTimeString();
        foreach ($shiftList as $shift) {
            if(substr($shift['schedule'], $day, 1) != '1') continue;
            if($shift['start_time'] <= $timeStamp && $shift['end_time'] >= $timeStamp) {
                $selectedShift = $shift;
                break;
            }
        }
        return $selectedShift;
    }

    public function findOperatorOfStation($stationIdToOperatorListMap, int $stationId, Carbon $producedAt)
    {
        $operatorList = $stationIdToOperatorListMap->get($stationId);
        if(empty($operatorList)) return null;
        $operatorList = collect($operatorList);
        $selectedOperator = null;
        foreach ($operatorList as $operator) {
            if($producedAt >= $operator->start_time && (empty($operator->end_time) || (!empty($operator->end_time) && $producedAt <= $operator->end_time))) {
                $selectedOperator = $operator;
            }
        }
        return $selectedOperator;
    }
}
