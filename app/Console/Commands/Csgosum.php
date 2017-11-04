<?php

namespace App\Console\Commands;

use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Csgosum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csgosum:check';

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

        Log::info('csgosum check');
        $csgosum = Site::find(8);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $tasks = Task::with('item')->where('site_id', '=', 8)->get();
        foreach ($tasks as $task) {
            curl_setopt($curl, CURLOPT_URL, $csgosum->get_data . str_replace(' ', '', $task->item->full_name));
            $csgosum_items = json_decode(curl_exec($curl));
            $csgosum_items = $csgosum_items[0]->response;

            foreach ($csgosum_items as $item) {
                if ($item->name == $task->item->full_name && $item->current > 0) {
                    Telegram::sendMessage([
                        'chat_id' => $task->chat_id,
                        'text' => "{$task->item->name}\r\n{$csgosum->url}\r\n{$task->item->phase}"
                    ]);
                    break;
                }
            }
        }
        Log::info('end check csgosum');

    }
}
