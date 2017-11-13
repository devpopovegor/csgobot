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
        curl_setopt($curl, CURLOPT_URL,"https://ru.tradeskinsfast.com/ajax/botsinventory");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		    "Origin: https://ru.tradeskinsfast.com",
		    "Referer: https://ru.tradeskinsfast.com/",
		    "Connection: keep-alive",
		    "Content-Length: 0",
		    "X-Requested-With: XMLHttpRequest",
		    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
		    "Accept: application/json, text/javascript, */*; q=0.01",
		    "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
		    "Authority: ru.tradeskinsfast.com",
		    "Method: POST",
		    "Path: /ajax/botsinventory",
		    "Scheme: https"
	    ));
	    $curl_response = curl_exec($curl);
	    $curl_response = json_decode(utf8_decode($curl_response))->response;
        dd(collect($curl_response)->where('m', '=', '★ Flip Knife | Doppler (Factory New)'));

    }

}
