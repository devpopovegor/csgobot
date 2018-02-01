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
        $response = json_decode(file_get_contents($site->get_data));
        $adobe1470 = collect($response->adobe1470);
        $peter6364 = collect($response->peter6364);
        $medusa1325 = collect($response->medusa1325);
        $para6350 = collect($response->para6350);
        $cooler613 = collect($response->cooler613);
        $first5025 = collect($response->first5025);
        $katkat750 = collect($response->katkat750);
        $erikli74 = collect($response->erikli74);
        $baron1578 = collect($response->baron1578);
        $etipuf257 = collect($response->etipuf257);

        $items_skintrade = $adobe1470->merge($peter6364)
            ->merge($medusa1325)
            ->merge($para6350)
            ->merge($cooler613)
            ->merge($first5025)
            ->merge($katkat750)
            ->merge($erikli74)
            ->merge($baron1578)
            ->merge($etipuf257);

        Log::info(count($items_skintrade));

        if (count($items_skintrade) > 0) { //проверка на то что cs.money вернула предметы
            $tasks = Task::with(['paintseeds:float', 'item'])->where('site_id', '=', 5)->get();
            foreach ($tasks as $task) { //перебор задач
                $items = $items_skintrade->where('m', '=', $task->item->full_name);
                if ($task->float) {
                    $item = $items->where('f', '<=', $task->float)->first();
                    if ($item) {
                        if ($item->f) {
                            $metjm = "https://metjm.net/csgo/#S" . $item->i;
                            $this->send_message($task, $site->url, $item->f, $metjm);
                            continue;
                        }
                    }
                } elseif ($task->pattern) {
                    $find = false;
                    foreach ($task->paintseeds as $paintseed) {
                        $float = round($paintseed->float, 16, PHP_ROUND_HALF_UP);
                        foreach ($items as $item) {
                            try {
                                if ($item->f == $float) {
                                    $metjm = "https://metjm.net/csgo/#" . $item->i;
                                    $this->send_message($task, $site->url, $item->f, $metjm);
                                    $find = true;
                                    break;
                                }
                            } catch (\Exception $ex) { Log::info('NO FLOAT'); }
                        }
                        if ($find) break;
                    }
                }
            }
        } else Log::info('SKINTRADE ERROR');
        Log::info('end check skintrade');
    }
}
