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

        Paintseed::where('name', '=', 'AK-47 | Case Hardened (Battle-Scarred)')
            ->update(['steam_id' => '40']);
        Paintseed::where('name', '=', 'StatTrak™ AK-47 | Case Hardened (Minimal Wear)')
            ->update(['steam_id' => '42']);
        Paintseed::where('name', '=', 'StatTrak™ AK-47 | Case Hardened (Field-Tested)')
            ->update(['steam_id' => '43']);

        dd('ok');

//        $tasks = Task::with('item')->where('site_id', '=', '7')
//            ->where('client','=', 'ska4an')->get();
//
//        $result = [];
//        foreach ($tasks as $task){
//            $arr = [];
//            $arr['task'] = $task;
//            $arr['patterns'] = $task->item->patterns->pluck('value')->toArray();
//            $result[] = $arr;
//        }
//        dd($result[10]);
//        set_time_limit(0);

        $tasks = Task::with('item')->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->where('pattern', '!=', '')->get();
        echo count($tasks) . "\r\n";

        $paindseeds = [];
        $names = [];

        foreach ($tasks as $task){
            $paterns = $task->item->patterns->where('name', '=', $task->pattern);
            $names[] = $task->item->name;
            foreach ($paterns as $patern){
                $paindseeds[] = $patern->value;
            }
        }

        $paindseeds = array_unique($paindseeds);
        $steams = Paintseed::whereIn('value', $paindseeds)->get();
        $steams = $steams->whereIn('name', $names)->toArray();
        dd(json_encode($steams));

    }

}
