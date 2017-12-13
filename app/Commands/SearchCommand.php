<?php

namespace App\Commands;

use App\Classes\NeedItem;
use App\Classes\SumClass;
use App\Dealer;
use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Site;
use App\Steam;
use App\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SearchCommand extends Command
{

    protected $name = "search";
    protected $description = "Поиск предмета. Для поиска предмета введи /search номер сайта,название предмета. Пример поиска предмета без фазы: /search 1,★ Karambit | Doppler (Factory New).\r\nПример поиска предмета с фазой: /search номер сайта,название сайта,фаза. Так же четвертым параметром можно добавить верхнюю границу float";

    public function handle($arguments)
    {
        set_time_limit(0);
        $updades = Telegram::getWebhookUpdates();
        $oMessage = $updades->getMessage();
        $user = $oMessage->getFrom();

        $message = '';

        $dealer = Dealer::where('username', '=', $user->getUsername())->where('subscription', '=', 1)->first();
        if ($dealer) {
            $now = Carbon::now()->format('Y-m-d');
            $end = $dealer->end_subscription;
            if ($now >= $end) {
                $dealer->subscription = 0;
                $dealer->save();
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                $this->replyWithMessage(['text' => 'Продлите подписку']);
            } else {

                $str_request = explode(',', $arguments);
                $count_params = count($str_request);
                if ($count_params == 2 || $count_params == 3 || $count_params == 4) {
                    $pattern_names = Pattern::groupBy('name')->pluck('name')->toArray();
//                    $pattern_items = collect(DB::select('select distinct items.name from items, patterns where items.id = patterns.item_id'));
                    $site = trim(explode(',', $arguments)[0]);
                    $item = trim(explode(',', $arguments)[1]);
                    $phase = false;
                    $float = null;
                    $pattern = null;

                    if ($count_params == 3) {
                        if (is_numeric(trim($str_request[2]))) {
                            $float = trim($str_request[2]);
                        } else {
                            if (in_array(trim(last($str_request)), $pattern_names)) $pattern = trim(last($str_request));
                            else $phase = trim($str_request[2]);
                        }
                    } elseif ($count_params == 4) {
                        if (is_numeric(trim(last($str_request))) && trim(last($str_request)) > 0) {
                            $float = trim(last($str_request));
                            if (in_array(trim($str_request[2]), $pattern_names)) $pattern = trim($str_request[2]);
                            else $phase = trim($str_request[2]);
                        } else {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => 'Неверный float']);
                        }
                    }

                    if ($mSite = Site::find($site)) {
                        $mItem = !$phase ? Item::where('name', '=', "{$item}")->first() : Item::where([
                            ['name', '=', "{$item}"],
                            ['phase', '=', "{$phase}"]
                        ])->first();
                        if ($mItem) {
                            if ($pattern && !count($mItem->patterns->where('name', '=', $pattern))) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => 'Поиск по паттерну для данного предмета невозможен']);
                            } else {
                                $tasks = Task::where('chat_id', '=', $oMessage->getChat()->getId())
                                    ->where('item_id', '=', $mItem->id)
                                    ->where('site_id', '=', $mSite->id)
                                    ->where('float', '=', $float)
                                    ->where('pattern', '=', $pattern)
                                    ->where('client', '=', $user->getUsername())->get();
                                if (!count($tasks)) {
                                    $message = "Поиск {$item} на сайте {$mSite->url} начался";
                                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                                    $this->replyWithMessage(['text' => $message]);
                                    //Логика поиска
                                    $obj = new NeedItem($mItem->name, $mSite->url, $oMessage->getChat()->getId(), $phase, $float, $pattern, $mItem->id);
                                    $curl = curl_init();
                                    $url = $mSite->get_data;
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                    $curl_response = null;
                                    if ($mSite->id == 8) {
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

                                    }
                                    elseif ($mSite->id == 11) {
                                        curl_setopt($curl, CURLOPT_POST, true);
                                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
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
                                        ));
                                        $curl_response = curl_exec($curl);
                                        $curl_response = json_decode(utf8_decode($curl_response))->response;
                                    }
                                    elseif ($mSite->id == 12) {
                                        curl_setopt($curl, CURLOPT_POST, true);
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
                                        $curl_response = curl_exec($curl);
                                        $curl_response = json_decode(utf8_decode($curl_response))->response;
                                    }
                                    elseif ($mSite->id == 9) {
                                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                            "Host: skinsjar.com",
                                            "Connection: keep-alive",
                                            "Upgrade-Insecure-Requests: 1",
                                            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36",
                                            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
                                            "Referer: https://skinsjar.com/",
                                            "Accept-Language: en-US,en;q=0.9",
                                            "Cookie: __cfduid=d0df6d8aa98ebfb3b13096c0859746ae71511002753; _ym_uid=1511002761162504861; _ym_isad=2; _ga=GA1.2.1049133680.1511002761; _gid=GA1.2.1258118144.1511002761; _ym_visorc_43477244=w; currentCurrencyCode=USD; intercom-id-f94tzf5i=c1047e04-5c0d-4a9c-a5c8-ac838bdb4ea1; cf_clearance=69cf861a9d3c1871a268efac87b3c32978442f02-1511005035-900"
                                        ));
                                        $curl_response = json_decode(curl_exec($curl));
                                    }
                                    elseif ($mSite->id == 13){
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
                                        $curl_response = json_decode(curl_exec($curl));
                                    }
                                    elseif ($mSite->id == 14){
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
                                        $curl_response = json_decode(curl_exec($curl))->items;
                                    }
                                    else {
                                        $curl_response = json_decode(curl_exec($curl));
                                    }
                                    $response = false;
                                    switch ($mSite->id) {
                                        case 1:
                                            $response = $this->check_raffletrades($obj, $curl_response);
                                            break;
                                        case 2:
                                            $response = $this->check_raffletrades($obj, $curl_response);
                                            break;
                                        case 3:
                                            $response = $this->check_raffletrades($obj, $curl_response);
                                            break;
                                        case 4:
                                            $response = $this->check_cstradegg($obj, $curl_response);
                                            break;
                                        case 5:
                                            $response = $this->check_skintrade($obj, $curl_response);
                                            break;
                                        case 7:
                                            $response = $this->check_csmoney($obj, $curl_response);
                                            break;
                                        case 8:
                                            $response = $this->check_csgosum($obj, $curl_response);
                                            break;
                                        case 9:
                                            $response = $this->check_skinsjar($obj, $curl_response);
                                            break;
                                        case 10:
                                            $response = $this->check_lootfarm($obj, $curl_response);
                                            break;
                                        case 11:
                                            $response = $this->check_csdeals($obj, $curl_response);
                                            break;
                                        case 12:
                                            $response = $this->check_csdeals($obj, $curl_response);
                                            break;
                                        case 13:
                                            $response = $this->check_csgosell($obj, $curl_response);
                                            break;
                                        case 14:
                                            $response = $this->check_csgobot($obj, $curl_response);
                                            break;
                                    }
                                    curl_close($curl);
                                    if (!$response) {
                                        $task = Task::create(['item_id' => $mItem->id, 'site_id' => $mSite->id,
                                            'float' => $float, 'chat_id' => $oMessage->getChat()->getId(),
                                            'pattern' => $pattern, 'client' => $user->getUsername()]);
                                        try{
                                        if ($pattern) {
                                            $arr = array_unique($task->item->patterns->where('name', '=', $pattern)->pluck('value')->toArray());
                                            $arr = $task->item->paintseeds->whereIn('value', $arr)->pluck('item_id')->toArray();
                                            foreach ($arr as $item) {
                                                Steam::create(['steam_id' => $item, 'task_id' => $task->id]);
                                            }
                                        }}catch (\Exception $exception){
                                            Log::info($exception->getMessage());
                                        }
                                    }
                                    //---------------------------
                                } else {
                                    $message = "Данный поиск уже существует";
                                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                                    $this->replyWithMessage(['text' => $message]);
                                }
                            }
                        } else {
                            $message = "Предмет {$item} не существует.";
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => $message]);
                        }
                    } else {
                        $message = "Сайт {$site} не существует.";
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => $message]);
                    }
                } else {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => 'Неправильный формат комманды']);
                }
            }

        } else {
            $message = 'Купи подписку у @ska4an';
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => $message]);
        }

    }

    private function is_pattern($item_id, $steam_id, $pattern_name){
	    $item = Item::find($item_id);
	    $patterns = array_unique($item->patterns->where('name', '=', $pattern_name)->pluck('value')->toArray());
//	    $steam_ids = DB::table('paintseeds')->whereIn('value',$patterns)->distinct()->pluck('item_id')->toArray();
	    $steam_ids = $item->paintseeds->whereIn('value',$patterns)->pluck('item_id')->toArray();
	    if (in_array($steam_id, $steam_ids)) return true;
	    return false;
    }

    private function send_message($name, $url, $phase, $float, $pattern, $metj){
	    $this->replyWithChatAction(['action' => Actions::TYPING]);
	    $this->replyWithMessage(['text' => "{$name}\r\n{$url}\r\n{$phase}\r\n{$float}\r\n{$pattern}\r\n<a href='$metj'>metjm</a>", 'parse_mode' => 'HTML']);
    }


    private function check_csmoney($obj, $curl_response)
    {
        $statuses = ['Factory New' =>  'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT', 'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];

	    $csmoney_items = collect($curl_response);

	    $name_parts = explode(' (', $obj->full_name);
	    $name = trim($name_parts[0]);
	    $status = count($name_parts) > 1 ? trim($statuses[str_replace(')', '', $name_parts[1])]) : null;


	    $items = $csmoney_items->where('m', '=', $name);
	    if ($status) $items = $items->where('e', '=', $status);
	    if ($obj->float) $items = $items->where('f.0', '<=', $obj->float);

	    if (count($items)){
		    if ($obj->pattern){
		    	foreach ($items as $item){
				    if ($this->is_pattern($obj->id, $item->id[0], $obj->pattern)){
					    $metjm = "https://metjm.net/csgo/#S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
					    $this->send_message($obj->name, $obj->url, $obj->phase, $item->f[0], $obj->pattern, $metjm);
					    return true;
				    }
			    }
		    }
		    else {
		    	$item = $items->first();
			    $metjm = "https://metjm.net/csgo/#S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
			    $this->send_message($obj->name, $obj->url, $obj->phase, $item->f[0], $obj->pattern, $metjm);
			    return true;
		    }
	    }

	    return false;
    }

    private function check_raffletrades($obj, $curl_response)
    {
        try {
            $curl_response = collect($curl_response->response);

            $items = $curl_response->where('custom_market_name', '=', $obj->full_name);
            if ($obj->float) $items = $items->where('float', '<=', $obj->float);

            if (count($items)) {
                if ($obj->pattern) {
                    foreach ($items as $item) {
	                    if ($this->is_pattern($obj->id, $item->id, $obj->pattern)){
		                    $inspectUrl = explode('%20', $item->inspect_link)[1];
		                    $metjm = "https://metjm.net/csgo/#{$inspectUrl}";
		                    $this->send_message($obj->name, $obj->url, $obj->phase, $item->float, $obj->pattern, $metjm);
		                    return true;
	                    }
                    }
                }
                else {
	                $item = $items->first();
	                $inspectUrl = explode('%20', $item->inspect_link)[1];
	                $metjm = "https://metjm.net/csgo/#{$inspectUrl}";
	                $this->send_message($obj->name, $obj->url, $obj->phase, $item->float, $obj->pattern, $metjm);
	                return true;
                }
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }

    }

    private function check_cstradegg($obj, $curl_response)
    {
        $curl_response = $curl_response->inventory;
        $items = collect($curl_response);
        $items = $items->where('market_hash_name', '=', $obj->full_name);
        if ($obj->float) $items = $items->where('wear', '<=', $obj->float);

        if (count($items)) {
            if ($obj->pattern) {
                foreach ($items as $item) {
                    if ($this->is_pattern($obj->id, $item->id, $obj->pattern)) {
                        $inspectUrl = explode('%20', $item->inspect_link)[1];
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                        $this->send_message($obj->name, $obj->url, $obj->phase, $item->wear, $obj->pattern, $url_metjm);
                        return true;
                    }
                }
            } else {
                $item = $items->first();
                $inspectUrl = explode('%20', $item->inspect_link)[1];
                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                $this->send_message($obj->name, $obj->url, $obj->phase, $item->wear, $obj->pattern, $url_metjm);
                return true;
            }
        }

        return false;
    }

    private function check_skintrade($obj, $curl_response)
    {
        $curl_response = array_merge($curl_response->cooler613, $curl_response->peter6364, $curl_response->para6350,
            $curl_response->erikli74,
            $curl_response->etipuf257, $curl_response->adobe1470, $curl_response->baron1578, $curl_response->katkat750);
        $find = false;
        if ($obj->float) {
            foreach ($curl_response as $item) {
                if ($item->m == $obj->full_name && $item->f <= $obj->float) {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->f}\r\n{$obj->phase}\r\n<a href='https://metjm.net/csgo/#{$item->i}'>metjm</a>",
                        'parse_mode' => 'HTML']);
                    $find = true;
                    break;
                }
            }
        } else {
            foreach ($curl_response as $item) {
                if ($item->m == $obj->full_name) {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->f}\r\n{$obj->phase}\r\n<a href='https://metjm.net/csgo/#{$item->i}'>metjm</a>",
                        'parse_mode' => 'HTML']);
                    $find = true;
                    break;
                }
            }
        }
        return $find;
    }

    private function check_csgosum($obj, $curl_response)
    {

        try {
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

            $items = $elements->where('name', '=', trim($obj->name));

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

                if ($obj->float && !$obj->pattern) {
                    if ($float <= $obj->float) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        return true;
                    }
                } elseif ($obj->pattern && !$obj->float) {
                    $p = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        return true;
                    }
                } elseif ($obj->pattern && $obj->float) {
                    $p = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        if ($float <= $obj->float) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }
                    }
                } else {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                        'parse_mode' => 'HTML']);
                    return true;
                }
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    private
    function check_skinsjar($obj, $curl_response)
    {
        try {
            $curl_response = collect($curl_response->items);
            $find_objs = null;
            $find_objs = $curl_response->where('name', '=', $obj->full_name);

            if (count($find_objs)) {
                if ($obj->pattern) {
                    foreach ($find_objs as $fo) {
                        $id = $fo->items[0]->id;
                        $inspectUrl = $fo->items[0]->inspectUrl;
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
                        try {
                            if ($response->success) {
                                $pattern = $response->result->item_paintseed;
                                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                                $float = $response->result->item_floatvalue;
                            }
                        } catch (\Exception $exception) {
                            continue;
                        }

                        if ($obj->float) {
                            if ($float <= $obj->float) {
                                $need_item = Item::find($obj->id);
                                $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                                if ($patterns) {
                                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                        'parse_mode' => 'HTML']);
                                    return true;
                                }
                            }
                        } else {
                            $need_item = Item::find($obj->id);
                            $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                            if ($patterns) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$float}\r\n{$obj->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML']);
                                return true;
                            }
                        }


                    }
                } else {
                    foreach ($find_objs as $fo) {
                        $id = $fo->items[0]->id;
                        $inspectUrl = $fo->items[0]->inspectUrl;
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
                        try {
                            if ($response->success) {
                                $pattern = $response->result->item_paintseed;
                                $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                                $float = $response->result->item_floatvalue;
                            }
                        } catch (\Exception $exception) {
                            continue;
                        }

                        if ($obj->float) {
                            if ($float <= $obj->float) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML']);
                                return true;
                            }
                        } else {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n{$pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }


                    }
                }
            }
            return false;
        }catch (\Exception $exception){
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => "Извините, временные неполадки на сервере",
//            $this->replyWithMessage(['text' => $exception->getMessage(),
                'parse_mode' => 'HTML']);
            return false;
        }
    }

    private
    function check_lootfarm($obj, $curl_response)
    {
        $items = collect($curl_response->result);
        $obj_name = str_replace('★ ', '', $obj->full_name);
        $pos = strpos($obj_name, ' (');
        $status = '';
        $statuses = ['Factory New' => 'FN', 'Minimal Wear' => 'MW', 'Field-Tested' => 'FT',
            'Battle-Scarred' => 'BS', 'Well-Worn' => 'WW'];
        if ($pos !== false) {
            $status = trim(substr($obj_name, $pos, strlen($obj_name)));
            $status = str_replace('(', '', $status);
            $status = str_replace(')', '', $status);
            $status = $statuses[$status];
            $obj_name = trim(substr($obj_name, 0, $pos));
        }
        $find_items = $items->where('n', '=', $obj_name);
        if ($status) $find_items = $find_items->where('e', '=', $status);
        $find_item = $find_items->first();

        if ($find_item) {
            foreach ($find_item->u as $item) {
                $metjm_link = 'https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=
                steam://rungame/730/76561202255233023/+csgo_econ_action_preview%20S76561198413200947';
                if ($item) {
                    foreach ($item as $item_u) {
//                        $item_u = array_first($item);
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
//                        continue;
                        }
                        if ($obj->float && !$obj->pattern) {
                            if ($float && $float <= $obj->float) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML']);
                                return true;
                            }
                        } elseif (!$obj->float && $obj->pattern) {
                            $need_item = Item::find($obj->id);
                            $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                            if ($patterns) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n{$obj->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML']);
                                return true;
                            }
                        } elseif ($obj->float && $obj->pattern) {
                            $need_item = Item::find($obj->id);
                            $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                            if ($float && $float <= $obj->float && $patterns) {
                                $this->replyWithChatAction(['action' => Actions::TYPING]);
                                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n{$obj->pattern}\r\n<a href='{$url_metjm}'>metjm</a>",
                                    'parse_mode' => 'HTML']);
                                return true;
                            }
                        } elseif (!$obj->float && !$obj->pattern) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$float}\r\n<a href='{$url_metjm}'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    private
    function check_csdeals($obj, $curl_response)
    {
        $find = null;
        $phases = [
            'Phase 1' => [418, 569],
            'Phase 2' => [419, 570],
            'Phase 3' => [420, 571],
            'Phase 4' => [421, 572],
            'Ruby' => [415],
            'Sapphire' => [416],
            'Black Pearl' => [417],
            'Emerald' => [568],
        ];

        foreach ($curl_response as $item) {
            $item_name = $item->m;
            if (is_numeric($item_name)) $item_name = $curl_response[$item_name]->m;
            if ($obj->float) {
                if ($item_name == $obj->name && $item->k < $obj->float) {
                    if ($obj->phase) {
                        if (in_array($item->g, $phases[$obj->phase])) {
                            if ($obj->pattern) {
                                $pat = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $item->p)->first();
                                if ($pat) {
                                    $obj->float = $item->k;
                                    $find = $obj;
                                    break;
                                }
                            } else {
                                $obj->float = $item->k;
                                $find = $obj;
                                break;
                            }
                        }
                    } else {
                        if ($obj->pattern) {
                            $pat = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $item->p)->first();
                            if ($pat) {
                                $obj->float = $item->k;
                                $find = $obj;
                                break;
                            }
                        } else {
                            $obj->float = $item->k;
                            $find = $obj;
                            break;
                        }
                    }
                }
            }
            else {
                if ($item_name == $obj->name) {
                    if ($obj->phase) {
                        if (in_array($item->g, $phases[$obj->phase])) {
                            if ($obj->pattern) {
                                $pat = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $item->p)->first();
                                if ($pat) {
                                    $obj->float = $item->k;
                                    $find = $obj;
                                    break;
                                }
                            } else {
                                $obj->float = $item->k;
                                $find = $obj;
                                break;
                            }
                        }
                    }
                    else {
                        if ($obj->pattern) {
                            $pat = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $item->p)->first();
                            if ($pat) {
                                $obj->float = $item->k;
                                $find = $obj;
                                break;
                            }
                        } else {
                            $obj->float = $item->k;
                            $find = $obj;
                            break;
                        }
                    }
                }
            }
        }

        if ($find) {
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$obj->pattern}\r\n{$find->float}"]);
        }

        return $find;
    }

    private function check_csgosell($obj, $curl_response){
        if (strpos($obj->full_name, '\'') !== false) $obj->full_name = str_replace('\'', '%27', $obj->full_name);
        $items = collect($curl_response);
        $items = $items->where('h', '=', $obj->full_name);
        if ($obj->float) $items = $items->where('f', '<=', $obj->float);
        if (count($items)){
            if ($obj->pattern){
                foreach ($items as $item){
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
                    $need_item = Item::find($obj->id);
                    $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                    if ($patterns) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->f}\r\n{$obj->pattern}\r\n<a href='{$link_metjm}'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        return true;
                    }
                }
            }
            else {
                $item = $items->first();
                $link_metjm = "https://metjm.net/csgo/#S{$item->i}";
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->f}\r\n<a href='{$link_metjm}'>metjm</a>",
                    'parse_mode' => 'HTML']);
                return true;
            }
        }
        return false;
    }

    private function check_csgobot($obj, $curl_response){
        $name = $obj->name;
        if ($obj->phase){
            if (strpos($name, '(') !== false){
                $status = substr($name, strpos($name, '('));
                $new_part = "({$obj->phase}) {$status}";
                $name = substr_replace($name, $new_part, strpos($name, '('));
            } else {
                $name .= " ({$obj->phase})";
            }
        }

        $items = collect($curl_response);
        $items = $items->where('market_hash_name', '=', $name);
        try {
            if ($obj->float) $items = $items->where('descriptions.0.aFloat', '<=', $obj->float);
            if (count($items)){
                if ($obj->pattern){
                    foreach ($items as $item){
                        $url_metj = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link={$item->descriptions[0]->inspectURL}";
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url_metj);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response);
                        $pattern = null;
                        $link_metjm = '';
                        try {
                            if ($response->success) {
                                $pattern = $response->result->item_paintseed;
                                $link_metjm = "https://metjm.net/csgo/#" . explode('%20', $item->descriptions[0]->inspectURL)[1];
                            }
                        } catch (\Exception $exception) {
                            continue;
                        }
                        $need_item = Item::find($obj->id);
                        $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                        if ($patterns) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->descriptions[0]->aFloat}\r\n{$obj->pattern}\r\n<a href='{$link_metjm}'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }
                    }
                }
                else {
                    $item = $items->first();
                    $metj = "https://metjm.net/csgo/#" . explode('%20', $item->descriptions[0]->inspectURL)[1];
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->descriptions[0]->aFloat}\r\n<a href='{$metj}'>metjm</a>",
                        'parse_mode' => 'HTML']);
                    return true;
                }
            }

        }catch (\Exception $exception){
            Log::info($exception->getMessage());
            return false;
        }
        return false;
    }

    private function getDataMetjm($item)
    {
        $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link={$item->inspect_link}";
        $inspectUrl = explode('%20', $item->inspect_link)[1];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        $pattern = null;
        $url_metjm = null;
        $float = null;
        try {
            $pattern = $response->result->item_paintseed;
            $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
            $float = $response->result->item_floatvalue;
        } catch (\Exception $exception) {
            $pattern = null;
            $float = null;
            $url_metjm = null;
        }
        return ['float' => $float, 'pattern' => $pattern, 'url_metjm' => $url_metjm];
    }
}