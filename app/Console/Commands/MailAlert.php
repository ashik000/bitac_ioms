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
     * @return void
     */
    public function handle()
    {
        $mailBody = [
          'machine_name'=> 'CM-1',
          'alarm_info'=> 'Test Alarm'
        ];

        $toEmails = [
            'emails' => [
                'arifahmed.bitac@gmail.com',
                'mhasan0925@gmail.com',
                'pulakkantiroy09@gmail.com',
                'omaryusuf778106@gmail.com',
                'ae_cncdhaka@bitac.gov.bd',
                'ashik@inovacetech.com'
            ],
            'names' => [
                'Arif Ahmed',
                'M Hasan',
                'Pulak Roy',
                'Omar Yusuf',
                'AE CNC Dhaka',
                'Ashik Inovace'
            ]
        ];

        $mailController = new MailController();
        $mailController->generateAlarmMail($mailBody, $toEmails);
    }
}
