<?php

namespace App\Console\Commands;

use App\Data\Models\Station;
use Illuminate\Console\Command;

class CreateStationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:create-station';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new stations';

    protected $codes = [
        "ODPA-01",
        "ODPA-02",
        "ODPA-03",
        "ODPA-04",
        "ODPA-05",
        "ODPA-06",
        "ODPA-07",
        "ODPA-08"
    ];

    protected $names = [
        "Base Pre-Assembly",
        "Condenser Pre-Assembly",
        "Valve stand Pre-Assembly",
        "Capilary Tube & Streightner Pre-Assembly",
        "Suction pipe Pre-Assembly",
        "Fan Stand Pre-Assembly",
        "Compressor power Cable Pre-Assembly",
        "Electrical Box Pre-Assembly"
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        for ($i = 0; $i < count($this->codes); $i++)
        {
            $station                   = new Station();
            $station->code             = $this->codes[$i];
            $station->name             = $this->names[$i];
            $station->description      = '';
            $station->station_group_id = '';
            $station->oee_threshold    = 100;
            $station->created_at       = now();
            $station->updated_at       = now();
            $station->save();
        }
        return 0;
    }
}
