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

        $obj_name = str_replace('★ ', '', '★ Bayonet | Marble Fade (Factory New)');
        $pos = strpos($obj_name, ' (');
        $status = '';
        $statuses = ['Factory New' => 'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT',
            'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];
        if ($pos !== false){
            $status = trim(substr($obj_name, $pos, strlen($obj_name)));
            $status = str_replace('(', '', $status);
            $status = str_replace(')', '', $status);
            $status = $statuses[$status];
            $obj_name = trim(substr($obj_name, 0, $pos));
        }
        dd($obj_name, $status);


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"https://loot.farm/botsInventory_new.json");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = json_decode($curl_response);
        dd(collect($curl_response->result)->where('n', '=', 'Karambit | Gamma Doppler Emerald'));
//        dd($curl_response);
        $i = 0;
        foreach (collect($curl_response->result) as $item){
            if ($i == 100) dd($item);
            $i++;
        }
//        dd(collect($curl_response->result)->first());

    }

}
