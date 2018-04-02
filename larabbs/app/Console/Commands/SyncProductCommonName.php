<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

class SyncProductCommonName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-product-common-name {--begin_at= : Whether the job should be queued}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步通用名数据到通用名表
                              --begin_at=2017-03-08
    ';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment(PHP_EOL . '--------药目通用名同步开始-------' . PHP_EOL);
        $begin_updated_at = $queueName = $this->option('begin_at') ? $this->option('begin_at') : Carbon::now()->subDay(2);
        $this->comment($begin_updated_at);
        \MiddleHandler::syncProductCommonName($begin_updated_at);
        $this->comment(PHP_EOL . '--------药目通用名同步完成-------' . PHP_EOL);
    }
}
