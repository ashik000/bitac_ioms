<?php

namespace App\Console\Commands;

use App\Data\Models\Packet;
use App\Devices\InovaceDevice;
use Illuminate\Console\Command;
use Log;

class RecalculateOldPacketsWithId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:recalculate-packets-id{--device=}{--range=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /** @var InovaceDevice $deviceManager */
    protected $deviceManager;

    /**
     * Create a new command instance.
     * @var InovaceDevice $deviceManager
     * @return void
     */
    public function __construct(InovaceDevice $deviceManager)
    {
        parent::__construct();
        $this->deviceManager = $deviceManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $packetIdRange = $this->option('range');
        $device_id = $this->option('device');
        $startId = $packetIdRange[0];
        $endId = $packetIdRange[1];
        $packets = Packet::where('device_id', '=', $device_id)
            ->whereBetween('id', $packetIdRange)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($packets as $packet) {
//            Log::debug($packet->id);
            $this->deviceManager->parseAndSaveProductionLogs($packet);
        }
    }
}
