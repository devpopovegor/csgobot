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
        if ($item = Item::where('name', '=', $name)->first()){
            if (Task::where('item_id', '=', $item->id)->where('client','=','ska4an')
                ->where('site_id', '=', 7)->where('float', '=', $float)->first()){
                return "Search already exists";
            }
        }
        else {
            return "Item not exists";
        }


        return "OK";
    }

    public function getList()
    {
        $tasks = json_encode(Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get());

        return json_encode($tasks);
    }
}
