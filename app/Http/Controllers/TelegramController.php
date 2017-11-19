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
        curl_setopt($curl, CURLOPT_URL,"https://www.thecsgobot.com/api/service.inventory.json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Host: www.thecsgobot.com",
            "Connection: keep-alive",
            "Content-Length: 7",
            "Accept: */*",
            "Origin: https://www.thecsgobot.com",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Referer: https://www.thecsgobot.com/trading/",
//            "Accept-Encoding: gzip, deflate, br",
            "Accept-Language: en-US,en;q=0.9",
            "Cookie: __cfduid=d55eded162019ba018906e0d894d5ea701511096482;
            JSESSIONID=6C5A1642AAF4F09151B09838EDBBF944;
            _ga=GA1.2.1308276030.1511096483;
            _gid=GA1.2.437421720.1511096483"
        ));
        $post_data = array (
            "who" => "bot"
        );
//        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "who=bot");
        $curl_response = curl_exec($curl);
        curl_close($curl);
        dd(collect(json_decode($curl_response)->items)->where('descriptions.0.aFloat', '<=', '0.02'));
//        dd($curl_response);

    }

}
