<?php

namespace App\Console\Commands;

use App\Pattern;
use App\Report;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Raffletrades extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'raffle:check {site_id}';

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
        Log::info('skintrades check');
        $site = Site::find(3);
        $items_skintrades = json_decode(file_get_contents($site->get_data))->response;
        $items_skintrades = collect($items_skintrades);
        Log::info(count($items_skintrades));

        if (count($items_skintrades) > 0) { //проверка на то что cs.money вернула предметы
            $tasks = Task::with(['paintseeds:float', 'item'])->where('site_id', '=', 3)->get();
            foreach ($tasks as $task) { //перебор задач
                $items = $items_skintrades->where('market_name', '=', $task->item->full_name);
                if ($task->float) {
                    $item = $items->where('float', '<=', $task->float)->first();
                    if ($item) {
                        $metjm = "https://metjm.net/csgo/#S" . explode('%20',$item->inspect_link)[1];
                        $this->send_message($task, $site->url, $item->float, $metjm);
                        continue;
                    }
                } elseif ($task->pattern) {
                    $find = false;
                    foreach ($task->paintseeds as $paintseed) {
                        $float = round($paintseed->float, 17, PHP_ROUND_HALF_UP);
                        foreach ($items as $item) {
                            try {
                                if ($item->float == $float) {
                                    $metjm = "https://metjm.net/csgo/#S" . explode('%20',$item->inspect_link)[1];
                                    $this->send_message($task, $site->url, $item->float, $metjm);
                                    $find = true;
                                    break;
                                }
                            } catch (\Exception $ex) { Log::info('NO FLOAT'); }
                        }
                        if ($find) break;
                    }
                }
            }
        } else Log::info('SKINTRADES ERROR');
        Log::info('end check skintrades');
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
