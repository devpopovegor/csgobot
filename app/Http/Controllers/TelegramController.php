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
	    curl_setopt($curl, CURLOPT_URL, 'https://api.raffletrades.com/v1/inventory/');
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    $items = json_decode(curl_exec($curl));
	    $items = collect($items->response);
	    dd($items);
    }

	private function is_pattern($item_id, $steam_id, $pattern_name){

		$item = Item::find($item_id);
		$patterns = $item->patterns->where('name', '=', $pattern_name)->pluck('value')->toArray();
		$steam_ids = DB::table('paintseeds')->whereIn('value',$patterns)->distinct()->pluck('item_id')->toArray();
		if (in_array($steam_id, $steam_ids)) return true;
		return false;
	}

}
