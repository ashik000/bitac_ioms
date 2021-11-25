<?php

namespace App\Data\Repositories;

use App\Data\Models\Product;
use App\Data\Models\Shift;
use App\Data\Models\Station;
use App\Data\Models\StationProduct;
use App\Data\Models\Scrap;
use Carbon\CarbonImmutable;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ScrapRepository {
    public function fetchScrapsOfADate($stationId, CarbonImmutable $date){
        return Scrap::where('date', '=', $date)
            ->where('station_id', '=', $stationId)
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc')
            ->get();
    }

    public function fetchScrapsBetweenADateRangeOfAllStations(CarbonImmutable $startDate, CarbonImmutable $endDate){
        return Scrap::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'asc')
            ->orderBy('hour', 'asc')
            ->get();
    }

    public function postScrap($date, $startTime, $endTime)
    {
        $date = date('Y-m-d', strtotime($date));
        $startTime = date('H:i:s', strtotime($startTime));
        $endTime = date('H:i:s', strtotime($endTime));

        $products = Product::all();

        foreach ($products as $product)
        {
            $productId = $product->id;
            $stationProduct = StationProduct::where('product_id', $productId)->first();

            if ($stationProduct)
            {
                $stationId = $stationProduct->station_id;
                $station = Station::where('id', $stationId)->first();

                if ($station)
                {
                    $stationName = $station->name;

                    $shift = Shift::where('start_time', '<=', $startTime)
                        ->where('end_time', '>=', $endTime)
                        ->first();
                    $shiftId = $shift->id;

                    $scrapData = $this->getScrapData($date, $startTime, $endTime, 'outdoor_final', $stationName, $product->name);

                    if ($scrapData)
                    {
                        foreach ($scrapData['data'] as $key1 => $res1)
                        {
                            $hour = $this->mapHour($key1);

                            $scrap = new Scrap();
                            $scrap->created_by = 1;
                            $scrap->value = $res1;
                            $scrap->date = $date;
                            $scrap->hour = $hour;
                            $scrap->product_id = $productId;
                            $scrap->station_id = $stationId;
                            $scrap->shift_id = $shiftId;

                            $scrap->save();
                        }
                    }
                }
            }
        }

        return response()->json('sucess', 200);
    }

    public function mapHour($hrStr)
    {
        switch ($hrStr) {
            case 'hr_00':
                return '0';
                break;
            case 'hr_01':
                return '1';
                break;
            case 'hr_02':
                return '2';
                break;
            case 'hr_03':
                return '3';
                break;
            case 'hr_04':
                return '4';
                break;
            case 'hr_05':
                return '5';
                break;
            case 'hr_06':
                return '6';
                break;
            case 'hr_07':
                return '7';
                break;
            case 'hr_08':
                return '8';
                break;
            case 'hr_09':
                return '9';
                break;
            case 'hr_10':
                return '10';
                break;
            case 'hr_11':
                return '11';
                break;
            case 'hr_12':
                return '12';
                break;
            case 'hr_13':
                return '13';
                break;
            case 'hr_14':
                return '14';
                break;
            case 'hr_15':
                return '15';
                break;
            case 'hr_16':
                return '16';
                break;
            case 'hr_17':
                return '17';
                break;
            case 'hr_18':
                return '18';
                break;
            case 'hr_19':
                return '19';
                break;
            case 'hr_20':
                return '20';
                break;
            case 'hr_21':
                return '21';
                break;
            case 'hr_22':
                return '22';
                break;
            case 'hr_23':
                return '23';
                break;
            default:
                return '0';
                break;
        }
    }

    public function getScrapData($date, $startTime, $endTime, $point, $line, $prod)
    {
        // api call to http://man.api.waltonbd.com/api/hour_wise_punch/index.php
        // type get

        // query params
        // date (Ex. ‘2021-01-01’)
        // from_time (Ex. ‘12:00’)
        // to_time (Ex. ’14:30’)
        // point (Ex. ‘Indoor_final’)
        // line (Ex. ‘ac_ind_prod_1’)
        // prod (Ex. ‘ac_ind’)

        // guzzle get request sample
        $client = new Client([
            'base_uri' => 'http://man.api.waltonbd.com/',
            'timeout'  => 2.0,
        ]);

        $response = $client->request('GET', 'api/hour_wise_punch/index.php', ['query' => ['date' => $date, 'from_time' => $startTime, 'to_time' => $endTime, 'point' => 'indoor_final', 'line' => $line, 'prod' => $prod]]);

        return $response->getBody();
    }

    public function getDummyScrap()
    {
        $res = [
            "success" => TRUE,
            "data" => array("hr_12" => 1212,"hr_13" => 1121, "hr_14" => 675)
        ];
        return $res;
    }

    public function testScrap()
    {
        $products = Product::all();

        foreach ($products as $product)
        {
            $productId = $product->id;
            $stationProduct = StationProduct::where('product_id', $productId)->first();

            if ($stationProduct)
            {
                $stationId = $stationProduct->station_id;
                $station = Station::where('id', $stationId)->first();

                if ($station)
                {
                    $stationName = $station->name;
                    // $shift = Shift::where('start_time', '<=', $startTime)
                    //     ->where('end_time', '>=', $endTime)
                    //     ->first();
                    // $shiftId = $shift->id;
                    $shiftId = 1;

                    $scrapData = $this->getDummyScrap();

                    if ($scrapData)
                    {
                        foreach ($scrapData['data'] as $key1 => $res1)
                        {
                            $hour = $this->mapHour($key1);

                            $exists = Scrap::where('date', date('Y-m-d'))
                                ->where('hour', $hour)
                                ->where('product_id', $productId)
                                ->where('station_id', $stationId)
                                ->where('shift_id', $shiftId)
                                ->first();

                            if (!$exists)
                            {
                                $scrap = new Scrap();
                                $scrap->created_by = 1;
                                $scrap->value = $res1;
                                $scrap->date = date('Y-m-d');
                                $scrap->hour = $hour;
                                $scrap->product_id = $productId;
                                $scrap->station_id = $stationId;
                                $scrap->shift_id = $shiftId;

                                $scrap->save();
                            }
                            else
                            {
                                // update scrap
                                $exists->value = $exists->value+$res1;
                                $exists->save();
                            }
                        }
                    }
                }
            }

            // echo "<pre>";
            // print_r($stationName);
            // die();
        }

        return response()->json('sucess', 200);
    }
}
