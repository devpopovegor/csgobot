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
//        dd(dirname(__FILE__) . '/cookie.txt');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://skinsjar.com/api/v3/load/bots?refresh=0&v=0");
//        curl_setopt($curl, CURLOPT_URL,"http://104.28.26.228");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Host: skinsjar.com",
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 1",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
            "Referer: https://skinsjar.com/",
//            "Accept-Encoding: gzip, deflate, br",
            "Accept-Language: en-US,en;q=0.9",
            "Cookie: __cfduid=d0df6d8aa98ebfb3b13096c0859746ae71511002753; _ga=GA1.2.1049133680.1511002761; _gid=GA1.2.1258118144.1511002761; _ym_uid=1511002761162504861; currentCurrencyCode=USD; _ym_visorc_43477244=w; _ym_isad=2; intercom-id-f94tzf5i=c1047e04-5c0d-4a9c-a5c8-ac838bdb4ea1; cf_clearance=8242741f508f1748d5554ec410736c151b4f3fbd-1511003906-900"
        ));
        $curl_response = curl_exec($curl);

        dd(json_decode($curl_response));

    }

}
