<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use SeedlingsZO\PmsModule\PmsProductInfo;

class CountLibrary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:count-library';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计拥有商品的通用名、品牌数量';

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
        $this->comment(PHP_EOL . '--------统计拥有商品的通用名、品牌数量开始-------' . PHP_EOL);

        $pms = new PmsProductInfo();
        $pms->countBrandLibrary();
        $pms->countPronameLibrary();

        $this->comment(PHP_EOL . '--------统计拥有商品的通用名、品牌数量完成-------' . PHP_EOL);
    }
}
