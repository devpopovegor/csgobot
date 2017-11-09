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
        curl_setopt($curl, CURLOPT_URL,"https://ru.tradeskinsfast.com/ajax/botsinventory");
	    curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		    "Origin: https://ru.tradeskinsfast.com",
			"Referer: https://ru.tradeskinsfast.com/",
			"Connection: keep-alive",
			"Content-Length: 0",
			"X-Requested-With: XMLHttpRequest",
			"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
		    "Accept: application/json, text/javascript, */*; q=0.01",
			"Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
		    "Authority: ru.tradeskinsfast.com",
			"Method: POST",
	        "Path: /ajax/botsinventory",
			"Scheme: https"
	    ));
//        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt( $curl, CURLOPT_HEADER, true );
//        curl_setopt ($curl, CURLOPT_USERAGENT, "Mozilla/5.0");
        $curl_response = curl_exec($curl);
//        $curl_response = json_decode($curl_response);
        dd(json_decode(utf8_decode($curl_response)));
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//        dd($code);
        if ($code == '503') {
            sleep(4);
            $curl_response = curl_exec($curl);
        }
        dd($curl_response);
    }

}
