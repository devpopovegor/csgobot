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

        $tasks = Task::with('item')->where('site_id', '=', 13)->get();
        foreach ($tasks as $task){
            $full_name = $task->item->full_name;
            if (strpos($task->item->full_name, '\'') !== false) $full_name = str_replace('\'', '%27', $task->item->full_name);
            $current_items = $items->where('h', '=', $full_name);
            if ($task->float) $current_items = $current_items->where('f', '<=', $task->float);
            if (count($current_items)){
                if ($task->pattern){
                    foreach ($current_items as $item){
                        $url_metjm = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/76561202255233023/+csgo_econ_action_preview%20S{$item->i}";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url_metjm);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response);
                        $pattern = null;
                        $link_metjm = '';
                        try {
                            if ($response->success) {
                                $pattern = $response->result->item_paintseed;
                                $link_metjm = "https://metjm.net/csgo/#S{$item->i}";
                            }
                        } catch (\Exception $exception) {
                            continue;
                        }
                        $patterns = $task->item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first();
                        if ($patterns) {
                            Telegram::sendMessage([
                                'chat_id' => $task->chat_id,
                                'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$item->f}\r\n{$task->pattern}\r\n<a href='$link_metjm'>metjm</a>",
                                'parse_mode' => 'HTML'
                            ]);
                            Report::create([
                                'item_id' => $task->item_id,
                                'site_id' => $task->site_id,
                                'float' => $task->float,
                                'pattern' => $task->pattern,
                                'client' => $task->client,
                            ]);
                            $task->delete();
                            break;
                        }
                    }
                }
                else {
                    $item = $current_items->first();
                    $link_metjm = "https://metjm.net/csgo/#S{$item->i}";
                    Telegram::sendMessage([
                        'chat_id' => $task->chat_id,
                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$item->f}\r\n<a href='$link_metjm'>metjm</a>",
                        'parse_mode' => 'HTML'
                    ]);
                    Report::create([
                        'item_id' => $task->item_id,
                        'site_id' => $task->site_id,
                        'float' => $task->float,
                        'pattern' => $task->pattern,
                        'client' => $task->client,
                    ]);
                    $task->delete();
                    break;
                }
            }
        }
        Log::info('csgosell end');
    }
}
