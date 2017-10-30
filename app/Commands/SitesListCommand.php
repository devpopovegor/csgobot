<?php
/**
 * Created by PhpStorm.
 * User: Егор
 * Date: 10.10.2017
 * Time: 10:38
 */

namespace App\Commands;

use App\Dealer;
use App\Site;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SitesListCommand extends Command
{

	protected $name = "sites_list";
	protected $description = "Выводит список сайтов на которых можно искать предметы";

	public function handle($arguments)
	{
		set_time_limit(0);
		$updades = Telegram::getWebhookUpdates();
		$message = $updades->getMessage();
		$user = $message->getFrom();

		$message = '';

		$dealer = Dealer::where('username', '=', $user->getUsername())->where('subscription', '=',1)->first();
		if ($dealer) {
			$now = Carbon::now()->format('Y-m-d');
			$end = $dealer->end_subscription;
			if ($now >= $end){
				$dealer->subscription = 0;
				$dealer->save();
				$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
				$this->replyWithMessage( [ 'text' => 'Продлите подписку' ] );
			} else {
				$message = $this->getListSites( Site::where( 'active', '=', 1 )->get() );
			}
		} else {
			$message = 'Купи подписку у @ska4an';
		}

		$this->replyWithChatAction(['action' => Actions::TYPING]);
		$this->replyWithMessage(['text' => $message]);
	}

	private function getListSites($arr)
	{
		$str = "";
		foreach ($arr as $item){
			$str .= "{$item->id}. {$item->url}\r\n";
		}
		return $str;
	}

}