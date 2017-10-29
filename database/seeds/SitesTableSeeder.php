<?php

use Illuminate\Database\Seeder;

class SitesTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		$sites = [
			'https://www.raffletrades.com' => 'https://api.raffletrades.com/v1/inventory/',
			'https://www.ninjaswap.com' => 'https://api.ninjaswap.com/v1/inventory/',
			'https://www.skintrades.com' => 'https://api.skintrades.com/v1/inventory/',
			'https://cstrade.gg' => 'https://cstrade.gg/loadBotInventory',
			'https://skin.trade' => 'https://skin.trade/load_all_bots_inventory',
			'https://csoffer.me' => 'https://csoffer.me/load_all_bots_inventory',
			'https://turbotrades.gg' => '',
			'https://cs.money/ru' => 'https://cs.money/load_bots_inventory',
			'https://www.csgosum.com' => 'https://www.csgosum.com/api/status?search=',
			'https://skinsjar.com' => 'https://skinsjar.com/api/v3/load/bots?refresh=0&v=0',
			'https://loot.farm' => 'https://loot.farm/fullprice.json'
		];


		foreach ($sites as $key => $value){
			\App\Site::create(['url' => $key, 'get_data' => $value]);
		}

	}
}
