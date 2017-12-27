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
        echo Carbon::now() . "</br>";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://dev.csgo.trade/load_bots_inventory');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_exec = curl_exec($curl);

        $csmoney_items = collect(json_decode($curl_exec));
        echo Carbon::now() . "</br>";
        if (count($csmoney_items) > 0) { //проверка на то что cs.money вернула предметы
            $statuses = ['Factory New' => 'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT', 'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];
            $tasks = Task::with(['paintseeds', 'item'])->where('site_id', '=', 7)->get();

            echo Carbon::now() . "</br>";
            foreach ($tasks as $task) { //перебор задач
                $name_parts = explode(' (', $task->item->full_name);
                $name = trim($name_parts[0]);
                $status = count($name_parts) > 1 ? trim($statuses[str_replace(')', '', $name_parts[1])]) : null;

                $items = $csmoney_items->where('m', '=', $name)->where('e', '=', $status);
                if ($task->float) $items = $items->where('f.0', '<=', $task->float);
                elseif ($task->pattern) {
                    foreach ($task->paintseeds as $paintseed){
                        $float =  round($paintseed->float,8,PHP_ROUND_HALF_UP);
                        foreach ($items as $item){
                            if ($item->f[0] == $float){
                                dd(1);
                            }
                        }
                    }
                }
            }
        }

//        $items_id = $items->pluck('id.0')->toArray();

        dd(Carbon::now());
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