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

//        dd("SUCK MY DICK\r\nLICK MY ASS");

//        $ps = Paintseed::where('name', '=', '★ StatTrak™ Bayonet | Marble Fade(Factory New)')
//        ->update(['name' => '★ StatTrak™ Bayonet | Marble Fade (Factory New)']);
//
//        dd('ok');

        $tasks = Task::with('item')->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->where('pattern', '!=', '')->get();
        echo count($tasks) . "\r\n";
//        $paintseeds = [];
//        foreach ($tasks as $task){
        $paintseeds = $tasks[0]->item->patterns->where('name', '=', $tasks[0]->pattern)->pluck('value')->toArray();
//            foreach ($paterns as $patern){
//                if (!in_array($patern, $paintseeds))
//                    $paintseeds[] = $patern;
//            }
//            dd($paintseeds);
//        }
        $paintseeds = array_unique($paintseeds);
        $steam_ids = DB::table('paintseeds')->where('name','=',$tasks[0]->item->name)->get();
        $steam_ids = $steam_ids->whereIn('value',$paintseeds)->toArray();
        dd(array_unique($paintseeds), $steam_ids, $tasks[0]->item->name);
    }
//

}
