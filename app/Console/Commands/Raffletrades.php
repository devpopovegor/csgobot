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
        $site_id = $this->argument('site_id');
        Log::info('raffle check');
        $site = Site::find($site_id);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $items = json_decode(curl_exec($curl));
        $items_raffle = collect($items->response);
        Log::info(count($items_raffle));

        $tasks = Task::with('item')->with('steams')->where('site_id', '=', $site_id)->get();

        foreach ($tasks as $task) {

            $items = $items_raffle->where('custom_market_name', '=', $task->item->full_name);
            if ($task->float) $items = $items->where('float', '<=', $task->float);

            if (count($items)) {
                if ($task->pattern) {
                    foreach ($items as $item) {
                        if (in_array($item->id, $task->steams->pluck('steam_id')->toArray())) {
                            $inspectUrl = explode('%20', $item->inspect_link)[1];
                            $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                            $this->send_message($task, $site->url, $item->float, $url_metjm);
                            break;
                        }
                    }
                } else {
                    $item = $items->first();
                    $inspectUrl = explode('%20', $item->inspect_link)[1];
                    $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                    $this->send_message($task, $site->url, $item->float, $url_metjm);
                }
            }
        }

        Log::info('end check raffle');
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
            'float' => $task->float,
            'pattern' => $task->pattern,
            'client' => $task->client,
        ]);
        foreach ($task->steams as $steam) {
            $steam->delete();
        }
        $task->delete();
    }

}
