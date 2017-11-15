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


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://loot.farm/botsInventory_new.json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($curl_response);
//        dd(collect($curl_response)->where('m', '=', '★ Flip Knife | Doppler (Factory New)'));
        dd(collect($curl_response->result)->first());

    }

}
