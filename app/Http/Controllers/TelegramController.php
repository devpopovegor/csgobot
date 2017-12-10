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

//        $ps = Paintseed::where('name', '=', '★ Karambit | Marble Fade(Minimal Wear)')
//        ->update(['name' => '★ Karambit | Marble Fade (Minimal Wear)']);
//
//        dd('ok');

        $tasks = Task::with('item')->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->where('pattern', '!=', '')->get();
        echo count($tasks) . "\r\n";

        $objects = [];
        $paindseeds = [];

        foreach ($tasks as $task){
            $paterns = $task->item->patterns->where('name', '=', $task->pattern);
            foreach ($paterns as $patern){
                $object = [];
                $object['paintseed'] = $patern->value;
                $object['name'] = $task->item->name;
                $objects[] = $object;
                $paindseeds[] = $patern->value;
            }
        }

        $paindseeds = array_unique($paindseeds);

        $steams = Paintseed::whereIn('value', $paindseeds)->get()->toArray();

        dd($steams,$objects);

    }

}
