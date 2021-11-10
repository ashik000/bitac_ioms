<?php

namespace App\Console\Commands;

use App\Data\Repositories\ScrapRepository;
use Illuminate\Console\Command;

class StoreScrapData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:scrap
                            {--D|date=}
                            {--S|startTime=}
                            {--E|endTime=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test description';

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
        $date = $this->option('date');
        $startTime = $this->option('startTime');
        $endTime = $this->option('endTime');

        $scrap = app(ScrapRepository::class)->postScrap($date, $startTime, $endTime);
    }
}
