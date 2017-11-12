<?php

namespace App\Http\Controllers;

use App\Item;
use App\Pattern;
use App\Site;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        curl_setopt($curl, CURLOPT_URL,"https://ru.cs.deals/ajax/botsinventory");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Origin: https://ru.cs.deals",
            "Referer: https://ru.cs.deals/",
            "Connection: keep-alive",
            "Content-Length: 0",
            "Origin: https://ru.cs.deals",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
            "Referer: https://ru.cs.deals/",
            "Accept: application/json, text/javascript, */*; q=0.01",
            "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
            "Authority: ru.cs.deals",
            "Method: POST",
            "Path: /ajax/botsinventory",
            "Scheme: https"
        ));
        $curl_response = curl_exec($curl);
	    $csmoney_items = json_decode($curl_response);
        dd(collect($csmoney_items->response)->where('m', '=', '3437'));
//        dd(collect($csmoney_items->response)->where('m', '=', '★ Gut Knife | Gamma Doppler (Factory New)'));
//        dd($csmoney_items->response);

    }

}
