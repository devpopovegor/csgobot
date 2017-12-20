<?php

namespace App\Http\Controllers;

use App\Classes\SumClass;
use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Site;
use App\Steam;
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




        set_time_limit(0);
        Steam::delete();
//        $item = Item::where('name', '=', '★ StatTrak™ Karambit | Marble Fade (Minimal Wear)')->get()->first();
//        Paintseed::where('name', '=', '★ StatTrak™ Karambit | Marble Fade(Minimal Wear)')->update([
//            'name' => '★ StatTrak™ Karambit | Marble Fade (Minimal Wear)',
//            'steam_id' => $item->id
//        ]);

        dd("SUCK MY DICK\r\nLICK MY ASS");

//        $this->set_Steams_task(4);

    }

    private function set_Steams_task($id)
    {


        $tasks = Task::with('item')->where('site_id', '=', $id)
            ->where('pattern','!=', '')->get();

        foreach ($tasks as $task){
            $arr = array_unique($task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray());
            $arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
            foreach ($arr as $item){
                Steam::create(['steam_id' => $item, 'task_id' => $task->id]);
            }
        }

        return dd('ok');
    }

}
