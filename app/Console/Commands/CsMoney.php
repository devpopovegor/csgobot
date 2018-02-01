<?php

namespace App\Console\Commands;


use App\Report;
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
            $tasks = Task::with(['paintseeds:float', 'item'])->where('site_id', '=', 7)->get();
            foreach ($tasks as $task) { //перебор задач

                $items = $csmoney_items->where('m', '=', $task->item->shot_name)
                    ->where('e', '=', $task->item->status);

                if ($task->float) {
                    $item = $items->where('f.0', '<=', $task->float)->first();
                    if ($item) {
                        $metjm = "https://metjm.net/csgo/#S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                        $this->send_message($task, $csmoney->url, $item->f[0], $metjm);
                        continue;
                    }
                } elseif ($task->pattern) {
                    $find = false;
                    foreach ($task->paintseeds as $paintseed) {
                        $float = round($paintseed->float, 8, PHP_ROUND_HALF_UP);
                        foreach ($items as $item) {
                            try {
                                if ($item->f[0] == $float) {
                                    $metjm = "https://metjm.net/csgo/#S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                                    $this->send_message($task, $csmoney->url, $item->f[0], $metjm);
                                    $find = true;
                                    break;
                                }
                            } catch (\Exception $ex) { Log::info('NO FLOAT'); }
                        }
                        if ($find) break;
                    }
                }
            }
        } else {
            Log::info('MANY ERROR');
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

        Report::create([
            'item_id' => $task->item_id,
            'site_id' => $task->site_id,
            'float' => $float,
            'pattern' => $task->pattern,
            'client' => $task->client,
        ]);
    }

}
