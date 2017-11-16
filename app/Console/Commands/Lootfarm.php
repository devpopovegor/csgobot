<?php

namespace App\Console\Commands;

use App\Item;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Lootfarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lootfarm:check';

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
        Log::info('lootfarm check');
        $site = Site::find(10);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $items = json_decode(curl_exec($curl));
        try {
            $items = collect($items->result);
        Log::info(count($items));

        $statuses = ['Factory New' => 'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT',
            'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];
        $tasks = Task::with('item')->where('site_id', '=', 10)->get();
        foreach ($tasks as $task){
            $obj_name = str_replace('★ ', '', $task->full_name);
            $pos = strpos($obj_name, ' (');
            $status = '';
            if ($pos !== false){
                $status = trim(substr($obj_name, $pos, strlen($obj_name)));
                $status = str_replace('(', '', $status);
                $status = str_replace(')', '', $status);
                $status = $statuses[$status];
                $obj_name = trim(substr($obj_name, 0, $pos));
            }

            $find_items = $items->where('n', '=', $obj_name);
            if ($status) $find_items = $find_items->where('e','=', $status);
            $find_item = $find_items->first();
            if ($find_item) {
                foreach ($find_item->u as $item) {
                    $metjm_link = 'https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/76561202255233023/+csgo_econ_action_preview%20S76561198413200947';
                    if ($item) {
                        foreach ($item as $item_u) {
//                            $item_u = array_first($item);
                            $metjm_link .= "A{$item_u->id}";
                            $metjm_link .= $item_u->l;
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $metjm_link);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $response = json_decode($response);
                            $pattern = null;
                            $url_metjm = "";
                            $float = null;
                            try {
                                if ($response->success) {
                                    $pattern = $response->result->item_paintseed;
                                    $url_metjm = "https://metjm.net/csgo/#S76561198413200947A{$item_u->id}{$item_u->l}";
                                    $float = $response->result->item_floatvalue;
                                }
                            } catch (\Exception $exception) {
                                $float = $item_u->f / 100000;
//                            continue;
                            }
                            if ($task->float && !$task->pattern) {
                                if ($float && $float <= $task->float) {
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                            elseif (!$task->float && $task->pattern) {
                                $need_item = Item::find($task->id);
                                $patterns = $need_item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first();
                                if ($patterns) {
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                            elseif ($task->float && $task->pattern) {
                                $need_item = Item::find($task->id);
                                $patterns = $need_item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first();
                                if ($float && $float <= $task->float && $patterns) {
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$task->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                            elseif (!$task->float && !$task->pattern) {
                                Log::info('AAAAAA');
                                Telegram::sendMessage([
                                    'chat_id' => $task->chat_id,
                                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML'
                                ]);
                                $task->delete();
                                break;
                            }
                        }
                    }
                }
            }
        }
        }catch (\Exception $exception){
            Log::info($exception->getMessage());
        }
        Log::info('end check lootfarm');
    }
}
