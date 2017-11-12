<?php

namespace App\Console\Commands;

use App\Classes\SumClass;
use App\Site;
use App\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Telegram\Bot\Actions;
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
        curl_setopt($curl, CURLOPT_URL,$csgosum->get_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Connection: keep-alive",
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
            "Accept: */*",
            "Accept-Language: en-US,en;q=0.8",
            "Host: www.csgosum.com",
            "Referer:https://www.csgosum.com/",
        ));
        $curl_response = curl_exec($curl);
        curl_close($curl);
        $tasks = Task::with('item')->where('site_id', '=', 8)->get();
        $crawler = new Crawler($curl_response);
        $elements = collect($crawler->filter('div.bot-results > div.inventory-item-hold')->each(function (Crawler $node, $i) {
            $item = new SumClass();
            $item->name = $node->attr('data-item-name');
            $item->name = utf8_decode($item->name);
            $item->cost = $node->attr('data-item-price');
            try {
                $item->inspect_link = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=" . trim(explode('">', explode('<a href="', $node->filter('label div.right-inspect')->first()->html())[1])[0]);
            } catch (\Exception $exception) {
                $item->inspect_link = null;
            }
            return $item;
        }));

        foreach ($tasks as $task){
            $items = $elements->where('name', '=', trim($task->item->name));
            foreach ($items as $item) {
                $inspectUrl = explode('%20', $item->inspect_link)[1];
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $item->inspect_link);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($curl);
                curl_close($curl);
                $response = json_decode($response);
                $pattern = $response->result->item_paintseed;
                $float = $response->result->item_floatvalue;
                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";

                if ($task->float && !$task->pattern) {
                    if ($float <= $task->float) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$task->item->name}\r\n{$csgosum->url}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        break;
                    }
                }
                elseif ($task->pattern && !$task->float) {
                    $p = $task->item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$task->item->name}\r\n{$csgosum->url}\r\n{$task->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        break;
                    }
                }
                elseif ($task->pattern && $task->float) {
                    $p = $task->item->patterns->where('name', '=', $task->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        if ($float <= $task->float) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$task->item->name}\r\n{$csgosum->url}\r\n{$task->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            break;
                        }
                    }
                }
                else {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$task->item->name}\r\n{$csgosum->url}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                        'parse_mode' => 'HTML']);
                    break;
                }
            }
        }

        Log::info('end check csgosum');

    }
}
