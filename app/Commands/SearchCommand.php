<?php

namespace App\Commands;

use App\Classes\NeedItem;
use App\Classes\SumClass;
use App\Dealer;
use App\Item;
use App\Pattern;
use App\Site;
use App\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
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

                                } elseif ($mSite->id == 11) {
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
                                } elseif ($mSite->id == 12) {
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
                                } else {
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
                                }
                                curl_close($curl);
                                if (!$response) Task::create(['item_id' => $mItem->id, 'site_id' => $mSite->id,
                                    'float' => $float, 'chat_id' => $oMessage->getChat()->getId(), 'pattern' => $pattern]);
                                //---------------------------
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

    private function check_csmoney($obj, $curl_response)
    {
        $statuses = ['FN' => '(Factory New)', 'MW' => '(Minimal Wear)', 'FT' => '(Field-Tested)',
            'BS' => '(Battle-Scarred)', 'WW' => '(Well-Worn)'];

        $find = false;
        if ($obj->float) {
            foreach ($curl_response as $item) {
                $item_name = '';
                try {
                    $item_name = $item->m . " {$statuses[$item->e]}";
                } catch (\Exception $exception) {
                    $item_name = $item->m;
                }
                if ($item_name == $obj->full_name && $item->f[0] <= $obj->float) {
                    $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/{$item->b[0]}/+csgo_econ_action_preview%20S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                    $inspectUrl = "S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    $pattern = null;
                    $url_metjm = '';
                    try {
                        if ($response->success) {
                            $pattern = $response->result->item_paintseed;
                            $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                        }
                    } catch (\Exception $exception) {
                        continue;
                    }

                    if (!$obj->pattern) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->f[0]}\r\n{$pattern}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        $find = true;
                        break;
                    } else {
                        if (Item::where('full_name', '=', $obj->full_name)->first()
                            ->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first()) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->float}\r\n{$obj->pattern}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            $find = true;
                            break;
                        }
                    }
                }
            }
        } else {
            foreach ($curl_response as $item) {
                $item_name = '';
                try {
                    $item_name = $item->m . " {$statuses[$item->e]}";
                } catch (\Exception $exception) {
                    $item_name = $item->m;
                }
                if ($item_name == $obj->full_name) {
                    $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=steam://rungame/730/{$item->b[0]}/+csgo_econ_action_preview%20S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                    $inspectUrl = "S{$item->b[0]}A{$item->id[0]}D{$item->l[0]}";
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $response = json_decode($response);
                    $pattern = null;
                    $url_metjm = '';
                    try {
                        if ($response->success) {
                            $pattern = $response->result->item_paintseed;
                            $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                        }
                    } catch (\Exception $exception) {
                        continue;
                    }

                    if (!$obj->pattern) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->f[0]}\r\npattern index = {$pattern}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        $find = true;
                        break;
                    } else {
                        $need_item = Item::find($obj->id);
                        $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                        if ($patterns) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->f[0]}\r\n{$obj->pattern}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            $find = true;
                            break;
                        }
                    }
                }
            }
        }

        return $find;
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
                        $url = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link={$item->inspect_link}";
                        $inspectUrl = explode('%20', $item->inspect_link)[1];
                        $curl = curl_init();
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $response = json_decode($response);
                        $pattern = $response->result->item_paintseed;
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                        $need_item = Item::find($obj->id);
                        $patterns = $need_item->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                        if ($patterns) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->float}\r\n{$obj->pattern}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }
                    }
                } else {
                    $item = $items->first();
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
                    if ($response->success) {
                        $pattern = $response->result->item_paintseed;
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                    }
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->float}\r\n<a href='$url_metjm'>metjm</a>",
                        'parse_mode' => 'HTML']);
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

        $find = false;
        foreach ($curl_response as $item) {
            if ($obj->float) {
                if ($item->market_hash_name == $obj->full_name && $item->wear <= $obj->float) {
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
                    if ($response->success) {
                        $pattern = $response->result->item_paintseed;
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                    }

                    if ($obj->pattern) {
                        if (Pattern::where('name', '=', $obj->pattern)
                            ->where('value', '=', $pattern)->first()) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->float}\r\n{$obj->pattern}\r\n{$url_metjm}"]);
                            $find = true;
                            break;
                        }
                    } else {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->float}\r\npattern index = {$pattern}\r\n{$url_metjm}"]);
                        $find = true;
                        break;
                    }
                }
            } else {
                if ($item->market_hash_name == $obj->full_name) {
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
                    if ($response->success) {
                        $pattern = $response->result->item_paintseed;
                        $url_metjm = "https://metjm.net/csgo/#{$inspectUrl}";
                    }

                    if ($obj->pattern) {
                        if (Pattern::where('name', '=', $obj->pattern)
                            ->where('value', '=', $pattern)->first()) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->wear}\r\n{$obj->pattern}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            $find = true;
                            break;
                        }
                    } else {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->wear}\r\npattern index = {$pattern}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        $find = true;
                        break;
                    }
                }
            }
        }

        return $find;
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
                $item->cost = $node->attr('data-item-price');
                try {
                    $item->inspect_link = "https://metjm.net/shared/screenshots-v5.php?cmd=request_new_link&inspect_link=" . trim(explode('">', explode('<a href="', $node->filter('label div.right-inspect')->first()->html())[1])[0]);
                } catch (\Exception $exception) {
                    $item->inspect_link = null;
                }
                return $item;
            }));

            $items = $elements->where('name', '=', trim($obj->name));
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => count($items),
                'parse_mode' => 'HTML']);

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
                }
                elseif ($obj->pattern && !$obj->float) {
                    $p = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        $this->replyWithChatAction(['action' => Actions::TYPING]);
                        $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                            'parse_mode' => 'HTML']);
                        return true;
                    }
                }
                elseif ($obj->pattern && $obj->float) {
                    $p = Item::find($obj->id)->patterns->where('name', '=', $obj->pattern)->where('value', '=', $pattern)->first();
                    if ($p) {
                        if ($float <= $obj->float) {
                            $this->replyWithChatAction(['action' => Actions::TYPING]);
                            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->pattern}\r\n{$float}\r\n<a href='$url_metjm'>metjm</a>",
                                'parse_mode' => 'HTML']);
                            return true;
                        }
                    }
                }
                else {
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
    }

    private
    function check_lootfarm($obj, $curl_response)
    {
        $find = false;
        foreach ($curl_response as $item) {
            if ($item->name == $obj->full_name) {
                $this->replyWithChatAction(['action' => Actions::TYPING]);
                $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}"]);
                $find = true;
            }
        }

        return $find;
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
            'Ruby' => [],
            'Sapphire' => [],
            'Black Perl' => [],
            'Emerald' => [],
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
            } else {
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
        }

        if ($find) {
            $this->replyWithChatAction(['action' => Actions::TYPING]);
            $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$obj->pattern}\r\n{$find->float}"]);
        }

        return $find;
    }

}