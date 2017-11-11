<?php

namespace App\Console\Commands;

use App\Pattern;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Laravel\Facades\Telegram;

class Skinsjar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skinsjar:check';

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
        Log::info('skinsjar check');
        $site = Site::find(9);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $site->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $items = json_decode(curl_exec($curl));
        try {
            $items = collect($items->items);
            Log::info(count($items));

            $tasks = Task::with('item')->where('site_id', '=', 9)->get();
            foreach ($tasks as $task) {
                $item = null;
                $item = $items->where('name', '=', $task->item->full_name);


                if (count($item)) {
                    if (!$task->pattern){
                        $item = $item->first();
                        $id = $item->items[0]->id;
                        $inspectUrl = $item->items[0]->inspectUrl;
                        $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/{$id}/+csgo_econ_action_preview%25{$inspectUrl}";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response);
                        $pattern = null;
                        $url_metjm = '';
                        $float = null;
                        if ($response->success){
                            $pattern = $response->result->item_paintseed;
                            $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                            $float = $response->result->item_floatvalue;
                        }

                        if ($task->float) {
                            if ($float <= $task->float) {
                                if (Pattern::where('name', '=', $task->pattern)
                                    ->where('value', '=', $pattern)->first()) {
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                        } else {
                            if (Pattern::where('name', '=', $task->pattern)
                                ->where('value', '=', $pattern)->first()) {
                                Telegram::sendMessage([
                                    'chat_id' => $task->chat_id,
                                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML'
                                ]);
                                $task->delete();
                                break;
                            }
                        }
                    }
                    else {
                        foreach ($item as $obj){
                            $id = $obj->items[0]->id;
                            $inspectUrl = $obj->items[0]->inspectUrl;
                            $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/{$id}/+csgo_econ_action_preview%25{$inspectUrl}";
                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($curl);
                            curl_close($curl);
                            $response = json_decode($response);
                            $pattern = null;
                            $url_metjm = '';
                            $float = null;
                            if ($response->success){
                                $pattern = $response->result->item_paintseed;
                                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                                $float = $response->result->item_floatvalue;
                            }

                            if ($task->float){
                                if ($float <= $task->float){
                                    Telegram::sendMessage([
                                        'chat_id' => $task->chat_id,
                                        'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML'
                                    ]);
                                    $task->delete();
                                    break;
                                }
                            }
                            else {
                                Telegram::sendMessage([
                                    'chat_id' => $task->chat_id,
                                    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML'
                                ]);
                                $task->delete();
                                break;
                            }
                        }
                    }
                }
            }
        }catch (\Exception $exception){
            Log::info('error');
        }

        Log::info('end check skinsjar');
    }
}
