<?php

namespace App\Console\Commands;

use App\Http\Controllers\MailController;
use Illuminate\Console\Command;

class MailAlert extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Mail';

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
        $mailBody = [
          'machine_name'=> 'CM-1',
          'alarm_info'=> 'Test Alarm'
        ];
        $controller = new MailController();
        $controller->GenerateAlarmMail($mailBody);
    }
}
