<?php

namespace App\Observers;

use App\Report;
use App\Task;
use Illuminate\Support\Facades\DB;

class TaskObserver
{

    public function created(Task $task)
    {
        $paintseeds = $task->item->paintseeds;
        if ($task->float) $paintseeds = $paintseeds->where('float', '<=', $task->float);
        if ($task->pattern) $paintseeds = $paintseeds->where('pattern_name', '=', $task->pattern);
        foreach ($paintseeds as $paintseed) {
            DB::insert('insert into paintseed_task (task_id, paintseed_id) values (?, ?)', [$task->id, $paintseed->id]);
        }
    }

    public function deleting(Task $task)
    {
//        Report::create([
//            'item_id' => $task->item_id,
//            'site_id' => $task->site_id,
//            'float' => $task->float,
//            'pattern' => $task->pattern,
//            'client' => $task->client,
//        ]);
        DB::delete('delete from paintseed_task where task_id = ?',[$task->id]);
    }

}