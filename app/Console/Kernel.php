<?php

namespace App\Console;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {  

        $schedule->call('App\Http\Controllers\Admin\BuildController@getTheatres')
                ->weekly()->fridays()->at('06:00');

        $schedule->call('App\Http\Controllers\Admin\BuildController@movistar')
                ->dailyAt('06:00');        

        $schedule->call('App\Http\Controllers\Admin\BuildController@movistar')
                ->dailyAt('05:30');

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
