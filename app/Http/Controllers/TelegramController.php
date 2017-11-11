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
        curl_setopt($curl, CURLOPT_URL,"https://api.raffletrades.com/v1/inventory/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
	    $csmoney_items = collect(json_decode($curl_response)->response);
        dd($csmoney_items->where('custom_market_name', '=', '★ Karambit | Marble Fade (Factory New)'));

    }

}
