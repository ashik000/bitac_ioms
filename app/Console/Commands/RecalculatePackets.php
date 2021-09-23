<?php

namespace App\Console\Commands;

use App\Data\Models\Device;
use App\Data\Models\Packet;
use App\Devices\InovaceDevice;
use Illuminate\Console\Command;

class RecalculatePackets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:recalculate {--I|identifier=} {--T|time-range=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle(InovaceDevice $inovaceDevice)
    {
        $deviceIdentifier = $this->option('identifier');
        $timeRange = $this->option('time-range');
        $timeRangeArray = explode(',', $timeRange);
        $startTime = $timeRangeArray[0];
        $endTime = $timeRangeArray[1];

        $device = Device::where('identifier', $deviceIdentifier)->get();
        $packets = Packet::where('device_id', $device->id)
                            ->whereBetween('created_at', [$startTime, $endTime])
                            ->orderBy('created_at')
                            ->get();

        $count = $packets->count();
        $bar = $this->output->createProgressBar($count);
        foreach ($packets as $packet) {
            $bar->advance();
            $inovaceDevice->parseLogPacketAndSave($device, $packet);
        }
        $bar->finish();
        error_log('Done');
        return 0;
    }
}
