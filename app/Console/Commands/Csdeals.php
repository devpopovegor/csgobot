<?php

namespace App\Console\Commands;

use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class Csdeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csdeals:check {site_id}';

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
	    Log::info('csdeals check');
	    $site = Site::find($site_id);
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $site->get_data);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_POST, true);
	    if ($site_id == 11) {
		    curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			    "Origin: https://ru.cs.deals",
			    "Referer: https://ru.cs.deals/",
			    "Connection: keep-alive",
			    "Content-Length: 0",
			    "Origin: https://ru.cs.deals",
			    "X-Requested-With: XMLHttpRequest",
			    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
			    "Referer: https://ru.cs.deals/",
			    "Accept: application/json, text/javascript, */*; q=0.01",
			    "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
			    "Authority: ru.cs.deals",
			    "Method: POST",
			    "Path: /ajax/botsinventory",
			    "Scheme: https"
		    ) );
	    } else {
		    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			    "Origin: https://ru.tradeskinsfast.com",
			    "Referer: https://ru.tradeskinsfast.com/",
			    "Connection: keep-alive",
			    "Content-Length: 0",
			    "X-Requested-With: XMLHttpRequest",
			    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
			    "Accept: application/json, text/javascript, */*; q=0.01",
			    "Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
			    "Authority: ru.tradeskinsfast.com",
			    "Method: POST",
			    "Path: /ajax/botsinventory",
			    "Scheme: https"
		    ));
	    }

	    $curl_response = curl_exec($curl);
	    $items = json_decode(utf8_decode($curl_response))->response;
	    Log::info(count($items));

	    $tasks = Task::with('item')->where('site_id', '=', $site_id)->get();

	    foreach ($tasks as $task){
		    $find = null;

		    foreach ($items as $item){
			    $item_name = $item->m;
			    if (is_numeric($item_name)) $item_name = $items[$item_name]->m;
			    if ($task->float){
				    if ($item_name == $task->item->full_name && $item->k < $task->float){
					    $find = $item;
					    break;
				    }
			    } else {
				    if ($item_name == $task->item->full_name){
					    $find = $item;
					    break;
				    }
			    }
		    }

		    if ($find) {
			    Telegram::sendMessage([
				    'chat_id' => $task->chat_id,
				    'text' => "{$task->item->name}\r\n{$site->url}\r\n{$task->item->phase}\r\n{$find->k}"
			    ]);
			    $task->delete();
		    }
	    }

    }
}
