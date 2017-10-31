<?php

namespace App\Http\Controllers;

use App\Item;
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
//        $db_item = Item::where('full_name','=', 'ads')->first();
//        $tasks = Task::where('item_id', '=', 1)->where('site_id', '=', 7)->get();
//        foreach ($tasks as $task) var_dump(123);
        var_dump(123);
    }

}
