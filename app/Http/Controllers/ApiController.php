<?php

namespace App\Http\Controllers;

use App\Item;
use App\Pattern;
use App\Task;
use Illuminate\Http\Request;
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

    public function getList()
    {
        $tasks = json_encode(Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get());

        return json_encode($tasks);
    }

    public function getPatterns()
    {
        return json_encode(Pattern::all());
    }
}
