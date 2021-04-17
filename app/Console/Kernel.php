<?php

namespace App\Console;

use App\Console\Commands\GenerateReportCommand;
use Carbon\CarbonImmutable;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * @var $hourlyTime CarbonImmutable
         * @var $dailyTime CarbonImmutable
         * @var $weekTime CarbonImmutable
         * @var $monthTime CarbonImmutable
         */
        $hourlyTime = now()->toImmutable()->subHour();
        $schedule->command(GenerateReportCommand::class, [
            '-T' . 'hourly',
            '-S' . $hourlyTime->startOfHour()->toDateTimeString(),
            '-E' . $hourlyTime->endOfHour()->toDateTimeString(),
        ])->hourlyAt(5);

        $dailyTime = now()->toImmutable()->subDay();
        $schedule->command(GenerateReportCommand::class, [
            '-T' . 'daily',
            '-S' . $dailyTime->startOfDay()->toDateTimeString(),
            '-E' . $dailyTime->endOfDay()->toDateTimeString(),
        ])->dailyAt('00:10:00');

        $weekTime = now()->toImmutable()->subWeek();
        $schedule->command(GenerateReportCommand::class, [
            '-T' . 'weekly',
            '-S' . $weekTime->startOfDay()->toDateTimeString(),
            '-E' . $weekTime->addWeek()->endOfDay()->toDateTimeString(),
        ])->fridays()->at('00:15:00');

        $monthTime = now()->toImmutable()->subWeek();
        $schedule->command(GenerateReportCommand::class, [
            '-T' . 'monthly',
            '-S' . $monthTime->startOfMonth()->toDateTimeString(),
            '-E' . $monthTime->endOfMonth()->toDateTimeString(),
        ])->monthlyOn(1, '00:20:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
