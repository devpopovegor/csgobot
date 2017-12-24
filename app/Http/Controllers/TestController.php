<?php

namespace App\Http\Controllers;

use App\Paintseed;
use App\Pattern;
use App\Steam;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
	public function set_steams_task($id)
	{

		dd($id);

		$tasks = Task::with('item')->where('site_id', '=', $id)
		             ->where('pattern','!=', '')->get();

		foreach ($tasks as $task){
			$arr = array_unique($task->item->patterns->where('name', '=', $task->pattern)->pluck('value')->toArray());
			$arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
			foreach ($arr as $item){
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
		foreach ($patterns as $pattern){
			$paintseeds = Paintseed::where('item_id', '=', $pattern->item_id)->where('value', '=', $pattern->value)->get();
			if (count($paintseeds)) {
				foreach ($paintseeds as $paintseed){
					$paintseed->pattern_name =  $pattern->name;
					$paintseed->save();
				}
				$pattern->delete();
			}
		}
	}

	public function get_patterns()
    {
        set_time_limit(0);
        $json = json_encode(Pattern::with('item')->get());
        $json = str_replace('\u2605', '★', $json);
        $json = str_replace('\u2122','™', $json);
        return $json;
    }

    public function delete_patterns()
    {
        DB::table('paintseeds')->delete();
    }

}