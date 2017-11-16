<?php

namespace App\Console\Commands;

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
        Log::info('cstrade check');
        $site = Site::find(4);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $items = json_decode(curl_exec($curl));
        $items = collect($items->inventory);
        Log::info(count($items));

        $tasks = Task::with('item')->where('site_id', '=', 4)->get();
        foreach ($tasks as $task){
//            $item = null;
//            if ($task->float){
//                $item = $items->where('market_hash_name', '=', $task->item->full_name)
//                    ->where('wear', '<=', $task->float);
//            } else {
                $itemss = $items->where('market_hash_name', '=', $task->item->full_name);
//            }

            if (count($itemss)){
                foreach ($itemss as $obj) {
                    $float = null;
                    $data_metjm = null;
                    $url_metjm = null;
                    try {
                        $float = $obj->wear;
                    } catch (\Exception $exception) {
                        $data_metjm = $this->getDataMetjm($obj, 'float,pattern');
                        $float = $data_metjm['float'];
                    }
                    if ($task->float) {
                        if ($float && $float <= $task->float) {
                            if (!$data_metjm) $data_metjm = $this->getDataMetjm($obj, 'pattern');
                            $pattern = $data_metjm['pattern'];
                            $url_metjm = $data_metjm['metjm'];
                            if ($task->pattern) {
                                if ($task->item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first()) {
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                            else {
                                Telegram::sendMessage([
                                    'chat_id' => $task->chat_id,
                                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML'
                                ]);
                                $task->delete();
                                break;
                            }
                        }
                    }
                    else {
                        if (!$data_metjm) $data_metjm = $this->getDataMetjm($obj, 'pattern');
                        $pattern = $data_metjm['pattern'];
                        $url_metjm = $data_metjm['metjm'];
                        if ($task->pattern) {
                            if ($task->item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first()) {
                                Telegram::sendMessage([
                                    'chat_id' => $task->chat_id,
                                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML'
                                ]);
                                $task->delete();
                                break;
                            }
                        } else {
                            Telegram::sendMessage([
                                'chat_id' => $task->chat_id,
                                'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                'parse_mode' => 'HTML'
                            ]);
                            $task->delete();
                            break;
                        }
                    }
                }
            }
        }

        Log::info('end check cstrade');
    }

    private function getDataMetjm($item, $get){
        $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link={$item->inspect_link}";
        $inspectUrl = explode('%20', $item->inspect_link)[1];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        $response = json_decode($response);
        $pattern = null;
        $url_metjm = '';
        $float = null;
        if ($response->success) {
            try {
                $pattern = $response->result->item_paintseed;
                $float = $response->result->item_floatvalue;
                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
            }catch (\Exception $exception){
                Log::info('Failed pattern');
            }
        }
        $return = [];
        $return['metjm'] = $url_metjm;
        if ($get == 'float') $return['float'] = $float;
        if ($get == 'pattern') $return['pattern'] = $pattern;
        if ($get == 'float,pattern') {
            $return['float'] = $float;
            $return['pattern'] = $pattern;
        }
        return $return;
    }
}
