<?php

namespace App\Http\Controllers;

use App\Item;
use App\Task;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function addItem(Request $request)
    {
        $name = $request->item_name;
        $float = $request->item_float;

        if ($item = Item::where('name', '=', $name)->first()){
            if (Task::where('item_id', '=', $item->id)->where('client','=','ska4an')
                ->where('site_id', '=', 7)->where('float', '=', $float)->first()){
                return json_encode(['error' => 'Данный поиск уже существует', 'result' => null]);
            }
        }
        else {
            return json_encode(['error' => 'Предмет не существует', 'result' => null]);
        }

        return json_encode(['error' => null, 'result' => 'ok']);
    }

    public function getList()
    {
        $tasks = json_encode(Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get());

        return json_encode($tasks);
    }
}
