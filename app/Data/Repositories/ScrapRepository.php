<?php

namespace App\Data\Repositories;

use App\Data\Models\Scrap;
use Carbon\CarbonImmutable;

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
}
