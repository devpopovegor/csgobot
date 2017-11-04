<?php

namespace App\Console\Commands;

use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Skintrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skintrade:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info('skintrade check');
        $site = Site::find(5);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $items = json_decode(curl_exec($curl));
        $items = array_merge($items->cooler613, $items->peter6364, $items->para6350,
            $items->erikli74,
            $items->etipuf257, $items->adobe1470, $items->baron1578, $items->katkat750);
        $items = collect($items);
        Log::info(count($items));

        $tasks = Task::with('item')->where('site_id', '=', 5)->get();
        foreach ($tasks as $task){
            $item = null;
            if ($task->float){
                $item = $items->where('m', '=', $task->item->full_name)
                    ->where('f', '<=', $task->float)->first();
            } else {
                $item = $items->where('m', '=', $task->item->full_name)->first();
            }

            if ($item){
                Telegram::sendMessage([
                    'chat_id' => $task->chat_id,
                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$item->f}"
                ]);
            }
        }

        Log::info('end check skintrade');
    }
}
