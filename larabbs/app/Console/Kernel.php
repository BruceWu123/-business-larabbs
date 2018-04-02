<?php

namespace App\Console;

use App\Console\Commands\CountLibrary;
use App\Console\Commands\MakeBrandCommonNameDict;
use App\Console\Commands\SyncProductCommonName;
use App\Console\Commands\UpdateAskMenu;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        SyncProductCommonName::class,
        UpdateAskMenu::class,
        CountLibrary::class,
        MakeBrandCommonNameDict::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //同步通用名
        $schedule->command('command:sync-product-common-name')->dailyAt('00:00');
        // 更新问答首页菜单排序
        $schedule->command('command:update-ask-menu')->dailyAt('02:00');
        // 生成品牌和通用名词典
        $schedule->command('command:make-brand-common-name-dict')->dailyAt('04:00');
    }
}
