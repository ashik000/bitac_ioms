<?php

namespace App\Data\Repositories;

use App\Data\Models\Device;
use App\Data\Models\Packet;

class PacketRepository
{

    /**
     * @param Device $device
     * @param string $content
     * @return Packet
     */
    public function savePacketFromDevice(Device $device, string $content)
    {
        $packet = new Packet();
        $packet->device_id = !empty($device)? $device->id : null;
        $packet->request = $content;
        $packet->processing_start = now();
        $packet->save();
        return $packet;
    }

}
