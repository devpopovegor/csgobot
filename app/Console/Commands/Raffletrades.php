<?php

namespace App\Console\Commands;

use App\Pattern;
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
        $items = collect($items->response);
        Log::info(count($items));

        $tasks = Task::with('item')->where('site_id', '=', $site_id)->get();
        foreach ($tasks as $task){
            $items = $items->where('market_name', '=', $task->item->name);
            if ($task->item->phase) $items->where('item_phase','=', $task->item->phase);

            if (count($items)){
                foreach ($items as $item){
                    $find = false;
                    $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link={$item->inspect_link}";
                    $inspectUrl = explode('%20', $item->inspect_link)[1];
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    $pattern = null;
                    $url_metjm = '';
                    $float = null;
                    if ($response->success) {
                        $pattern = $response->result->item_paintseed;
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                        $float = $response->result->item_floatvalue;
                    }

                    if ($task->float){
                        if ($float < $task->float){
                            if ($task->pattern) {
                                if (Pattern::where('name', '=', $task->pattern)
                                    ->where('value', '=', $pattern)->first()) {
                                    $find = true;
                                }
                            }
                            else $find = true;
                        }
                    }
                    else {
                        if ($task->pattern) {
                            if (Pattern::where('name', '=', $task->pattern)
                                ->where('value', '=', $pattern)->first()) {
                                $find = true;
                            }
                        }
                        else $find = true;
                    }

                    if ($find){
                        Telegram::sendMessage([
                            'chat_id' => $task->chat_id,
                            'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML'
                        ]);
                        $task->delete();
                        break;
                    }
                }
            }
        }

        Log::info('end check raffle');
    }
}
