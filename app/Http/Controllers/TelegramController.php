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
//        curl_setopt($curl, CURLOPT_URL,"https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/76561202255233023/+csgo_econ_action_preview%20S76561198360517576A12592785808D4801592215145461771");
        curl_setopt($curl, CURLOPT_URL,"https://skin.trade/load_all_bots_inventory");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt( $curl, CURLOPT_HEADER, true );
//        curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0");
//        curl_setopt ($curl, CURLOPT_COOKIE, "__cfduid=d2e8e0c7c573784d69c2679276b53d4c41503849628;cf_clearance=134cedabea468fe2b7c64ee37de7ee58f34ce95f-1509831532-900");
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($curl_response);
        dd($curl_response->cooler613);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//        dd($code);
        if ($code == '503') {
            sleep(4);
            $curl_response = curl_exec($curl);
        }
        dd($curl_response);
    }

}
