<?php

namespace App\Jobs;

use App\Data\Models\Device;
use App\Data\Models\Packet;
use App\Devices\InovaceDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ParseAndSaveIomsLogPacket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $device;
    protected $packet;

    /**
     * Create a new Job Instance
     * @param Device $device
     * @param Packet $packet
     */
    public function __construct(Device $device, Packet $packet)
    {
        $this->device = $device;
        $this->packet = $packet;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InovaceDevice $deviceManager)
    {
        $deviceManager->parseLogPacketAndSave($this->device, $this->packet);
    }
}
