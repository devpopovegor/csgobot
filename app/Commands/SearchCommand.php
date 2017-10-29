<?php

namespace App\Commands;

use App\Classes\FindObject;
use App\Dealer;
use App\Item;
use App\Site;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
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
                    $float = 0;
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

							$object = new FindObject( $item, $mSite->get_data, $oMessage->getChat()->getId(), '', $float);
							if ( $phase ) {
								$object->phase = $phase;
							}

//						$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
//						$this->replyWithMessage( [ 'text' => '2' ] );

							fastcgi_finish_request();
							Artisan::call( $mSite->command, [ 'object' => $object ] );

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

}