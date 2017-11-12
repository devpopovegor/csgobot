<?php

namespace App\Console\Commands;

use App\Item;
use Illuminate\Console\Command;

class ItemFullName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items_full_name:set';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting full name for items';

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
        echo "Setting start...";
        Item::chunk(500, function ($items) {
            foreach ($items as $item) {
                $full_name = $item->name;
                if (strpos($item->name, '(') !== false) {
                    $parts_name = explode('(', $item->name);
                    $full_name = trim($parts_name[0]);
                    $full_name .= trim($item->phase) . ' (';
                    $full_name .= trim($parts_name[1]);
                } else {
                    $full_name = trim("{$item->name} {$item->phase}");
                }
                // might be more logic here
                $item->update(['full_name' => $full_name]);
            }
        });
        echo "End";
    }
}
