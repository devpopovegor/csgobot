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
        curl_setopt($curl, CURLOPT_URL,"https://skinsjar.com/api/v3/load/bots?refresh=0&v=0");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
	    $csmoney_items = json_decode($curl_response);
        dd(collect($csmoney_items->items)->where('name','=', 'AWP | Medusa (Field-Tested)'));
//        dd(collect($csmoney_items->response)->where('m', '=', '★ Gut Knife | Gamma Doppler (Factory New)'));
//        dd($csmoney_items->response);

    }

}
