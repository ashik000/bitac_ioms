<?php

namespace App\Console\Commands;

use App\Data\Repositories\DeviceRepository;
use Illuminate\Console\Command;

class AddNewDevices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:add-device
                            {--I|identifiers=} ';

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
    public function handle(DeviceRepository $deviceRepository)
    {
        $identifiers = $this->option('identifiers');
        $identifiersArray = !empty($identifiers)? explode(',', $identifiers) : [];
        $count = count($identifiersArray) -1;
        $bar = $this->output->createProgressBar($count);
        $devices = [];
        foreach ($identifiersArray as $identifier) {
            $bar->advance();
            $device = $deviceRepository->findByIdentifier($identifier);
            if(empty($device)) {
                $devices = [
                    'identifier' => $identifier,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                $deviceRepository->batchInsertDevice($devices);
            }
        }
        $bar->finish();
    }
}
