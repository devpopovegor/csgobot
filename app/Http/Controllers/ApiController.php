<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSkinsjar()
    {
        $skinsjar_task = Task::where('site_id','=','9')->get()->toArray();
        return json_encode($skinsjar_task);
    }
}
