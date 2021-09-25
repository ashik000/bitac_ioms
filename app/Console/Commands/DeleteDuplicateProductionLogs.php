<?php

namespace App\Console\Commands;

use App\Data\Models\ProductionLog;
use Illuminate\Console\Command;

class DeleteDuplicateProductionLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inovace:dpl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete duplicate production logs';

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
        $allLogs = ProductionLog::all()->groupBy('id');
//        \Log::debug($allLogs);
        foreach ($allLogs as $key => $value) {
//            \Log::debug($value);
//            return;
            if($value->count() > 1) {
                $log = $value[2];
                $log->temp = 'tempval';
                $log->save();
//                $log->forceDelete();
                return;
            }
//            else {

//                $log = $value->get(0);
//                \Log::debug($log);
//                return;
//
//            }
        }
    }
}
