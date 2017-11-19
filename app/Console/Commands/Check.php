<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check';

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
     * @return mixed
     */
    public function handle()
    {
//        $this->call( 'raffle:check', [ 'site_id' => 1 ] );
//        $this->call( 'raffle:check', [ 'site_id' => 2 ] );
//        $this->call( 'raffle:check', [ 'site_id' => 3 ] );
        $this->call( 'cstrade:check');
//        $this->call( 'skintrade:check');
//        $this->call( 'csgosum:check');
        $this->call( 'lootfarm:check');
	    $this->call( 'csdeals:check', [ 'site_id' => 11 ] );
	    $this->call( 'csdeals:check', [ 'site_id' => 12 ] );
    }
}
