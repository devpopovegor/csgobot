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
//        dd(strpos('★ Sport Gloves | Pandora\'s Box (Minimal Wear)', '\''));
//        dd(urldecode("★ Sport Gloves | Pandora%27s Box (Minimal Wear)"));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://csgosell.com/phpLoaders/forceBotUpdate/all.txt");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Accept:*/*",
            "Accept-Language:en-US,en;q=0.9",
            "Connection:keep-alive",
            "Content-Length:61",
            "Content-Type:application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie:__cfduid=d70de1f6eabeb188f99455a2b86f795301511086344; language=en; PHPSESSID=90a6e4178d3be74bb4fcc8d8778e0c9d; _ga=GA1.2.1372332778.1511086346; _gid=GA1.2.1835651314.1511086346; _gat=1",
            "Host:csgosell.com",
            "Origin:https://csgosell.com",
            "Referer:https://csgosell.com/ru",
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            "X-Requested-With:XMLHttpRequest"
        ));
        $post_data = array (
            "stage" => "botAll",
            "steamId" => "76561198364873979",
            "hasBonus" => "false",
            "coins" => 0
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        dd(json_decode($curl_response));

    }

}
