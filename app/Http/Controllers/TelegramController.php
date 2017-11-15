<?php

namespace App\Http\Controllers;

use App\Classes\SumClass;
use App\Item;
use App\Pattern;
use App\Site;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function handle()
    {
        set_time_limit(0);
        Telegram::commandsHandler(true);
    }

    public function test(){
        if (null <= 12.3) dd(1);
        else dd(2);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://skinsjar.com/api/v3/load/bots?refresh=0&v=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $curl_response = curl_exec($curl);
	    $curl_response = json_decode($curl_response)->items;
        dd(collect($curl_response)->where('name', '=', '★ Moto Gloves | Spearmint (Field-Tested)'));

    }

}
