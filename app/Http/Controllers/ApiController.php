<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function addItem()
    {

    }

    public function getList()
    {
        $tasks = Task::with('item')->where('site_id', '=', '7')
            ->where('client','=', 'ska4an')->get();

        return json_encode($tasks);
    }
}
