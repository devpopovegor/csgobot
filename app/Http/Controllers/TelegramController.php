<?php

namespace App\Http\Controllers;

use App\Classes\SumClass;
use App\Item;
use App\Paintseed;
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

        $tasks = Task::with('item')->where('pattern', '!=', null)->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->get();

        $paintseeds = [];
        foreach ($tasks as $task){
            $paterns = $task->item->patterns->where('name', '=', $task->pattern)->toArray();
            foreach ($paterns as $patern){
                $paintseeds[] = $patern['value'];

            }
        }
        $steam_ids = DB::table('paintseeds')->whereIn('value',$paintseeds)->pluck('item_id');

        dd($steam_ids);

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
            "Accept-Language: en-US,en;q=0.9",
            "Cookie: __cfduid=d55eded162019ba018906e0d894d5ea701511096482;
            JSESSIONID=6C5A1642AAF4F09151B09838EDBBF944;
            _ga=GA1.2.1308276030.1511096483;
            _gid=GA1.2.437421720.1511096483"
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, "who=bot");
        $curl_response = collect(json_decode(curl_exec($curl))->items);
        curl_close($curl);
        dd($curl_response);
//        dd($curl_response);

    }

}
