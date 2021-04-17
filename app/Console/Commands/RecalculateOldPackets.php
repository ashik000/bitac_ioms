<?php

namespace App\Console\Commands;

use App\Data\Models\Packet;
use App\Devices\InovaceDevice;
use Illuminate\Console\Command;
use Log;

class RecalculateOldPackets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:recalculate-packets{device}';

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
        $device_id = $this->argument('device');
        $packets = Packet::where('device_id', '=', $device_id)
                        ->where('processing_end', '=', null)
                        ->orderBy('id', 'asc')
                        ->get();

        foreach ($packets as $packet) {
            $this->deviceManager->parseAndSaveProductionLogs($packet);
        }
    }
}
