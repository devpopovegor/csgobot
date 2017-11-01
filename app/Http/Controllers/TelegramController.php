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

        $name = explode(" (", "★ Bayonet | Autotronic (Battle-Scarred)");
        dd($name);

        $csmoney = Site::find(8);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $csmoney->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = collect(json_decode(curl_exec($curl)));

        dd($curl_response->where('m', '=', '★ M9 Bayonet | Crimson Web')
            ->where('f.0', '<=', '0.07')->first());
    }

}
