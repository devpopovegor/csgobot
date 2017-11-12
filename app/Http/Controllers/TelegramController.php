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
        curl_setopt($curl, CURLOPT_URL,"https://www.csgosum.com/ajax/bots.php?_=1510500047448");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Connection: keep-alive",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
            "Accept: */*",
            "Accept-Language: en-US,en;q=0.8",
            "Host: www.csgosum.com",
            "Referer:https://www.csgosum.com/",
        ));
        $curl_response = curl_exec($curl);
        $crawler = new Crawler($curl_response);
        $elements = collect($crawler->filter('div.bot-results > div.inventory-item-hold')->each(function (Crawler $node, $i) {
            $item = new SumClass();
            $item->name = $node->attr('data-item-name');
            $item->cost = $node->attr('data-item-price');
            try {
                $item->inspect_link = trim(explode('">', explode('<a href="', $node->filter('label div.right-inspect')->first()->html())[1])[0]);
            }catch (\Exception $exception){
                $item->inspect_link = null;
            }
            return $item;
        }));
//	    $csmoney_items = json_decode($curl_response);
        dd($elements);

    }

}
