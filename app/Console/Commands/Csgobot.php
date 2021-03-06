<?php

namespace App\Console\Commands;

use App\Report;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Csgobot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csgobot:check';

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
        Log::info('csgobot check');
        $site = Site::find(14);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Host: www.thecsgobot.com",
            "Connection: keep-alive",
            "Content-Length: 7",
            "Accept: */*",
            "Origin: https://www.thecsgobot.com",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
            "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
            "Referer: https://www.thecsgobot.com/trading/",
            "Accept-Language: en-US,en;q=0.9",
            "Cookie: __cfduid=d55eded162019ba018906e0d894d5ea701511096482;
            JSESSIONID=6C5A1642AAF4F09151B09838EDBBF944;
            _ga=GA1.2.1308276030.1511096483;
            _gid=GA1.2.437421720.1511096483"
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, "who=bot");
        $curl_exec = curl_exec($curl);
        $items = collect(json_decode($curl_exec)->items);
        Log::info(count($items));

        $tasks = Task::with('item')->where('site_id', '=', 14)->get();
        foreach ($tasks as $task){
            $name = $task->item->name;
            if ($task->item->phase){
                if (strpos($name, '(') !== false){
                    $status = substr($name, strpos($name, '('));
                    $new_part = "({$task->item->phase}) {$status}";
                    $name = substr_replace($name, $new_part, strpos($name, '('));
                } else {
                    $name .= " ({$task->item->phase})";
                }
            }
            $current_items = $items->where('market_hash_name', '=', $name);
            if ($task->float) $current_items = $current_items->where('descriptions.0.aFloat', '<=', $task->float);
            if (count($current_items)){
                if ($task->pattern){
                    foreach ($current_items as $item){
                        $steam = explode('A', $item->descriptions[0]->inspectURL)[1];
                        $steam = explode('D', $steam)[0];
                        if (in_array($steam, $task->steams->pluck('steam_id')->toArray())){
                            $link_metjm = "https://metjm.net/csgo/#" . explode('%20', $item->descriptions[0]->inspectURL)[1];
                            $this->send_message($task, $site->url, $item->descriptions[0]->aFloat, $link_metjm);
                            break;
                        }
                    }
                }
                else {
                    $item = $current_items->first();
                    $link_metjm = "https://metjm.net/csgo/#" . explode('%20', $item->descriptions[0]->inspectURL)[1];
                    $this->send_message($task, $site->url, $item->descriptions[0]->aFloat, $link_metjm);
                    break;
                }
            }
        }

        Log::info('csgobot end');
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
        foreach ($task->steams as $steam){
            $steam->delete();
        }
        $task->delete();
    }
}
