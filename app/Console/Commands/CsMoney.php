<?php

namespace App\Console\Commands;


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

        $csmoney_items = collect(json_decode($curl_exec)); // получение предметов с cs.money

        Log::info(count($csmoney_items));

        if (count($csmoney_items) > 0) { //проверка на то что cs.money вернула предметы
            $items_id = $csmoney_items->pluck('id.0')->toArray();
            $tasks = Task::with('paintseeds')->where('site_id', '=', 7)->get();

            foreach ($tasks as $task) { //перебор задач
                $paintseeds = $task->paintseeds->pluck('steam')->toArray();
                $intersect = array_intersect($paintseeds, $items_id);
                if (count($intersect)) {
                    foreach ($intersect as $steam) {
                        $float = $task->paintseeds->where('steam', '=', $steam)->first()->float;
                        $csmoney_item = $csmoney_items->where('id.0', '=', $steam)->first();
                        $metjm = "https://metjm.net/csgo/#S{$csmoney_item->b[0]}A{$csmoney_item->id[0]}D{$csmoney_item->l[0]}";
                        $this->send_message($task, $csmoney->url, $float, $metjm);
                    }
                    $task->delete();
                }

            }
        } else {
            Log::info($curl_exec);
        }

        Log::info('end check csmoney');

    }

    private function send_message($task, $url, $float, $metj)
    {
        Telegram::sendMessage([
            'chat_id' => $task->chat_id,
            'text' => "{$task->item->name}\r\n{$url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='$metj'>metjm</a>",
            'parse_mode' => 'HTML'
        ]);
    }

}
