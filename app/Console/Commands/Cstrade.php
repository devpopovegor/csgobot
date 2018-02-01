<?php

namespace App\Console\Commands;

use App\Item;
use App\Report;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Cstrade extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cstrade:check';

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
        Log::info('cstradegg check');
        $site = Site::find(4);
        $items_cstradegg = json_decode(file_get_contents($site->get_data))->inventory;
        $items_cstradegg = collect($items_cstradegg);
        Log::info(count($items_cstradegg));

        if (count($items_cstradegg) > 0) { //проверка на то что cs.money вернула предметы
            $tasks = Task::with(['paintseeds:float', 'item'])->where('site_id', '=', 4)->get();
            foreach ($tasks as $task) { //перебор задач
                $items = $items_cstradegg->where('market_hash_name', '=', $task->item->full_name);
                if ($task->float) {
                    $item = $items->where('wear', '<=', $task->float)->first();
                    if ($item) {
                        $metjm = "https://metjm.net/csgo/#S" . explode('%20',$item->inspect_link)[1];
                        $this->send_message($task, $site->url, $item->wear, $metjm);
                        continue;
                    }
                } elseif ($task->pattern) {
                    $find = false;
                    foreach ($task->paintseeds as $paintseed) {
                        $float = round($paintseed->float, 8, PHP_ROUND_HALF_UP);
                        foreach ($items as $item) {
                            try {
                                if ($item->wear == $float) {
                                    $metjm = "https://metjm.net/csgo/#S" . explode('%20',$item->inspect_link)[1];
                                    $this->send_message($task, $site->url, $item->wear, $metjm);
                                    $find = true;
                                    break;
                                }
                            } catch (\Exception $ex) { Log::info('NO FLOAT'); }
                        }
                        if ($find) break;
                    }
                }
            }
        } else Log::info('CSTRAGEGG ERROR');
        Log::info('end check cstradegg');
    }

    private function send_message($task, $url, $float, $metj)
    {
        Telegram::sendMessage([
            'chat_id' => $task->chat_id,
            'text' => "{$task->item->name}\r\n{$url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='$metj'>metjm</a>",
            'parse_mode' => 'HTML'
        ]);
        Report::create([
            'item_id' => $task->item_id,
            'site_id' => $task->site_id,
            'float' => $float,
            'pattern' => $task->pattern,
            'client' => $task->client,
        ]);
    }

}
