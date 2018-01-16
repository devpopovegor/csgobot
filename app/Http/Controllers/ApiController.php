<?php

namespace App\Http\Controllers;

use App\Dealer;
use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Report;
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

        $tasks = Task::with(['item', 'paintseeds:float'])
            ->where('site_id','=','7')
            ->where('client','ska4an')
            ->get()
            ->toArray();
        $json = str_replace('\u2605', '★', json_encode($tasks));
        $json = str_replace('\u2122', '™', $json);
        return $json;
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

    public function sendTelegram(){
        $client = $_GET['client'];
        $name = $_GET['name'];
        $float = $_GET['float'];
        $pattern = $_GET['pattern'];
        $metjm = $_GET['metjm'];

        $message = "{$name}\r\nhttps://cs.money/ru\r\n{$float}\r\n{$pattern}\r\n<a href='$metjm'>metjm</a>";


        $chat_id = $client == 'ska4an' ? 424791552 : 400699906;
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);

        Report::create([
            'item_id' => $_GET['item_id'],
            'site_id' => 7,
            'float' => $float,
            'pattern' => $pattern,
            'client' => $client,
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
