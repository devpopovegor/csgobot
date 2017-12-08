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
	    $item = Item::find(15);
	    $patterns = $item->patterns->where('name', '=', 'FI')->pluck('value')->toArray();
	    dd($patterns);
    }

	private function is_pattern($item_id, $steam_id, $pattern_name){

		$item = Item::find($item_id);
		$patterns = $item->patterns->where('name', '=', $pattern_name)->pluck('value')->toArray();
		$steam_ids = DB::table('paintseeds')->whereIn('value',$patterns)->distinct()->pluck('item_id')->toArray();
		if (in_array($steam_id, $steam_ids)) return true;
		return false;
	}

}
