<?php

namespace App\Console;

use App\Console\Commands\Check;
use App\Console\Commands\Csdeals;
use App\Console\Commands\Csgosum;
use App\Console\Commands\CsMoney;
use App\Console\Commands\Cstrade;
use App\Console\Commands\FillItemsCommand;
use App\Console\Commands\ItemFullName;
use App\Console\Commands\Lootfarm;
use App\Console\Commands\Raffletrades;
use App\Console\Commands\Skinsjar;
use App\Console\Commands\Skintrade;
use App\Console\Commands\TestCommand;
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
        FillItemsCommand::class,
        TestCommand::class,
        CsMoney::class,
        ItemFullName::class,
        Csgosum::class,
        Cstrade::class,
        Lootfarm::class,
        Skinsjar::class,
        Skintrade::class,
        Raffletrades::class,
	    Csdeals::class,
        Check::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
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
