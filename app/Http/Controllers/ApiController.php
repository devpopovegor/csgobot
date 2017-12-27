<?php

namespace App\Http\Controllers;

use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ApiController extends Controller
{
    public function addItem()
    {
        $name = $_GET['name'];
        $phase = $_GET['phase'] ? $_GET['phase'] : '';
        $float = $_GET['float'];
        $pattern = $_GET['pattern'];
        $item = Item::where('name', '=', $name)->where('phase', '=', $phase)->first();
        if ($item){
            if (Task::where('item_id', '=', $item->id)->where('client','=','ska4an')
                ->where('site_id', '=', 7)->where('float', '=', $float)->where('pattern', '=', $pattern)->first()){
                return "Search already exists";
            }
        }
        else {
            return "Item not exists";
        }

        Task::create(['item_id' => $item->id, 'site_id' => 7, 'float' => $float, 'client' => 'ska4an', 'pattern' => $pattern, 'chat_id' => '']);
        return "OK";
    }

    public function getTasks()
    {
        set_time_limit(0);

        $tasks = Task::with(['paintseeds:float', 'item:full_name'])->where('site_id','=','7')->get()->toArray();
        dd($tasks[20]);
    }

    public function getPatterns()
    {
        return json_encode(Pattern::all());
    }

    public function send()
    {
    	$task = Task::with('item')->where('id', '=', $_GET['task_id'])->first();
    	$message = "Бот для cs.money отправил оффер на {$task->item->full_name}\r\nфлоат = {$task->float}\r\nпаттерн = {$task->pattern}";
	    Telegram::sendMessage([
		    'chat_id' => 222881167,
		    'text' => $message,
		    'parse_mode' => 'HTML'
	    ]);

	    return json_encode('ok');
    }

    public function getSteam()
    {
        $tasks = Task::with('item')->where('client', '=', 'ska4an')
            ->where('site_id', '=', '7')->where('pattern', '!=', '')->get();

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

        return json_encode($steams);

    }
}
