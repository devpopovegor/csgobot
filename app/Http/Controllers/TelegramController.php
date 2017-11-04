<?php

namespace App\Http\Controllers;

use App\Item;
use App\Site;
use App\Task;
use Illuminate\Http\Request;
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

//        $name = explode(" (", "★ Bayonet | Autotronic (Battle-Scarred)");
//        dd($name);

//        $csmoney = Site::find(2);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://api.raffletrades.com/v1/inventory/");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = json_decode(curl_exec($curl));
        dd($curl_response->response);
    }

}
