<?php

namespace App\Console\Commands;

use App\Item;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CsMoney extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csmoney:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking csmoney items';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('csmoney check');
        $csmoney = Site::find(7);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $csmoney->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_exec = curl_exec($curl);
        $csmoney_items = collect(json_decode($curl_exec));
        Log::info(count($csmoney_items));
        if (count($csmoney_items) > 0) {
            $statuses = ['Factory New' => 'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT',
                'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];

            $tasks = Task::with('item')->where('site_id', '=', 7)->get();
            foreach ($tasks as $task) {
            	Log::info($task->item->full_name);
                $name_parts = explode(' (', $task->item->full_name);
                $name = trim($name_parts[0]);
                $status = count($name_parts) > 1 ? trim($statuses[str_replace(')', '', $name_parts[1])]) : null;
	            Log::info('name  = ' . $name);
	            Log::info('status  = ' . $status);
	            Log::info('chat  = ' . $task->chat_id);

	            $item = null;
                if ($task->float) {
	                Log::info('float = ' . $task->float);
                    if ($status) $item = $csmoney_items->where('m', '=', $name)->where('e', '=', $status)->where('f.0', '<=', $task->float)->first();
                    else $item = $csmoney_items->where('m', '=', $name)->where('f.0', '<=', $task->float)->first();
                }
                else {
	                Log::info('none float');
                    if ($status) {
                        Log::info('HAVE STATUS');
                        $item = $csmoney_items->where('m', '=', $name)->where('e', '=', $status)->first();
                    }
                    else $item = $csmoney_items->where('m', '=', $name)->first();
                }

                if ($item) {
	                Log::info('нашел ' . $item->m);
                    Telegram::sendMessage([
                        'chat_id' => $task->chat_id,
                        'text' => "{$task->item->name}\r\n{$csmoney->url}\r\n{$task->item->phase}\r\n{$item->f[0]}"
                    ]);
                    $task->delete();
                }
            }
        }
        else {
            Log::info($curl_exec);
        }

        Log::info('end check csmoney');

    }
}
