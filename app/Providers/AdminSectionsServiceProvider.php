<?php

namespace App\Providers;

use App\Dealer;
use App\Item;
use App\Paintseed;
use App\Pattern;
use App\Report;
use App\Site;
use App\Steam;
use App\Task;
use App\User;
use SleepingOwl\Admin\Providers\AdminSectionsServiceProvider as ServiceProvider;

class AdminSectionsServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $sections = [
        User::class => 'App\Admin\Sections\Users',
        Dealer::class => 'App\Admin\Sections\Dealers',
        Item::class => 'App\Admin\Sections\Items',
        Site::class => 'App\Admin\Sections\Sites',
        Task::class => 'App\Admin\Sections\Tasks',
//        Pattern::class => 'App\Admin\Sections\Patterns',
        Report::class => 'App\Admin\Sections\Reports',
        Paintseed::class => 'App\Admin\Sections\Paintseeds',
        Steam::class => 'App\Admin\Sections\Steams',
    ];

    /**
     * Register sections.
     *
     * @return void
     */
    public function boot(\SleepingOwl\Admin\Admin $admin)
    {
    	//

        parent::boot($admin);
    }
}
