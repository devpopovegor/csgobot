<?php

namespace App\Commands;

use App\Dealer;
use Carbon\Carbon;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{

	protected $name = "start";
	protected $description = "Старт";

	public function handle($arguments)
	{
		set_time_limit(0);
        $this->replyWithChatAction(['action' => Actions::TYPING]);
		$this->replyWithMessage(['text' => "123"]);


//		$updades = Telegram::getWebhookUpdates();
//		$message = $updades->getMessage();
//		$user = $message->getFrom();
//
//		$message = '';
//
//		$dealer = Dealer::where('username', '=', $user->getUsername())->where('subscription', '=',1)->first();
//
//		if ($dealer){
//			$now = Carbon::now()->format('Y-m-d');
//			$end = $dealer->end_subscription;
//			if ($now >= $end){
//				$dealer->subscription = 0;
//				$dealer->save();
//				$this->replyWithChatAction( [ 'action' => Actions::TYPING ] );
//				$this->replyWithMessage( [ 'text' => 'Продлите подписку' ] );
//			} else {
//				$message = "Привет, {$user->getFirstName()}.\r\nДля просмотра списка комманд введи комманду /help";
//			}
//		} else {
//			$message = 'Купи подписку у @ska4an';
//		}
//
//
//		$this->replyWithChatAction(['action' => Actions::TYPING]);
//		$this->replyWithMessage(['text' => $message]);
	}
}