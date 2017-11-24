<?php

namespace App\Http\Controllers;

use App\Item;
use App\Task;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function addItem()
    {
        $name = $_GET['name'];
        $float = $_GET['float'];
        $item = Item::where('name', '=', $name)->first();
        if ($item){
            if (Task::where('item_id', '=', $item->id)->where('client','=','ska4an')
                ->where('site_id', '=', 7)->where('float', '=', $float)->first()){
                return "Search already exists";
            }
        }
        else {
            return "Item not exists";
        }

        Task::create(['item_id' => $item->id, 'site_id' => 7, 'float' => $float, 'client' => 'ska4an', 'pattern' => '']);
        return "OK";
    }

    public function getList()
    {
        $tasks = json_encode(Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get());

        return json_encode($tasks);
    }
}
