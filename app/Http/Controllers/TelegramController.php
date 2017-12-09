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
        $tasks = Task::with('item')->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->where('pattern', '!=', '')->get();
        echo count($tasks) . "\r\n";
        $paintseeds = [];
        foreach ($tasks as $task){
            $paterns = $task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray();
            foreach ($paterns as $patern){
//                if (!in_array($patern, $paintseeds))
                    $paintseeds[] = $patern;
            }
//            dd($paintseeds);
        }
//        dd($paintseeds);
        $steam_ids = DB::table('paintseeds')->whereIn('value',$paintseeds)->pluck('item_id', 'name')->toArray();
        dd($paintseeds, $steam_ids);
    }

	private function is_pattern($item_id, $steam_id, $pattern_name){

		$item = Item::find($item_id);
		$patterns = $item->patterns->where('name', '=', $pattern_name)->pluck('value')->toArray();
		$steam_ids = DB::table('paintseeds')->whereIn('value',$patterns)->distinct()->pluck('item_id')->toArray();
		if (in_array($steam_id, $steam_ids)) return true;
		return false;
	}

}
