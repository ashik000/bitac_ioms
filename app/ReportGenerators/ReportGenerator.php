<?php

namespace App\ReportGenerators;


use Carbon\CarbonImmutable;

interface ReportGenerator
{
    public function generate(CarbonImmutable $start, CarbonImmutable $end);

    public function truncate($cascadeUp);
}
