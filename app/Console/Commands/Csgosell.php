<?php

namespace App\Console\Commands;

use App\Report;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Csgosell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csgosell:check';

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
        Log::info('csgosell check');
        $site = Site::find(13);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Accept:*/*",
            "Accept-Language:en-US,en;q=0.9",
            "Connection:keep-alive",
            "Content-Length:61",
            "Content-Type:application/x-www-form-urlencoded; charset=UTF-8",
            "Cookie:__cfduid=d70de1f6eabeb188f99455a2b86f795301511086344; language=en; PHPSESSID=90a6e4178d3be74bb4fcc8d8778e0c9d; _ga=GA1.2.1372332778.1511086346; _gid=GA1.2.1835651314.1511086346; _gat=1",
            "Host:csgosell.com",
            "Origin:https://csgosell.com",
            "Referer:https://csgosell.com/ru",
            "User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            "X-Requested-With:XMLHttpRequest"
        ));
        $post_data = array (
            "stage" => "botAll",
            "steamId" => "76561198364873979",
            "hasBonus" => "false",
            "coins" => 0
        );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        $curl_exec = curl_exec($curl);
        $items = collect(json_decode($curl_exec));
        Log::info(count($items));

        $tasks = Task::with('item')->with('steams')->where('site_id', '=', 13)->get();
        foreach ($tasks as $task){
            $full_name = $task->item->full_name;
            if (strpos($task->item->full_name, '\'') !== false) $full_name = str_replace('\'', '%27', $task->item->full_name);
            $current_items = $items->where('h', '=', $full_name);
            if ($task->float) $current_items = $current_items->where('f', '<=', $task->float);

            if (count($current_items)){
                if ($task->pattern){
                    foreach ($current_items as $item){
                        $steam_id = explode('A', $item->i)[1];
                        $steam_id = explode('D', $steam_id)[0];
                        if (in_array($steam_id, $task->steams->pluck('steam_id')->toArray())){
                            $metjm = "https://metjm.net/csgo/#S{$item->i}";
                            $this->send_message($task, $site->url, $item->f, $metjm);
                            break;
                        }
                    }
                }
                else {
                    $item = $current_items->first();
                    $metjm = "https://metjm.net/csgo/#S{$item->i}";
                    $this->send_message($task, $site->url, $item->f, $metjm);
                }
            }
        }
        Log::info('csgosell end');
    }

    private function send_message($task, $url, $float, $metj){
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
//        foreach ($task->steams as $steam){
//            $steam->delete();
//        }
//        $task->delete();
    }

}
