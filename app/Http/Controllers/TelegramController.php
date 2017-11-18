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
        curl_setopt($curl, CURLOPT_URL,"https://skinsjar.com/api/v3/load/bots?refresh=0&v=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
//        $curl_response = json_decode($curl_response);
        dd($curl_response);
//        dd($curl_response);
        $i = 0;
        foreach (collect($curl_response->result) as $item){
            if ($i == 100) dd($item);
            $i++;
        }
//        dd(collect($curl_response->result)->first());

    }

}
