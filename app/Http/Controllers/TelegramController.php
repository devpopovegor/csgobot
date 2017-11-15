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
        curl_setopt($curl, CURLOPT_URL,"https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/12668280896/+csgo_econ_action_preview%2520S76561198410132220A12668280896D175253795608612215");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $curl_response = curl_exec($curl);
	    $curl_response = json_decode($curl_response);
        dd($curl_response);

    }

}
