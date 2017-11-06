<?php

namespace App\Commands;

use App\Classes\NeedItem;
use App\Dealer;
use App\Item;
use App\Site;
use App\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SearchCommand extends Command {

	protected $name = "search";
	protected $description = "Поиск предмета. Для поиска предмета введи /search номер сайта,название предмета. Пример поиска предмета без фазы: /search 1,★ Karambit | Doppler (Factory New).\r\nПример поиска предмета с фазой: /search номер сайта,название сайта,фаза. Так же четвертым параметром можно добавить верхнюю границу float";

	public function handle( $arguments ) {
		set_time_limit(0);
		$updades = Telegram::getWebhookUpdates();
		$oMessage = $updades->getMessage();
		$user    = $oMessage->getFrom();

		$message = '';

		$dealer = Dealer::where( 'username', '=', $user->getUsername() )->where( 'subscription', '=', 1 )->first();
		if ( $dealer ) {
			$now = Carbon::now()->format('Y-m-d');
			$end = $dealer->end_subscription;
			if ($now >= $end){
				$dealer->subscription = 0;
				$dealer->save();
				$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
				$this->replyWithMessage( [ 'text' => 'Продлите подписку' ] );
			} else {
				$str_request  = explode( ',', $arguments );
				$count_params = count( $str_request );
				if ( $count_params == 2 || $count_params == 3 || $count_params == 4) {

					$site  = trim( explode( ',', $arguments )[0] );
					$item  = trim( explode( ',', $arguments )[1] );
					$phase = false;
                    $float = null;
                    if ($count_params == 3){
					    if (is_numeric(trim($str_request[2]))){
					        $float = trim($str_request[2]);
                        } else {
					        $phase = trim($str_request[2]);
                        }
                    } elseif ($count_params == 4){
                        if (is_numeric(trim(last($str_request))) && trim(last($str_request)) > 0){
                            $float = trim(last($str_request));
                            $phase = trim($str_request[2]);
                        } else {
                            $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
						    $this->replyWithMessage( [ 'text' => 'Неверный float' ] );
                        }
                    }

					if ( $mSite = Site::find( $site ) ) {
						$mItem = ! $phase ? Item::where( 'name', '=', "{$item}" )->first() : Item::where( [
							[ 'name', '=', "{$item}" ],
							[ 'phase', '=', "{$phase}" ]
						] )->first();
						if ( $mItem ) {
							$message = "Поиск {$item} на сайте {$mSite->url} начался";
							$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
							$this->replyWithMessage( [ 'text' => $message ] );

							//Логика поиска
                            $obj = new NeedItem($mItem->name, $mSite->url, $oMessage->getChat()->getId(), $phase, $float);
                            $curl = curl_init();
                            $url = $mSite->id == 8 ? $mSite->get_data . str_replace(' ', '', $obj->full_name) : $mSite->get_data;
                            curl_setopt($curl, CURLOPT_URL, $url);
                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            $curl_response = json_decode(curl_exec($curl));
                            $response = false;
                            switch ($mSite->id){
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
                            }
                            curl_close($curl);
                            if (!$response) Task::create(['item_id' => $mItem->id, 'site_id' => $mSite->id, 'float' => $float, 'chat_id' => $oMessage->getChat()->getId()]);
                            //---------------------------

						} else {
							$message = "Предмет {$item} не существует.";
							$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
							$this->replyWithMessage( [ 'text' => $message ] );
						}
					} else {
						$message = "Сайт {$site} не существует.";
						$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
						$this->replyWithMessage( [ 'text' => $message ] );
					}
				} else {
					$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
					$this->replyWithMessage( [ 'text' => 'Неправильный формат комманды' ] );
				}
			}

		} else {
			$message = 'Купи подписку у @ska4an';
			$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
			$this->replyWithMessage( [ 'text' => $message ] );
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
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->f[0]}" ] );
                    $find = true;
                    break;
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
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
                    $find = true;
                    break;
                }
            }
        }

        return $find;
    }

    private function check_raffletrades($obj, $curl_response)
    {
        $curl_response = $curl_response->response;

        $find = false;
        if (!$obj->phase) {
            if ($obj->float) {
                foreach ($curl_response as $item) {
                    if ($item->market_name == $obj->name && $item->float <= $obj->float) {
                        $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                        $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->float}" ] );
                        $find = true;
                        break;
                    }
                }
            } else {
                foreach ($curl_response as $item) {
                    if ($item->market_name == $obj->name) {
                        $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                        $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
                        $find = true;
                        break;
                    }
                }
            }
        }
        else {
            if ($obj->float) {
                foreach ($curl_response as $item) {
                    if ($item->market_name == $obj->name && $item->item_phase == $obj->phase && $item->float <= $obj->name) {
                        $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                        $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}\r\n{$item->float}" ] );
                        $find = true;
                        break;
                    }
                }
            } else {
                foreach ($curl_response as $item) {
                    if ($item->market_name == $obj->name && $item->item_phase == $obj->phase) {
                        $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                        $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}" ] );
                        $find = true;
                        break;
                    }
                }
            }
        }

        return $find;

    }

    private function check_cstradegg($obj, $curl_response)
    {
        $curl_response = $curl_response->inventory;

        $find = false;
        foreach ($curl_response as $item) {
            if ($obj->float) {
                if ($item->market_hash_name == $obj->full_name && $item->wear <= $obj->float) {
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->float}\r\n{$obj->phase}" ] );
                    $find = true;
                    break;
                }
            } else {
                if ($item->market_hash_name == $obj->full_name) {
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
                    $find = true;
                    break;
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
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->float}\r\n{$obj->phase}" ] );
                    $find = true;
                    break;
                }
            }
        } else {
            foreach ($curl_response as $item) {
                if ($item->m == $obj->full_name) {
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
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
            $curl_response = $curl_response[0]->response;
            foreach ($curl_response as $item) {
                if ($item->name == $obj->full_name && $item->current > 0) {
                    $this->replyWithChatAction(['action' => Actions::TYPING]);
                    $this->replyWithMessage(['text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}"]);
                    $find = true;
                    break;
                }
            }
            $find = false;
            return $find;
        } catch (\Exception $exception){
            return false;
        }
    }

    private  function check_skinsjar($obj, $curl_response)
    {
        $curl_response = $curl_response->items;
        $find = false;
        if ($obj->float) {
            foreach ($curl_response as $item) {
                if ($item->name == $obj->full_name && $item->floatMax <= $obj->float) {
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$item->floatMax}\r\n{$obj->phase}" ] );
                    $find = true;
                }
            }
        } else {
            foreach ($curl_response as $item) {
                if ($item->name == $obj->full_name) {
                    $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                    $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
                    $find = true;
                }
            }
        }

        return $find;
    }

    private function check_lootfarm($obj, $curl_response)
    {
        $find = false;
        foreach ($curl_response as $item) {
            if ($item->name == $obj->full_name) {
                $this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
                $this->replyWithMessage( [ 'text' => "{$obj->name}\r\n{$obj->url}\r\n{$obj->phase}" ] );
                $find = true;
            }
        }

        return $find;
    }

}