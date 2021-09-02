<?php

namespace App\Jobs;

use App\Data\Models\Device;
use App\Data\Models\Packet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Log\Logger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

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
    public function handle()
    {
        Log::debug('Job dispatched');
        Log::debug($this->device->identifier);
    }
}
