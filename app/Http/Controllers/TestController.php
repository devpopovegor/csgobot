<?php

namespace App\Http\Controllers;

use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Steam;
use App\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function set_steams_task($id)
    {

        dd($id);

        $tasks = Task::with('item')->where('site_id', '=', $id)
            ->where('pattern', '!=', '')->get();

        foreach ($tasks as $task) {
            $arr = array_unique($task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray());
            $arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
            foreach ($arr as $item) {
                Steam::create(['steam_id' => $item, 'task_id' => $task->id]);
            }
        }

        return 'ok';
    }

    public function set_patterns_name()
    {

//		dd(Paintseed::where('pattern_name', '!=', null)->distinct()->count());
        set_time_limit(0);
        $patterns = Pattern::all();
        foreach ($patterns as $pattern) {
            $paintseeds = Paintseed::where('item_id', '=', $pattern->item_id)->where('value', '=', $pattern->value)->get();
            if (count($paintseeds)) {
                foreach ($paintseeds as $paintseed) {
                    $paintseed->pattern_name = $pattern->name;
                    $paintseed->save();
                }
//				$pattern->delete();
            }
        }
    }

    public function get_patterns()
    {
        set_time_limit(0);
        $json = json_encode(Pattern::with('item')->get());
        $json = str_replace('\u2605', '★', $json);
        $json = str_replace('\u2122', '™', $json);
        return $json;
    }

    public function delete_patterns()
    {
        dd(2);
        DB::table('paintseeds')->delete();
    }

    public function set_patterns()
    {

        dd(1);
        set_time_limit(0);
        $patterns = json_decode('');
        foreach ($patterns as $pattern) {
            $item_id = Item::where('full_name', '=', $pattern->item_name)->first();
            if ($item_id) {
                $item_id = $item_id->id;
                $val = '1001';
                try {
                    $val = $pattern->value;
                } catch (\Exception $exception) {
                }
                Paintseed::create(['item_id' => $item_id, 'value' => $val,
                    'steam' => $pattern->steam, 'float' => $pattern->float, 'pattern_name' => null]);
            }
        }

        return 'OK';
    }

    public function get_tasks($site_id, $username)
    {
        set_time_limit(0);
        $tasks = Task::with('item.paintseeds')->where('site_id', '=', $site_id)
            ->where('client', '=', $username)->get();

        $result = [];
        foreach ($tasks as $task) {
            $paintseeds = $task->item->paintseeds;
            if ($task->float) $paintseeds = $paintseeds->where('float', '<=', $task->float);
            if ($task->pattern) $paintseeds = $paintseeds->where('pattern_name', '=', $task->pattern);
            $paintseeds = $paintseeds->pluck('steam')->toArray();
            $result[$task->id] = $paintseeds;
        }

        dd($result);

//        return json_encode($result);
    }

    public function get_items()
    {
        $tasks = Task::whereHas('paintseeds', function($query){
			//$query->where('float', '0.021840905770659447');
			$query->where('value', '12822894553');
		})->get();
		dd($tasks);
    }

    public function insert_paintseed_task($site_id)
    {
        set_time_limit(0);
        $tasks = Task::with('item.paintseeds')->where('site_id', '=', $site_id)->get();
        foreach ($tasks as $task) {
            $paintseeds = $task->item->paintseeds;
            if ($task->float) $paintseeds = $paintseeds->where('float', '<=', $task->float);
            if ($task->pattern) $paintseeds = $paintseeds->where('pattern_name', '=', $task->pattern);
            foreach ($paintseeds as $paintseed) {
                DB::insert('insert into paintseed_task (task_id, paintseed_id) values (?, ?)', [$task->id, $paintseed->id]);
            }
        }
        dd(213);
    }

    public function delete_paintseed_task($site_id)
    {
        set_time_limit(0);
        $tasks = Task::where('site_id', '=', $site_id)->get();
        foreach ($tasks as $task) {
            DB::delete('delete from paintseed_task where task_id = ?',[$task->id]);
        }
        dd(213);
    }

    public function delete_user_tasks($username)
    {
        Task::where('client', '=',$username)->delete();
    }



}