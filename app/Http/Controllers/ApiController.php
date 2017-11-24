<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSkinsjar()
    {
        $skinsjar_tasks = Task::with('item')->where('site_id','=','9')->get();
        return json_encode($skinsjar_tasks);
    }
}
